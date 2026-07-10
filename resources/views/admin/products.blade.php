@extends('layouts.admin')

@section('title', 'Kelola Barang — Admin Pasarin')

@section('content')
<div style="display:flex; justify-content:space-between; align-items:center;">
    <div>
        <h1 class="page-title">Kelola barang</h1>
        <p class="page-sub">Tambah, ubah, atau hapus barang yang dijual di toko.</p>
    </div>
    <button class="btn-primary" style="width:auto; padding:10px 18px;" id="add-product-btn">+ Tambah Barang</button>
</div>

<table class="data-table" id="products-table">
    <thead>
        <tr><th>Nama</th><th>Kategori</th><th>Harga</th><th>Stok</th><th>Status</th><th>Aksi</th></tr>
    </thead>
    <tbody id="products-body">
        <!-- Diisi via fetch ke GET /api/products -->
    </tbody>
</table>

<!-- Modal sederhana tambah/edit barang -->
<div id="product-modal" style="display:none; position:fixed; inset:0; background:rgba(22,36,27,.5); align-items:center; justify-content:center;">
    <div style="background:var(--paper); padding:26px; border-radius:12px; width:360px;">
        <h3 id="modal-title" style="margin-bottom:14px;">Tambah Barang</h3>
        <form id="product-form">
            <input type="hidden" name="id">
            <label style="font-size:13px; font-weight:700;">Nama barang</label>
            <input type="text" name="name" required style="width:100%; padding:9px; margin:6px 0 10px; border-radius:6px; border:1px solid var(--line);">
            <label style="font-size:13px; font-weight:700;">Kategori</label>
            <select name="category_id" id="product-category-select" style="width:100%; padding:9px; margin:6px 0 10px; border-radius:6px; border:1px solid var(--line); background:var(--white);">
                <option value="">Tanpa kategori</option>
                <!-- Diisi via fetch ke GET /api/categories -->
            </select>
            <label style="font-size:13px; font-weight:700;">Harga (Rp)</label>
            <input type="number" name="price" required style="width:100%; padding:9px; margin:6px 0 10px; border-radius:6px; border:1px solid var(--line);">
            <label style="font-size:13px; font-weight:700;">Stok</label>
            <input type="number" name="stock" required style="width:100%; padding:9px; margin:6px 0 14px; border-radius:6px; border:1px solid var(--line);">
            <div style="display:flex; gap:8px;">
                <button type="button" id="cancel-modal" style="flex:1; padding:10px; border-radius:6px; border:1px solid var(--line); background:var(--white);">Batal</button>
                <button type="submit" class="btn-primary" style="margin:0; flex:1;">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof loadAdminProducts === 'function') loadAdminProducts();
    });
</script>
@endsection
