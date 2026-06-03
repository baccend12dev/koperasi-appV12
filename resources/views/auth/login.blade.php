<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Koperasi OPI') }} &mdash; Masuk</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Vite Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #F3F1FA 0%, #E6E2F5 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
        }

        /* Ambient background glow elements */
        .glow-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(100px);
            opacity: 0.55;
            z-index: 0;
            transition: all 0.5s ease;
            pointer-events: none;
        }
        
        .blob-1 {
            width: 400px;
            height: 400px;
            background: #5C4F8A;
            top: -100px;
            left: -100px;
        }
        
        .blob-2 {
            width: 450px;
            height: 450px;
            background: #C0B4F0;
            bottom: -150px;
            right: -100px;
        }

        .login-container {
            width: 100%;
            max-width: 440px;
            padding: 24px;
            position: relative;
            z-index: 10;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(92, 79, 138, 0.08), 0 1px 3px rgba(0, 0, 0, 0.02);
            padding: 40px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .login-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 36px rgba(92, 79, 138, 0.12), 0 2px 4px rgba(0, 0, 0, 0.02);
        }

        .logo-wrap {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 30px;
            text-align: center;
        }

        .logo-container {
            width: 64px;
            height: 64px;
            background: white;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(92, 79, 138, 0.1);
            margin-bottom: 16px;
            border: 1px solid rgba(92, 79, 138, 0.1);
            color: #5C4F8A;
        }

        .logo-container svg {
            width: 36px;
            height: 36px;
            fill: currentColor;
        }

        .app-title {
            font-size: 22px;
            font-weight: 700;
            color: #1C1A2E;
            letter-spacing: -0.5px;
            margin-bottom: 4px;
        }

        .app-subtitle {
            font-size: 13px;
            color: #6B6880;
            font-weight: 500;
        }

        /* Error/success alerts */
        .alert-box {
            background: #FCEBEB;
            border-left: 4px solid #A32D2D;
            color: #A32D2D;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 24px;
            font-weight: 500;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .alert-box svg {
            flex-shrink: 0;
            margin-top: 2px;
        }

        /* Form elements */
        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #1C1A2E;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            color: #A09DB8;
            pointer-events: none;
            transition: color 0.2s ease;
        }

        .form-input {
            width: 100%;
            height: 44px;
            padding: 0 16px 0 42px;
            font-size: 14px;
            color: #1C1A2E;
            background: #FFFFFF;
            border: 1px solid #D0CCEC;
            border-radius: 10px;
            outline: none;
            transition: all 0.2s ease;
        }

        .form-input:focus {
            border-color: #5C4F8A;
            box-shadow: 0 0 0 3px rgba(92, 79, 138, 0.15);
        }

        .form-input:focus + .input-icon {
            color: #5C4F8A;
        }

        .password-toggle {
            position: absolute;
            right: 14px;
            background: none;
            border: none;
            color: #A09DB8;
            cursor: pointer;
            display: flex;
            align-items: center;
            padding: 4px;
            transition: color 0.2s ease;
        }

        .password-toggle:hover {
            color: #5C4F8A;
        }

        .input-error-msg {
            color: #A32D2D;
            font-size: 12px;
            font-weight: 500;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* Checkbox & Forgot link */
        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 16px;
            margin-bottom: 24px;
        }

        .remember-me {
            display: inline-flex;
            align-items: center;
            cursor: pointer;
        }

        .remember-me input[type="checkbox"] {
            width: 16px;
            height: 16px;
            border-radius: 4px;
            border: 1px solid #D0CCEC;
            accent-color: #5C4F8A;
            cursor: pointer;
        }

        .remember-text {
            font-size: 13px;
            color: #6B6880;
            margin-left: 8px;
            font-weight: 500;
            user-select: none;
        }

        .forgot-link {
            font-size: 13px;
            color: #5C4F8A;
            font-weight: 600;
            transition: color 0.2s ease;
        }

        .forgot-link:hover {
            color: #4a3e70;
            text-decoration: underline;
        }

        /* Submit Button */
        .btn-submit {
            width: 100%;
            height: 46px;
            background: #5C4F8A;
            border: none;
            color: #FFFFFF;
            font-size: 14px;
            font-weight: 700;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(92, 79, 138, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-submit:hover {
            background: #4a3e70;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(92, 79, 138, 0.3);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-fade-in {
            animation: fadeIn 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
    </style>
</head>
<body class="antialiased">
    <!-- Glow elements -->
    <div class="glow-blob blob-1"></div>
    <div class="glow-blob blob-2"></div>

    <div class="login-container animate-fade-in">
        <div class="login-card">
            
            <div class="logo-wrap">
                <div class="logo-container">
                    <x-application-logo />
                </div>
                <h2 class="app-title">Koperasi Karyawan OPI</h2>
                <p class="app-subtitle">Selamat datang kembali! Silakan masuk ke akun Anda</p>
            </div>

            <!-- Session Status (if exists) -->
            @if(session('status'))
                <div class="alert-box animate-fade-in" style="background: #EAF3DE; border-left: 4px solid #2F6010; color: #2F6010;">
                    <svg width="16" height="16" viewBox="0 0 15 15" fill="none"><circle cx="7.5" cy="7.5" r="6.5" stroke="currentColor" stroke-width="1.3"/><path d="M4.5 7.5l2 2.5 4-4" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <!-- Main error message (like throttle or generic errors) -->
            @if($errors->has('email') && !$errors->has('password') && $errors->first('email') !== 'Akun tidak ditemukan.' && !Str::contains($errors->first('email'), 'tidak aktif'))
                <div class="alert-box animate-fade-in">
                    <svg width="16" height="16" viewBox="0 0 15 15" fill="none"><circle cx="7.5" cy="7.5" r="6.5" stroke="currentColor" stroke-width="1.3"/><path d="M7.5 4.5v4M7.5 10.5h.01" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                    <span>{{ $errors->first('email') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" x-data="{ loading: false }" @submit="loading = true">
                @csrf

                <!-- Email / NIK Address -->
                <div class="form-group">
                    <label for="email" class="form-label">Email atau NIK</label>
                    <div class="input-wrapper">
                        <svg class="input-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <input id="email" class="form-input" type="text" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="contoh@domain.com atau 1001" />
                    </div>
                    @if ($errors->has('email') && ($errors->first('email') === 'Akun tidak ditemukan.' || Str::contains($errors->first('email'), 'tidak aktif')))
                        <div class="input-error-msg animate-fade-in">
                            <svg width="14" height="14" viewBox="0 0 15 15" fill="none"><circle cx="7.5" cy="7.5" r="6.5" stroke="currentColor" stroke-width="1.3"/><path d="M7.5 4.5v4M7.5 10.5h.01" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                            <span>{{ $errors->first('email') }}</span>
                        </div>
                    @endif
                </div>

                <!-- Password -->
                <div class="form-group" x-data="{ showPassword: false }">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-wrapper">
                        <svg class="input-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <input id="password" class="form-input" :type="showPassword ? 'text' : 'password'" name="password" required autocomplete="current-password" placeholder="Masukkan password Anda" />
                        
                        <button type="button" class="password-toggle" @click="showPassword = !showPassword" aria-label="Toggle password visibility">
                            <!-- eye icon -->
                            <svg x-show="!showPassword" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <!-- eye-off icon -->
                            <svg x-show="showPassword" x-cloak width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                            </svg>
                        </button>
                    </div>
                    @if ($errors->has('password'))
                        <div class="input-error-msg animate-fade-in">
                            <svg width="14" height="14" viewBox="0 0 15 15" fill="none"><circle cx="7.5" cy="7.5" r="6.5" stroke="currentColor" stroke-width="1.3"/><path d="M7.5 4.5v4M7.5 10.5h.01" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                            <span>{{ $errors->first('password') }}</span>
                        </div>
                    @endif
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="form-options">
                    <label for="remember_me" class="remember-me">
                        <input id="remember_me" type="checkbox" name="remember" />
                        <span class="remember-text">Ingat Saya</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a class="forgot-link" href="{{ route('password.request') }}">
                            Lupa Password?
                        </a>
                    @endif
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-submit" :disabled="loading">
                    <span x-show="!loading">Masuk</span>
                    <span x-show="loading" x-cloak style="display: inline-flex; align-items: center; gap: 8px;">
                        <svg class="animate-spin" style="animation: spin 1s linear infinite;" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" stroke-dasharray="32" stroke-dashoffset="10" fill="none"></circle>
                        </svg>
                        Memproses...
                    </span>
                </button>
            </form>
        </div>
    </div>

    <style>
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </style>
</body>
</html>
