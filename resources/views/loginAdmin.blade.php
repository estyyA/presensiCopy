<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <style>
    body, html {
      height: 100%;
      margin: 0;
      font-family: Arial, sans-serif;
      background: linear-gradient(to bottom, #1976d2, #0d47a1);
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .login-container {
      width: 100%;
      max-width: 400px;
      text-align: center;
    }
    .login-container img {
      width: 120px;
      margin-bottom: 10px;
    }
    .login-container h1 {
      color: white;
      font-weight: bold;
      margin-bottom: 0;
    }
    .login-container p {
      color: white;
      margin-bottom: 25px;
      font-size: 14px;
    }
    .login-card {
      background: white;
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0px 4px 8px rgba(0,0,0,0.2);
      text-align: left;
    }
    .login-card h5 {
      text-align: center;
      margin-bottom: 20px;
      color: #333;
    }
    .btn-login {
      background: linear-gradient(to right, #1976d2, #0d47a1);
      color: white;
      font-weight: bold;
    }
    .btn-login:hover {
      background: linear-gradient(to right, #1565c0, #0b3c91);
    }
    .forgot {
      font-size: 13px;
      text-decoration: none;
    }
  </style>
</head>
<body>
  <div class="login-container">
    <!-- Logo & Title -->
    <img src="{{ asset('img/logo.png') }}" alt="Logo">
    <h1>PT Madubaru</h1>
    <p>PG - PS MADUKISMO</p>

    <!-- Card -->
    <div class="login-card">
      <h5>Login Here</h5>
      <form action="{{ route('login.admin') }}" method="POST">
        @csrf
        <!-- Username -->
        <div class="mb-3">
          <label for="username" class="form-label">User Name</label>
          <input type="text" name="username" id="username" class="form-control" placeholder="Enter username" required>
        </div>
        <!-- Password -->
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <div class="input-group">
            <input type="password" name="password" id="password" class="form-control" placeholder="Enter password" required>
            <span class="input-group-text" id="togglePassword" style="cursor: pointer;">
              <i class="bi bi-eye-slash" id="eyeIcon"></i>
            </span>
          </div>
        </div>
        <!-- Remember & Forgot -->
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div class="form-check">
            <input type="checkbox" class="form-check-input" id="remember" name="remember">
            <label class="form-check-label" for="remember">Remember me</label>
          </div>
          <a href="#" class="forgot">Forgot Password</a>
        </div>
        <!-- Button -->
        <button type="submit" class="btn btn-login w-100">Login</button>
      </form>
    </div>
  </div>

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
