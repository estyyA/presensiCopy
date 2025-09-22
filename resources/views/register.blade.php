<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Akun</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(to bottom, #3f71dc, #1f3e99);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 15px;
        }

        .register-container {
            width: 100%;
            max-width: 950px;
        }

        .register-card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0px 12px 25px rgba(0,0,0,0.25);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease;
        }

        .register-card:hover { transform: translateY(-5px); }

        .register-header {
            background: linear-gradient(to right, #3f71dc, #1f3e99);
            color: white;
            padding: 25px 20px;
            text-align: center;
        }

        .register-header img {
            width: 80px;
            margin-bottom: 10px;
            border-radius: 50%;
            border: 2px solid white;
        }

        .register-header h1 {
            margin: 0;
            font-size: 26px;
            font-weight: 700;
        }

        .register-header p {
            margin: 0;
            font-size: 14px;
            color: #e3f2fd;
        }

        .register-body {
            padding: 35px 30px 40px 30px;
            max-height: 70vh;
            overflow-y: auto;
        }

        .register-body h5 {
            text-align: center;
            margin-bottom: 25px;
            font-weight: 600;
            color: #3f71dc;
        }

        .form-group { position: relative; }
        .form-control, .form-select {
            border-radius: 10px;
            padding-left: 40px;
        }
        .input-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #3f71dc;
        }

        .btn-register {
            background: linear-gradient(to right, #3f71dc, #1f3e99);
            color: white;
            font-weight: bold;
            border-radius: 10px;
            padding: 12px;
            transition: all 0.3s ease;
            margin-top: 20px;
        }

        .btn-register:hover {
            background: linear-gradient(to right, #1f3e99, #1a357f);
            transform: translateY(-2px);
            box-shadow: 0px 6px 12px rgba(0,0,0,0.2);
        }

        .footer-text {
            text-align: center;
            font-size: 13px;
            color: #666;
            padding: 12px;
            background: #f9f9f9;
            border-top: 1px solid #eee;
        }

        /* scrollbar */
        .register-body::-webkit-scrollbar { width: 6px; }
        .register-body::-webkit-scrollbar-thumb { background: #3f71dc; border-radius: 10px; }
        .register-body::-webkit-scrollbar-track { background: #f1f1f1; }
    </style>
</head>
<body>
<div class="register-container">
    <div class="register-card">
        <!-- Header -->
        <div class="register-header">
            <img src="{{ asset('img/logo.png') }}" alt="Logo">
            <h1>PT Madubaru</h1>
            <p>PG - PS MADUKISMO</p>
        </div>

        <!-- Body -->
        <div class="register-body">
            <h5><i class="bi bi-person-plus-fill me-2"></i> Registrasi Akun</h5>

            @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('register.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <!-- Kolom Kiri -->
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <i class="bi bi-credit-card input-icon"></i>
                            <input type="text" name="nik" class="form-control" placeholder="NIK" required>
                        </div>
                        <div class="form-group mb-3">
                            <i class="bi bi-person-badge input-icon"></i>
                            <input type="text" name="username" class="form-control" placeholder="Username" required>
                        </div>
                        <div class="form-group mb-3">
                            <i class="bi bi-lock input-icon"></i>
                            <div class="input-group">
                                <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
                                <span class="input-group-text" id="togglePassword" style="cursor: pointer;">
                                    <i class="bi bi-eye-slash" id="eyeIcon"></i>
                                </span>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <i class="bi bi-envelope input-icon"></i>
                            <input type="email" name="email" class="form-control" placeholder="Email" required>
                        </div>
                        <div class="form-group mb-3">
                            <i class="bi bi-person-circle input-icon"></i>
                            <input type="text" name="nama_lengkap" class="form-control" placeholder="Nama Lengkap" required>
                        </div>
                        <div class="form-group mb-3">
                            <i class="bi bi-phone input-icon"></i>
                            <input type="text" name="no_hp" class="form-control" placeholder="No HP" required>
                        </div>
                    </div>
                    <!-- Kolom Kanan -->
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <i class="bi bi-calendar-date input-icon"></i>
                            <input type="date" name="tgl_lahir" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <i class="bi bi-geo-alt input-icon"></i>
                            <textarea name="alamat" class="form-control" rows="2" placeholder="Alamat" required></textarea>
                        </div>
                        <div class="form-group mb-3">
                            <i class="bi bi-diagram-3 input-icon"></i>
                            <select name="id_divisi" class="form-select" required>
                                <option value="">--Pilih Divisi--</option>
                                <option value="1">SDM</option>
                                <option value="2">Keuangan</option>
                                <option value="3">Keamanan</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <i class="bi bi-briefcase input-icon"></i>
                            <select name="id_jabatan" class="form-select" required>
                                <option value="">--Pilih Jabatan--</option>
                                <option value="1">Kepala Divisi</option>
                                <option value="2">Staff</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <i class="bi bi-shield-lock input-icon"></i>
                            <input type="text" class="form-control" value="Karyawan" disabled>
                            <input type="hidden" name="role" value="Karyawan">
                        </div>
                        <div class="form-group mb-3">
                            <i class="bi bi-image input-icon"></i>
                            <input type="file" name="foto" class="form-control">
                        </div>
                    </div>
                </div>

                <input type="hidden" name="status" value="Aktif">
                <button type="submit" class="btn btn-register w-100"><i class="bi bi-check-circle me-2"></i>Daftar</button>
            </form>
        </div>

        <!-- Footer -->
        <div class="footer-text">
            © 2025 PT Madubaru - All Rights Reserved
        </div>
    </div>
</div>

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
