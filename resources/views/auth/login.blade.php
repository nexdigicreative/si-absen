{{-- resources/views/auth/login.blade.php --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login – SIABSEN</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=JetBrains+Mono:wght@500&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #1a56db 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-wrap {
            width: 420px;
            max-width: 100%;
            background: rgba(255, 255, 255, .04);
            border: 1px solid rgba(255, 255, 255, .12);
            border-radius: 20px;
            padding: 40px;
            backdrop-filter: blur(20px);
        }

        .login-logo {
            text-align: center;
            margin-bottom: 32px;
        }

        .logo-box {
            width: 64px;
            height: 64px;
            background: #1a56db;
            border-radius: 18px;
            display: grid;
            place-items: center;
            margin: 0 auto 14px;
            font-size: 30px;
            font-weight: 900;
            color: #fff;
            font-family: 'JetBrains Mono', monospace;
            box-shadow: 0 8px 32px rgba(26, 86, 219, .5);
        }

        .login-title {
            color: #fff;
            font-size: 24px;
            font-weight: 800;
        }

        .login-sub {
            color: rgba(255, 255, 255, .5);
            font-size: 13px;
            margin-top: 4px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: rgba(255, 255, 255, .8);
            margin-bottom: 6px;
        }

        .form-control {
            width: 100%;
            padding: 10px 14px;
            background: rgba(255, 255, 255, .08);
            border: 1.5px solid rgba(255, 255, 255, .15);
            border-radius: 10px;
            color: #fff;
            font-family: inherit;
            font-size: 14px;
            outline: none;
            transition: border-color .15s, background .15s;
        }

        .form-control:focus {
            border-color: #1a56db;
            background: rgba(255, 255, 255, .12);
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, .3);
        }

        .form-group {
            margin-bottom: 18px;
        }

        .input-group-icon {
            position: relative;
        }

        .input-group-icon i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, .3);
        }

        .input-group-icon .form-control {
            padding-left: 36px;
        }

        .login-btn {
            width: 100%;
            padding: 12px;
            background: #1a56db;
            color: #fff;
            border: none;
            border-radius: 12px;
            font-family: inherit;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all .2s;
            margin-top: 4px;
        }

        .login-btn:hover {
            background: #1040b0;
            transform: translateY(-1px);
            box-shadow: 0 8px 24px rgba(26, 86, 219, .5);
        }

        .login-btn:active {
            transform: translateY(0);
        }

        .divider {
            border: none;
            border-top: 1px solid rgba(255, 255, 255, .1);
            margin: 20px 0;
        }

        .quick-login {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            margin-bottom: 20px;
        }

        .quick-btn {
            padding: 8px 12px;
            border: 1px solid rgba(255, 255, 255, .15);
            border-radius: 8px;
            background: rgba(255, 255, 255, .04);
            color: rgba(255, 255, 255, .6);
            font-size: 11.5px;
            font-weight: 600;
            cursor: pointer;
            transition: all .15s;
            text-align: center;
            font-family: inherit;
        }

        .quick-btn:hover {
            background: rgba(26, 86, 219, .3);
            border-color: #1a56db;
            color: #fff;
        }

        .error-msg {
            background: rgba(239, 68, 68, .15);
            border: 1px solid rgba(239, 68, 68, .3);
            color: #fca5a5;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 16px;
        }

        .hint {
            color: rgba(255, 255, 255, .35);
            font-size: 12px;
            text-align: center;
            margin-top: 16px;
        }

        .school-info {
            text-align: center;
            margin-bottom: 24px;
            padding: 12px;
            background: rgba(255, 255, 255, .04);
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, .08);
        }

        .school-info-name {
            color: rgba(255, 255, 255, .8);
            font-size: 13px;
            font-weight: 600;
        }

        .school-info-addr {
            color: rgba(255, 255, 255, .4);
            font-size: 11px;
            margin-top: 2px;
        }
    </style>
</head>

<body>
    <div class="login-wrap">
        <div class="login-logo">
            <div class="logo-box">S</div>
            <div class="login-title">SIABSEN</div>
            <div class="login-sub">Sistem Informasi Absensi Sekolah</div>
        </div>

        <div class="school-info">
            <div class="school-info-name">🏫 {{ config('school.name', 'SMAN 1 Bandung') }}</div>
            <div class="school-info-addr">{{ config('school.address', '') }}</div>
        </div>

        @if($errors->any())
            <div class="error-msg">
                <i class="bi bi-exclamation-triangle me-1"></i>
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}">
            @csrf

            <div class="form-group">
                <label class="form-label" for="username">Username</label>
                <div class="input-group-icon">
                    <i class="bi bi-person"></i>
                    <input type="text" class="form-control" id="username" name="username"
                        placeholder="Masukkan username..." value="{{ old('username') }}" required autofocus>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <div class="input-group-icon">
                    <i class="bi bi-lock"></i>
                    <input type="password" class="form-control" id="password" name="password"
                        placeholder="Masukkan password..." required>
                </div>
            </div>

            <div class="form-group d-flex align-items-center gap-2" style="margin-bottom:20px">
                <input type="checkbox" id="remember" name="remember"
                    style="accent-color:#1a56db;width:15px;height:15px">
                <label for="remember" style="color:rgba(255,255,255,.6);font-size:13px;cursor:pointer">Ingat
                    Saya</label>
            </div>

            <button type="submit" class="login-btn">
                <i class="bi bi-lock me-2"></i> Masuk ke Sistem
            </button>
        </form>

        <hr class="divider">

        <div style="color:rgba(255,255,255,.5);font-size:11.5px;font-weight:600;margin-bottom:8px;text-align:center">
            DEMO CEPAT</div>
        <div class="quick-login">
            <button class="quick-btn" onclick="fillLogin('admin','admin123')">👑 Admin</button>
            <button class="quick-btn" onclick="fillLogin('siti.rahayu','guru123')">📚 Guru</button>
            <button class="quick-btn" onclick="fillLogin('kepsek','kepsek123')">🏫 Kepala Sekolah</button>
            <button class="quick-btn" onclick="fillLogin('2025001','2025001')">🎒 Siswa</button>
        </div>

        <div class="hint">Demo: Klik tombol di atas untuk login cepat</div>
    </div>

    <script>
        function fillLogin(username, password) {
            document.getElementById('username').value = username;
            document.getElementById('password').value = password;
            document.querySelector('.login-btn').click();
        }
    </script>
</body>

</html>