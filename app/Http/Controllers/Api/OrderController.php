<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Checkout oleh customer. Body:
     * { "items": [ { "product_id": 1, "qty": 2 }, ... ] }
     * Metode pembayaran cash (satu-satunya opsi sesuai ketentuan).
     */
    public function checkout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty'        => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        try {
            $order = DB::transaction(function () use ($request) {
                $total = 0;
                $orderItems = [];

                // Lock baris produk agar tidak race-condition saat stok berkurang
                foreach ($request->items as $item) {
                    $product = Product::where('id', $item['product_id'])->lockForUpdate()->first();

                    if ($product->stock < $item['qty']) {
                        abort(422, "Stok tidak cukup untuk produk: {$product->name}");
                    }

                    $subtotal = $product->price * $item['qty'];
                    $total += $subtotal;

                    $orderItems[] = [
                        'product'  => $product,
                        'qty'      => $item['qty'],
                        'subtotal' => $subtotal,
                    ];
                }

                $order = Order::create([
                    'order_code'     => 'ORD-' . strtoupper(Str::random(8)),
                    'user_id'        => auth()->id(),
                    'total_amount'   => $total,
                    'payment_method' => 'cash',
                    'status'         => 'pending', // sesuai ketentuan, order masuk dengan status Pending
                ]);

                foreach ($orderItems as $oi) {
                    OrderDetail::create([
                        'order_id'     => $order->id,
                        'product_id'   => $oi['product']->id,
                        'product_name' => $oi['product']->name,
                        'qty'          => $oi['qty'],
                        'price'        => $oi['product']->price,
                        'subtotal'     => $oi['subtotal'],
                    ]);

                    // stok otomatis berkurang
                    $oi['product']->decrement('stock', $oi['qty']);
                }

                return $order;
            });

            return response()->json([
                'message' => 'Checkout berhasil, order menunggu diproses',
                'data'    => $order->load('details'),
            ], 201);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /** Customer: riwayat order milik sendiri */
    public function myOrders(Request $request)
    {
        $orders = Order::with('details')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($orders);
    }

    /** Admin only: laporan penjualan */
    public function report(Request $request)
    {
        $query = Order::with(['details', 'user:id,name,email']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(15);

        // Hanya order yang sudah dikonfirmasi admin (status completed) yang dihitung
        // sebagai pendapatan & barang terjual. Order pending tidak ikut terhitung.
        $summary = [
            'total_transaksi'  => Order::count(),
            'total_pendapatan' => Order::where('status', 'completed')->sum('total_amount'),
            'order_pending'    => Order::where('status', 'pending')->count(),
            'total_stok'       => Product::sum('stock'), // stok fisik saat ini, selalu real-time
            'total_terjual'    => OrderDetail::whereHas('order', fn ($q) => $q->where('status', 'completed'))->sum('qty'),
        ];

        // Produk terlaris (untuk chart), hanya dari order yang sudah completed
        $topProducts = OrderDetail::select('product_name', DB::raw('SUM(qty) as total_qty'))
            ->whereHas('order', fn ($q) => $q->where('status', 'completed'))
            ->groupBy('product_name')
            ->orderByDesc('total_qty')
            ->limit(6)
            ->get();

        return response()->json([
            'summary'      => $summary,
            'top_products' => $topProducts,
            'orders'       => $orders,
        ]);
    }

    /** Admin only: update status order, misal pending -> completed */
    public function updateStatus(Request $request, Order $order)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,paid,completed,cancelled',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        $order->update(['status' => $request->status]);

        return response()->json(['message' => 'Status order berhasil diupdate', 'data' => $order]);
    }
}
