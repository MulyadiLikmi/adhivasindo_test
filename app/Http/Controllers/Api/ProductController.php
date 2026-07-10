<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * List barang (public, bisa diakses tanpa login) dengan fitur search & filter kategori.
     */
    public function index(Request $request)
    {
        $products = Product::with('category')
            ->search($request->query('q'))
            ->when($request->query('category_id'), fn($q, $catId) => $q->where('category_id', $catId))
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return response()->json($products);
    }

    public function show(Product $product)
    {
        $product->load('category');
        return response()->json($product);
    }

    /** Admin only */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'nullable|exists:categories,id',
            'name'        => 'required|string|max:150',
            'description' => 'nullable|string',
            'price'       => 'required|integer|min:0',
            'stock'       => 'required|integer|min:0',
            'image'       => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        $product = Product::create($validator->validated());

        return response()->json(['message' => 'Barang berhasil ditambahkan', 'data' => $product], 201);
    }

    /** Admin only */
    public function update(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'nullable|exists:categories,id',
            'name'        => 'sometimes|required|string|max:150',
            'description' => 'nullable|string',
            'price'       => 'sometimes|required|integer|min:0',
            'stock'       => 'sometimes|required|integer|min:0',
            'image'       => 'nullable|string',
            'is_active'   => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        $product->update($validator->validated());

        return response()->json(['message' => 'Barang berhasil diupdate', 'data' => $product]);
    }

    /** Admin only */
    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json(['message' => 'Barang berhasil dihapus']);
    }
}
