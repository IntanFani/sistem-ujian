<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | CBT MTs Al Huda</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>

<body>

    <div class="login-wrapper">
        <div class="left-panel">
            <h1>CBT-ALHUDA</h1>
            <p>Sistem Informasi Ujian Terintegrasi<br>MTs Al Huda Pamegatan</p>
        </div>

        <div class="right-panel">
            <div class="login-box">
                <img src="{{ asset('images/logo-sekolah.png') }}" alt="Logo" class="school-logo">

                <h4>Sistem Informasi Ujian Terintegrasi<br>MTs Al Huda Pamegatan</h4>
                <!-- <hr class="mb-4" style="opacity: 0.1"> -->


                <form action="{{ route('login.post') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        {{-- 1. Ubah label biar user tahu bisa pakai NISN --}}
                        <label>Usernama/Email</label>

                        {{-- 2. Ubah name="email" jadi name="login", dan old('email') jadi old('login') --}}
                        <input type="text" name="login" class="form-control @error('login') is-invalid @enderror"
                            placeholder="Masukkan Usename / Email" value="{{ old('login') }}" required autofocus>

                        {{-- 3. Ubah tangkapan error dari 'email' jadi 'login' --}}
                        @error('login')
                            <span class="text-danger" style="font-size: 12px;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <div class="password-container" style="position: relative;">
                            <input type="password" name="password" id="password"
                                class="form-control @error('password') is-invalid @enderror"
                                placeholder="Masukkan Password" required>

                            <i class="bi bi-eye-slash" id="togglePassword"
                                style="position: absolute; top: 50%; right: 15px; transform: translateY(-50%); cursor: pointer; color: #6c757d; z-index: 10;">
                            </i>
                        </div>

                        @error('password')
                            <span class="text-danger" style="font-size: 12px;">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="btn-login">Masuk</button>
                </form>

                <div class="copy-text">
                    Copyright © 2026<br>
                    Tim IT MTs Al Huda Pamegatan
                </div>
            </div>
        </div>
    </div>
    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', function(e) {
            // Toggle tipe input (password ke text atau sebaliknya)
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);

            // Toggle icon (mata terbuka / mata tertutup)
            this.classList.toggle('bi-eye');
            this.classList.toggle('bi-eye-slash');
        });
    </script>
</body>

</html>
