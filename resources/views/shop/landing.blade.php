@extends('layouts.shop')

@section('title', 'Pasarin — Belanja Kebutuhan Harian')

@section('content')
<section class="hero">
    <div>
        <span class="hero-tag">Diantar hari ini</span>
        <h1>Belanja pasar,<br>tanpa keluar rumah.</h1>
        <p>Sembako, minuman, dan camilan pilihan dari kios terpercaya — pesan sekarang, bayar tunai saat barang tiba.</p>
    </div>
    <div class="hero-art">
        <img src="https://loremflickr.com/600/400/traditional-market,vegetables?lock=101" alt="Pasar tradisional" loading="lazy">
    </div>
</section>

<div class="chip-row" id="category-chips">
    <span class="chip active" data-category-id="">Semua</span>
    <!-- kategori lain akan ditambahkan otomatis via JS dari GET /api/categories -->
</div>

<div class="stall-grid" id="product-grid">
    <!--
        Diisi via fetch ke GET /api/products (lihat public/js/app.js).
        Contoh markup 1 kartu produk (di-generate dinamis oleh JS):
    -->
    <div class="stall-card">
        <div class="price-ribbon">Rp 15.000</div>
        <div class="thumb">foto produk (dummy)</div>
        <div class="body">
            <div class="name">Gula Pasir 1kg</div>
            <div class="meta">Stok: 80</div>
            <div class="qty-row">
                <button type="button">-</button>
                <span>1</span>
                <button type="button">+</button>
                <button type="button" class="btn-add">+ Keranjang</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Ambil produk sungguhan dari API saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof loadProducts === 'function') loadProducts();
    });
</script>
@endsection
