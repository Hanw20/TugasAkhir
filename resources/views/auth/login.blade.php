@extends('layouts.app')

@section('content')
<div class="container d-flex align-items-center justify-content-center" style="min-height: 80vh;">
    <div class="card shadow-sm border-0" style="width: 100%; max-width: 440px; border-radius: 16px; padding: 24px 20px;">
        <div class="card-body p-3">
            <!-- Icon Toko & Title -->
            <div class="text-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px; background-color: #EBF5F3; border-radius: 16px;">
                    <i class="bi bi-shop" style="font-size: 2rem; color: #264653;"></i>
                </div>
                <h3 class="fw-bold mb-1" style="color: #264653; letter-spacing: -0.5px;">Toko "X"</h3>
                <p class="text-muted small mb-0">Silahkan Login Untuk Mengakses Administrasi</p>
            </div>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Input Email -->
                <div class="mb-3">
                    <label for="email" class="form-label small fw-semibold text-dark mb-1">Alamat email</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted" style="border-radius: 8px 0 0 8px;">
                            <i class="bi bi-envelope"></i>
                        </span>
                        <input id="email" type="email" class="form-control border-start-0 @error('email') is-invalid @enderror" 
                               name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                               placeholder="andi@sumbermakmur.id" style="border-radius: 0 8px 8px 0; font-size: 0.95rem;">
                    </div>
                    @error('email')
                        <span class="text-danger small mt-1 d-block" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <!-- Input Password -->
                <div class="mb-3">
                    <label for="password" class="form-label small fw-semibold text-dark mb-1">Kata sandi</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted" style="border-radius: 8px 0 0 8px;">
                            <i class="bi bi-lock"></i>
                        </span>
                        <input id="password" type="password" class="form-control border-start-0 border-end-0 @error('password') is-invalid @enderror" 
                               name="password" required autocomplete="current-password"
                               placeholder="••••••••••" style="font-size: 0.95rem;">
                        <span class="input-group-text bg-white border-start-0 text-muted" style="cursor: pointer; border-radius: 0 8px 8px 0;" onclick="togglePassword()">
                            <i class="bi bi-eye-slash" id="toggleIcon"></i>
                        </span>
                    </div>
                    @error('password')
                        <span class="text-danger small mt-1 d-block" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="mb-4 d-flex justify-content-between align-items-center">
                    <div class="form-check mb-0">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label small text-muted" for="remember">
                            {{ __('Ingat saya') }}
                        </label>
                    </div>
                    @if (Route::has('password.request'))
                        <a class="text-decoration-none small" href="{{ route('password.request') }}" style="color: #2A9D8F;">
                            Lupa sandi?
                        </a>
                    @endif
                </div>

                <!-- Tombol Submit -->
                <div class="d-grid">
                    <button type="submit" class="btn text-white py-2 fw-semibold d-flex align-items-center justify-content-center gap-2 shadow-sm" 
                            style="background-color: #264653; border-radius: 8px; font-size: 0.95rem;">
                        <span>Masuk ke toko</span>
                        <i class="bi bi-arrow-right"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('toggleIcon');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.remove('bi-eye-slash');
            toggleIcon.classList.add('bi-eye');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.remove('bi-eye');
            toggleIcon.classList.add('bi-eye-slash');
        }
    }
</script>
@endsection
