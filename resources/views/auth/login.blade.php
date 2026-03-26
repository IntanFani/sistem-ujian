<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | CBT MTs Al Huda</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
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
                    <label>Username / Email</label>
                    <input type="text" name="email" class="form-control" placeholder="Masukkan Email" required autofocus>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Masukkan Password" required>
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

</body>
</html>