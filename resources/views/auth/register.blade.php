<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - SIPDA Kota Bekasi</title>
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

        /* KIRI: FORM */
        .left-pane {
            width: 500px;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            flex-shrink: 0;
            overflow-y: auto;
        }

        /* KANAN: GAMBAR LOKAL */
        .right-pane {
            flex: 1;
            /* Menggunakan gambar dari folder public/images */
            background-image: url("{{ asset('images/register-bg.jpg') }}");
            background-size: cover;
            background-position: center;
            position: relative;
        }

        .right-pane::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to top, rgba(78, 115, 223, 0.4), rgba(34, 74, 190, 0.2));
        }

        .reg-container {
            width: 100%;
            max-width: 380px;
        }

        .logo-img {
            width: 60px;
            margin-bottom: 10px;
        }

        .form-control {
            border-radius: 10px;
            padding: 10px 15px;
            background: #f8f9fa;
            border: 1px solid #e3e6f0;
        }

        .form-control:focus {
            background: #fff;
            border-color: #4e73df;
        }

        .btn-register {
            background: #4e73df;
            color: white;
            padding: 12px;
            border-radius: 10px;
            width: 100%;
            font-weight: 600;
            margin-top: 20px;
            border: none;
            transition: 0.3s;
        }

        .btn-register:hover {
            background: #2e59d9;
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .right-pane {
                display: none;
            }

            .left-pane {
                width: 100%;
            }
        }
    </style>
</head>

<body>

    <div class="split-screen">
        <div class="left-pane">
            <div class="reg-container">
                <div class="text-center mb-4">
                    <img src="{{ asset('images/logo-bekasi.png') }}" alt="Logo Bekasi" class="logo-img">
                    <h4 class="fw-bold m-0 text-primary">SIPDA</h4>
                    <span class="text-muted small">Registrasi Akun Baru</span>
                </div>
                @if ($errors->any())
                <div class="alert alert-danger shadow-sm border-0 rounded-3">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" placeholder="Nama Anda" required>
                        @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Email Dinas / Pribadi</label>
                        <input type="email" name="email" class="form-control" placeholder="nama@email.com" required>
                        @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Min. 8 karakter" required>
                        @error('password') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password" required>
                    </div>

                    <button type="submit" class="btn-register">DAFTAR AKUN</button>
                </form>

                <div class="text-center mt-4">
                    <p class="small text-muted">Sudah punya akun? <a href="{{ route('login') }}" class="fw-bold text-primary text-decoration-none">Masuk</a></p>
                </div>
            </div>
        </div>

        <div class="right-pane">
        </div>
    </div>

</body>

</html>