@extends('layouts.admin')

@section('title', 'Laporan Penjualan — Admin Pasarin')

@section('content')
<h1 class="page-title">Laporan penjualan</h1>
<p class="page-sub">Ringkasan transaksi toko secara keseluruhan.</p>

<div class="stat-bento">
    <div class="stat-card">
        <div class="label">Total Pendapatan</div>
        <div class="value" id="stat-revenue">Rp 0</div>
    </div>
    <div class="stat-card">
        <div class="label">Barang Terjual</div>
        <div class="value" id="stat-sold">0</div>
    </div>
    <div class="stat-card">
        <div class="label">Sisa Stok (semua barang)</div>
        <div class="value" id="stat-stock">0</div>
    </div>
    <div class="stat-card">
        <div class="label">Order Pending</div>
        <div class="value" id="stat-pending">0</div>
    </div>
</div>
<p class="page-sub" style="margin-top:-14px;">Total Pendapatan & Barang Terjual hanya menghitung order yang sudah <b>dikonfirmasi</b>.</p>

<h3 style="margin:24px 0 10px; font-size:16px;">Produk terlaris (dari order terkonfirmasi)</h3>
<div id="top-products-chart" style="background:var(--white); border:1px solid var(--line); border-radius:var(--radius); padding:18px;">
    <p style="color:var(--ink-soft); font-size:13px;">Memuat data...</p>
</div>

<h3 style="margin:28px 0 10px; font-size:16px;">Daftar order</h3>
<table class="data-table" id="orders-table">
    <thead>
        <tr><th>Kode Order</th><th>Pelanggan</th><th>Total</th><th>Status</th><th>Tanggal</th><th>Aksi</th></tr>
    </thead>
    <tbody id="orders-body">
        <!-- Diisi via fetch ke GET /api/report/sales -->
    </tbody>
</table>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof loadSalesReport === 'function') loadSalesReport();
    });
</script>
@endsection
