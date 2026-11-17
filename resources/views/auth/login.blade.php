<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login — Toko Bahan Bangunan "X"</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        /* Menghilangkan ikon mata default dari browser Microsoft Edge & Chrome */
        input::-ms-reveal,
        input::-ms-clear {
            display: none !important;
        }

        body {
            background-color: #F8F9FA;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 15px;
        }

        .login-box {
            width: 100%;
            max-width: 380px;
            background: #FFFFFF;
            border: 1px solid #E5E7EB;
            border-radius: 12px;
            padding: 35px 28px;
        }

        .brand-icon {
            width: 56px;
            height: 56px;
            background-color: #EBF5F3;
            color: #264653;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            margin-bottom: 16px;
        }

        .brand-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: #264653;
            margin-bottom: 4px;
        }

        .brand-subtitle {
            font-size: 0.85rem;
            color: #6C757D;
            margin-bottom: 24px;
        }

        .form-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }

        .form-control {
            border: 1px solid #D1D5DB;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 0.925rem;
            color: #1F2937;
        }

        .form-control:focus {
            border-color: #264653;
            box-shadow: none;
            outline: none;
        }

        .btn-submit {
            background-color: #264653;
            color: #FFFFFF;
            border: none;
            border-radius: 8px;
            padding: 11px;
            font-size: 0.925rem;
            font-weight: 600;
            width: 100%;
            margin-top: 18px;
            transition: background-color 0.15s ease;
        }

        .btn-submit:hover {
            background-color: #1D353F;
            color: #FFFFFF;
        }

        .copyright-text {
            font-size: 0.775rem;
            color: #9CA3AF;
            margin-top: 24px;
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="login-box">
        <!-- Header -->
        <div class="text-center">
            <div class="brand-icon">
                <i class="bi bi-shop"></i>
            </div>
            <h1 class="brand-title">Toko "X"</h1>
            <p class="brand-subtitle">Sistem Informasi & Pengambilan Keputusan</p>
        </div>

        <!-- Form Login -->
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email -->
            <div class="mb-3">
                <label for="email" class="form-label">Alamat Email</label>
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                       name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                @error('email')
                    <div class="invalid-feedback mt-1 small">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Password (Standar Sederhana & Bersih) -->
            <div class="mb-3">
                <label for="password" class="form-label">Kata Sandi</label>
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                       name="password" required autocomplete="current-password">
                @error('password')
                    <div class="invalid-feedback mt-1 small">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Tombol Masuk -->
            <button type="submit" class="btn-submit">
                Masuk
            </button>
        </form>

        <div class="copyright-text">
            &copy; {{ date('Y') }} Toko Bahan Bangunan "X".
        </div>
    </div>

</body>
</html>
