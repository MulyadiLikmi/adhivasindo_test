<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar — Pasarin</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<div class="auth-wrap">
    <div class="auth-side">
        <div class="brand" style="font-size:28px; margin-bottom:20px;">Pasar<span style="color:var(--paper)">in</span></div>
        <h2>Gabung sekarang.</h2>
        <p>Daftar sebagai pembeli dan mulai belanja kebutuhan harian dalam hitungan menit.</p>
    </div>
    <div class="auth-form">
        <h2>Buat akun baru</h2>
        <form id="register-form">
            <label>Nama lengkap</label>
            <input type="text" name="name" required placeholder="Nama Anda">
            <label>Email</label>
            <input type="email" name="email" required placeholder="nama@email.com">
            <label>No. HP</label>
            <input type="text" name="phone" placeholder="0812xxxxxxx">
            <label>Kata sandi</label>
            <div class="password-wrap">
                <input type="password" name="password" id="register-password" required placeholder="Minimal 6 karakter">
                <button type="button" class="toggle-password" onclick="togglePassword('register-password', this)">👁️</button>
            </div>
            <label>Konfirmasi kata sandi</label>
            <div class="password-wrap">
                <input type="password" name="password_confirmation" id="register-password-confirm" required placeholder="Ulangi kata sandi">
                <button type="button" class="toggle-password" onclick="togglePassword('register-password-confirm', this)">👁️</button>
            </div>
            <button type="submit" class="btn-primary">Daftar</button>
        </form>
        <div class="auth-switch">Sudah punya akun? <a href="{{ url('/login') }}">Masuk di sini</a></div>
        <div id="register-error" style="color:var(--clay); font-size:13px; margin-top:10px;"></div>
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
document.getElementById('register-form').addEventListener('submit', async function (e) {
    e.preventDefault();
    const form = new FormData(this);
    try {
        const res = await fetch('/api/register', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(Object.fromEntries(form))
        });
        const data = await res.json();
        if (!res.ok) throw new Error(data.message || 'Registrasi gagal');
        localStorage.setItem('pasarin_token', data.token);
        localStorage.setItem('pasarin_role', data.user.role);
        localStorage.setItem('pasarin_email', data.user.email);
        localStorage.setItem('pasarin_name', data.user.name);
        window.location.href = '/';
    } catch (err) {
        document.getElementById('register-error').textContent = err.message;
    }
});
</script>
</body>
</html>
