<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - TokoVape</title>

    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">

    <style>
        body {
            margin: 0;
            font-family: 'Nunito', sans-serif;
            background: linear-gradient(to bottom right, #f2f3f7, #e3e4e8);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-card {
            background: white;
            padding: 2rem 2.5rem;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 420px;
            text-align: center;
        }

        .login-card h1 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: #333;
        }

        .form-control {
            border-radius: 0.5rem;
        }

        .btn-login {
            border-radius: 0.5rem;
        }

        .form-group {
            text-align: left;
        }

        .form-check {
            text-align: left;
        }

        .small-link {
            font-size: 0.85rem;
            display: block;
            margin-top: 1rem;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <h1>FOGU POS</h1>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label for="email">Alamat Email</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror"
                       name="email" value="{{ old('email') }}" required autofocus placeholder="email@example.com">
                @error('email')
                    <span class="invalid-feedback d-block" role="alert">
                        {{ $message }}
                    </span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Kata Sandi</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror"
                       name="password" required placeholder="">
                @error('password')
                    <span class="invalid-feedback d-block" role="alert">
                        {{ $message }}
                    </span>
                @enderror
            </div>

            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="remember" id="remember"
                       {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label" for="remember">
                    Ingat Saya
                </label>
            </div>

            <button type="submit" class="btn btn-primary btn-block btn-login">
                Masuk
            </button>
        </form>

        @if (Route::has('password.request'))
            <a class="small-link" href="{{ route('password.request') }}">
                Lupa password?
            </a>
        @endif
    </div>

</body>
</html>