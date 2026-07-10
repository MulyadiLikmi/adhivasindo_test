<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Pasarin — Belanja Kebutuhan Harian')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <header class="market-banner">
        <div class="container inner">
            <a href="{{ url('/') }}" class="brand">Pasar<span>in</span></a>
            <form class="market-search" action="{{ url('/') }}" method="GET">
                <input type="text" name="q" placeholder="Cari beras, minyak, kopi..." value="{{ request('q') }}">
                <button type="submit">Cari</button>
            </form>
            <div class="nav-actions">
                <a href="{{ url('/cart') }}" class="cart-pill">🧺 Keranjang <span id="cart-count">0</span></a>
                <div id="auth-area">
                    <a href="{{ url('/login') }}">Masuk</a>
                </div>
            </div>
        </div>
    </header>

    <main class="container">
        @yield('content')
    </main>

    <script src="{{ asset('js/app.js') }}"></script>
    @yield('scripts')
</body>
</html>
