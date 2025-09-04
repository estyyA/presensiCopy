<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body, html {
        height: 100%;
        margin: 0;
    }

    .left-panel {
        position: relative;
        background-color: #0d47a1;
        color: white;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 30px;
    }

    .left-panel img {
        width: 300px;
        margin-bottom: 20px;
    }

    .left-panel .alamat {
        position: absolute;
        bottom: 20px;
        left: 20px;
        font-size: 14px;
    }

    .right-panel {
        position: relative;
        background: url("{{ asset('img/loginimage.jpeg.jpg') }}") no-repeat center center;
        background-size: cover;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .right-panel::before {
        content: "";
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 0;
    }

    .register-card {
        position: relative;
        background: rgba(255, 255, 255, 0.9);
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0px 4px 8px rgba(0,0,0,0.2);
        width: 400px;
        z-index: 1;
    }

    .register-card h3 {
        text-align: center;
        color: #0d47a1;
        margin-bottom: 20px;
        font-weight: bold;
    }

    .btn-register {
        background-color: #f9a825;
        color: white;
        font-weight: bold;
    }

    .btn-register:hover {
        background-color: #f57f17;
        color: white;
    }

    .register-card .login-link {
        text-align: center;
        margin-top: 10px;
        font-size: 14px;
    }

    .register-card .login-link a {
        color: #0d47a1;
        text-decoration: none;
        font-weight: bold;
    }

    .register-card .login-link a:hover {
        text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="container-fluid h-100">
    <div class="row h-100">

      <!-- Panel Kiri -->
      <div class="col-md-4 left-panel">
        <img src="{{ asset('img/logo.png') }}" alt="Logo">
        <h3>PT Madubaru</h3>
        <p>Pabrik Gula dan Alkohol / Spiritus</p>

        <p class="alamat">
          📍 Jl. Padokan, Tirtonirmolo Kasihan, Bantul<br>
          Yogyakarta 55181
        </p>
      </div>

      <!-- Panel Kanan -->
      <div class="col-md-8 right-panel">
<div class="register-card">
  <h3>Welcome!</h3>
  <form action="{{ route('register.admin') }}" method="POST">
    @csrf
    <div class="mb-3">
      <label for="fullname" class="form-label">Full Name</label>
      <input type="text" name="fullname" id="fullname" class="form-control" placeholder="Enter your full name" required>
    </div>
    <div class="mb-3">
      <label for="email" class="form-label">Email</label>
      <input type="email" name="email" id="email" class="form-control" placeholder="Enter your email" required>
    </div>
    <div class="mb-3">
      <label for="password" class="form-label">Password</label>
      <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" required>
    </div>
    <button type="submit" class="btn btn-register w-100">Create Account Here</button>
  </form>

  <!-- Link ke halaman login -->
  <div class="login-link">
    Already have an account? <a href="{{ route('login.admin') }}">Log In</a>
  </div>
</div>


    </div>
  </div>

  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</body>
</html>