@extends('layouts.shop')

@section('title', 'Keranjang — Pasarin')

@section('content')
<h1 class="page-title" style="margin-top:24px;">Keranjang belanja</h1>
<p class="page-sub">Periksa kembali pesanan sebelum checkout. Pembayaran dilakukan tunai saat barang tiba.</p>

<table class="cart-table" id="cart-table">
    <thead>
        <tr><th>Produk</th><th>Harga</th><th>Qty</th><th>Subtotal</th><th></th></tr>
    </thead>
    <tbody id="cart-body">
        <!-- Diisi via JS dari localStorage keranjang -->
    </tbody>
</table>

<div class="summary-box">
    <div class="row"><span>Total item</span><span id="cart-total-item">0</span></div>
    <div class="row total"><span>Total bayar</span><span id="cart-total-amount">Rp 0</span></div>
    <button class="btn-primary" style="margin-top:16px;" id="checkout-btn">Checkout (Bayar Tunai)</button>
    <div id="checkout-msg" style="margin-top:10px; font-size:13px;"></div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof renderCart === 'function') renderCart();
    });
    document.getElementById('checkout-btn').addEventListener('click', async function () {
        if (typeof doCheckout === 'function') await doCheckout();
    });
</script>
@endsection
