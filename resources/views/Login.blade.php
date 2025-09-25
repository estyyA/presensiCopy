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
        font-family: 'Segoe UI', Arial, sans-serif;
        background: linear-gradient(to bottom, #3f71dc, #1f3e99);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    .login-container {
        width: 100%;
        max-width: 400px;
        text-align: center;
    }
    .login-container img {
        width: 100px;
        margin-bottom: 10px;
        border-radius: 50%;
        border: 2px solid #e0e7ff;
        animation: bounce 2s infinite;
    }
    @keyframes bounce {0%,100%{transform:translateY(0);}50%{transform:translateY(-10px);}}
    .login-container h1 {color: #e0e7ff; font-weight: bold; margin-bottom: 0;}
    .login-container p {color: #cbd5e1; margin-bottom: 25px; font-size: 14px;}
    .login-card {
        background: #ffffff;
        padding: 30px;
        border-radius: 20px;
        box-shadow: 0px 8px 20px rgba(0,0,0,0.25);
        text-align: left;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .login-card:hover {transform:translateY(-5px); box-shadow:0px 12px 25px rgba(0,0,0,0.4);}
    .login-card h5 {text-align:center;margin-bottom:25px;color:#3f71dc;font-weight:600;}
    .form-group {position: relative;}
    .form-control {
        border-radius:10px;
        padding-left:40px;
        background:#f1f5f9;
        border:1px solid #ccc;
    }
    .form-control:focus {border-color:#3f71dc; box-shadow:0 0 0 0.2rem rgba(63,113,220,0.25);}
    .input-icon {position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#3f71dc;}
    .btn-login {background: linear-gradient(to right, #3f71dc, #1f3e99); color:white; font-weight:bold; border-radius:10px; padding:12px; transition: all 0.3s ease;}
    .btn-login:hover {background: linear-gradient(to right, #1f3e99, #1a357f); transform:translateY(-2px); box-shadow:0px 6px 12px rgba(0,0,0,0.2);}
    .forgot {font-size:13px;text-decoration:none;color:#3f71dc;}
    .forgot:hover {color:#1f3e99;}
    .register {color:#3f71dc;}
    .register:hover {color:#1f3e99; text-decoration:underline;}
</style>
</head>
<body>
<div class="login-container">
    <img src="{{ asset('img/logo.png') }}" alt="Logo">
    <h1>PT Madubaru</h1>
    <p>PG - PS MADUKISMO</p>

    <div class="login-card">
        <h5><i class="bi bi-box-arrow-in-right me-2"></i>Login</h5>
        <form action="{{ route('login.process') }}" method="POST">
            @csrf
            <!-- Username -->
            <div class="form-group mb-3">
                <i class="bi bi-person input-icon"></i>
                <input type="text" name="username" class="form-control" placeholder="Username" required>
            </div>
            <!-- Password -->
            <div class="form-group mb-3">
                <i class="bi bi-lock input-icon"></i>
                <div class="input-group">
                    <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
                    <span class="input-group-text" id="togglePassword" style="cursor:pointer;">
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
                <a href="{{ route('password.request') }}" class="forgot">Forgot Password?</a>
            </div>

            <button type="submit" class="btn btn-login w-100 mb-2"><i class="bi bi-box-arrow-in-right me-2"></i>Login</button>
            <div class="text-center mt-3">
                <small>Belum punya akun? <a href="{{ url('/register') }}" class="register fw-bold">Register</a></small>
            </div>
        </form>
    </div>
</div>

<script>
const togglePassword = document.querySelector('#togglePassword');
const password = document.querySelector('#password');
const eyeIcon = document.querySelector('#eyeIcon');
togglePassword.addEventListener('click', function(){
    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
    password.setAttribute('type', type);
    eyeIcon.classList.toggle('bi-eye');
    eyeIcon.classList.toggle('bi-eye-slash');
});
</script>
</body>
</html>
