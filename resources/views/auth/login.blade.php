<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — Pasarin</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<div class="auth-wrap">
    <div class="auth-side">
        <div class="brand" style="font-size:28px; margin-bottom:20px;">Pasar<span style="color:var(--paper)">in</span></div>
        <h2>Selamat datang kembali.</h2>
        <p>Lanjutkan belanja kebutuhan harian dengan harga kios langganan Anda.</p>
    </div>
    <div class="auth-form">
        <h2>Masuk ke akun</h2>
        <form id="login-form">
            <label>Email</label>
            <input type="email" name="email" required placeholder="nama@email.com">
            <label>Kata sandi</label>
            <div class="password-wrap">
                <input type="password" name="password" id="login-password" required placeholder="••••••••">
                <button type="button" class="toggle-password" onclick="togglePassword('login-password', this)">👁️</button>
            </div>
            <button type="submit" class="btn-primary">Masuk</button>
        </form>
        <div class="auth-switch">Belum punya akun? <a href="{{ url('/register') }}">Daftar di sini</a></div>
        <div id="login-error" style="color:var(--clay); font-size:13px; margin-top:10px;"></div>
    </div>
</div>
<script src="{{ asset('js/app.js') }}"></script>
<script>
function togglePassword(inputId, btn) {
    const input = document.getElementById(inputId);
    const isHidden = input.type === 'password';
    input.type = isHidden ? 'text' : 'password';
    btn.textContent = isHidden ? '🙈' : '👁️';
}
document.getElementById('login-form').addEventListener('submit', async function (e) {
    e.preventDefault();
    const form = new FormData(this);
    try {
        const res = await fetch('/api/login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(Object.fromEntries(form))
        });
        const data = await res.json();
        if (!res.ok) throw new Error(data.message || 'Login gagal');
        localStorage.setItem('pasarin_token', data.token);
        localStorage.setItem('pasarin_role', data.user.role);
        localStorage.setItem('pasarin_email', data.user.email);
        localStorage.setItem('pasarin_name', data.user.name);
        window.location.href = data.user.role === 'admin' ? '/admin/dashboard' : '/';
    } catch (err) {
        document.getElementById('login-error').textContent = err.message;
    }
});
</script>
</body>
</html>
