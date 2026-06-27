<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - SIPDA Kota Bekasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            height: 100vh;
            overflow: hidden;
        }

        .split-screen {
            display: flex;
            height: 100%;
        }

        /* BAGIAN KIRI: GAMBAR LOKAL */
        .left-pane {
            flex: 1;
            /* Menggunakan gambar dari folder public/images */
            background-image: url("{{ asset('images/login-bg.jpg') }}");
            background-size: cover;
            background-position: center;
            position: relative;
            display: flex;
            align-items: flex-end;
            padding: 40px;
            color: white;
        }

        .left-pane::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to bottom, rgba(78, 115, 223, 0.2), rgba(34, 74, 190, 0.8));
        }

        .left-content {
            position: relative;
            z-index: 2;
            max-width: 500px;
        }

        /* BAGIAN KANAN: FORM */
        .right-pane {
            width: 500px;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            flex-shrink: 0;
        }

        .login-container {
            width: 100%;
            max-width: 380px;
        }

        .logo-area {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo-img {
            width: 80px;
            margin-bottom: 15px;
        }

        .app-title {
            font-weight: 700;
            color: #4e73df;
            font-size: 1.5rem;
            margin: 0;
            line-height: 1.2;
        }

        .app-subtitle {
            color: #858796;
            font-size: 0.9rem;
        }

        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            background: #f8f9fa;
            border: 1px solid #e3e6f0;
            font-size: 0.95rem;
        }

        .form-control:focus {
            background: #fff;
            border-color: #4e73df;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }

        .btn-login {
            background: #4e73df;
            color: white;
            padding: 12px;
            border-radius: 10px;
            width: 100%;
            font-weight: 600;
            margin-top: 25px;
            border: none;
            transition: 0.3s;
        }

        .btn-login:hover {
            background: #2e59d9;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(78, 115, 223, 0.3);
        }

        @media (max-width: 768px) {
            .left-pane {
                display: none;
            }

            .right-pane {
                width: 100%;
            }
        }
    </style>
</head>

<body>

    <div class="split-screen">
        <div class="left-pane">
            <div class="left-content">
                <h1 class="fw-bold mb-2">Membangun Kota Bekasi <br>Yang Lebih Maju</h1>
                <p class="lead mb-0 opacity-75">Sistem Informasi Monitoring Kegiatan Diskominfostandi Kota Bekasi</p>
            </div>
        </div>

        <div class="right-pane">
            <div class="login-container">
                <div class="logo-area">
                    <img src="{{ asset('images/logo-bekasi.png') }}" alt="Logo Bekasi" class="logo-img">
                    <h3 class="app-title">SIPDA</h3>
                    <div class="app-subtitle">Kota Bekasi</div>
                </div>

                <h5 class="fw-bold text-dark mb-4">Silakan Masuk</h5>

                @if (session('error'))
                <div class="alert alert-danger shadow-sm border-0 rounded-3 mb-4" style="font-size: 0.9rem;">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                </div>
                @endif

                @if ($errors->any())
                <div class="alert alert-danger shadow-sm border-0 rounded-3 mb-4" style="font-size: 0.9rem;">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="user@bekasikota.go.id" required autofocus>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                            <label class="form-check-label small text-muted" for="remember">Ingat Saya</label>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted">Selesaikan Hitungan Ini</label>
                        <div class="d-flex align-items-center mb-2 bg-light p-2 rounded border">
                            <span id="captcha-img" class="me-auto">{!! captcha_img('math') !!}</span>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="document.getElementById('captcha-img').firstElementChild.src = '/captcha/math?' + Math.random()">
                                🔄 Ulangi
                            </button>
                        </div>
                        <input id="captcha" type="text" class="form-control" placeholder="Masukkan angka hasil hitungan" name="captcha" required>
                        <x-input-error :messages="$errors->get('captcha')" class="mt-2 text-danger small fw-bold" />
                    </div>

                    <button type="submit" class="btn-login">MASUK SEKARANG</button>
                </form>

                <div class="text-center mt-4">
                    <p class="small text-muted">Belum punya akun? <a href="{{ route('register') }}" class="fw-bold text-primary text-decoration-none">Daftar</a></p>
                </div>
            </div>
        </div>
    </div>

</body>

</html>