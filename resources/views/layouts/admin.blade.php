<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin — Pasarin')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<div class="admin-shell">
    <div class="admin-main">
        @yield('content')
    </div>
    <aside class="admin-side">
        <div class="logo">Pasar<span style="color:var(--paper)">in</span></div>
        <nav>
            <a href="{{ url('/admin/dashboard') }}" class="{{ request()->is('admin/dashboard') ? 'active' : '' }}">📊 Laporan Penjualan</a>
            <a href="{{ url('/admin/products') }}" class="{{ request()->is('admin/products') ? 'active' : '' }}">📦 Kelola Barang</a>
            <a href="{{ url('/') }}">🛍️ Lihat Toko</a>
            <a href="#" id="admin-logout">🚪 Keluar</a>
        </nav>
    </aside>
</div>
<script src="{{ asset('js/app.js') }}"></script>
@yield('scripts')
</body>
</html>
