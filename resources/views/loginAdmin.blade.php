<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Admin</title>
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

    /* alamat ditaruh di bawah kiri */
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

    /* Overlay hanya untuk panel kanan */
    .right-panel::before {
        content: "";
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 0;
    }

    .login-card {
        position: relative;
        background: rgba(255, 255, 255, 0.9);
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0px 4px 8px rgba(0,0,0,0.2);
        width: 350px;
        z-index: 1; /* pastikan di atas overlay */
    }

    .login-card h3 {
        text-align: center;
        color: #0d47a1;
        margin-bottom: 20px;
    }

    .btn-login {
        background-color: #f9a825;
        color: white;
        font-weight: bold;
    }

    .btn-login:hover {
        background-color: #f57f17;
        color: white;
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

        <!-- alamat pindah ke bawah kiri -->
        <p class="alamat">
          📍 Jl. Padokan, Tirtonirmolo Kasihan, Bantul<br>
          Yogyakarta 55181
        </p>
      </div>

      <!-- Panel Kanan -->
      <div class="col-md-8 right-panel">
        <div class="login-card">
          <h3>Login Here!</h3>
          <form action="{{ route('login.admin') }}" method="POST">
            @csrf
            <div class="mb-3">
              <label for="username" class="form-label">Username</label>
              <div class="input-group">
                <input type="text" name="username" id="username" class="form-control" placeholder="Masukkan username" required>
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
              </div>
            </div>
            <div class="mb-3">
              <label for="password" class="form-label">Password</label>
              <div class="input-group">
                <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan password" required>
                <span class="input-group-text" id="togglePassword" style="cursor: pointer;">
                  <i class="bi bi-eye-slash" id="eyeIcon"></i>
                </span>
              </div>
            </div>
            <button type="submit" class="btn btn-login w-100">Login</button>
          </form>
        </div>
      </div>

    </div>
  </div>

  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

  <!-- Script Show/Hide Password -->
  <script>
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');
    const eyeIcon = document.querySelector('#eyeIcon');

    togglePassword.addEventListener('click', function () {
      const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
      password.setAttribute('type', type);

      eyeIcon.classList.toggle('bi-eye');
      eyeIcon.classList.toggle('bi-eye-slash');
    });
  </script>
</body>
</html>