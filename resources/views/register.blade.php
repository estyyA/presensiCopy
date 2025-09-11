<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Akun</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body,
        html {
            height: 100%;
            margin: 0;
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(to bottom, #1976d2, #0d47a1);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .register-container {
            width: 100%;
            max-width: 900px;
            margin: 20px;
        }

        .register-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.25);
            overflow: hidden;
        }

        .register-header {
            background: linear-gradient(to right, #1976d2, #0d47a1);
            color: white;
            padding: 20px;
            text-align: center;
        }

        .register-header img {
            width: 80px;
            margin-bottom: 10px;
        }

        .register-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }

        .register-header p {
            margin: 0;
            font-size: 14px;
            color: #e3f2fd;
        }

        .register-body {
            padding: 30px 30px 50px 30px;
            /* extra padding bawah */
            max-height: 75vh;
            overflow-y: auto;
        }

        .register-body h5 {
            text-align: center;
            margin-bottom: 25px;
            font-weight: 600;
            color: #0d47a1;
        }

        .form-label {
            font-weight: 500;
            color: #444;
        }

        .form-control,
        .form-select {
            border-radius: 8px;
        }

        .btn-register {
            background: linear-gradient(to right, #1976d2, #0d47a1);
            color: white;
            font-weight: bold;
            border-radius: 8px;
            padding: 12px;
            transition: all 0.3s ease;
            margin-top: 15px;
            margin-bottom: 10px;
            /* ruang bawah */
        }

        .btn-register:hover {
            background: linear-gradient(to right, #1565c0, #0b3c91);
            transform: translateY(-2px);
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        }

        .footer-text {
            text-align: center;
            font-size: 13px;
            color: #666;
            padding: 10px;
            background: #f9f9f9;
            border-top: 1px solid #eee;
        }

        /* scrollbar */
        .register-body::-webkit-scrollbar {
            width: 6px;
        }

        .register-body::-webkit-scrollbar-thumb {
            background: #1976d2;
            border-radius: 10px;
        }

        .register-body::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
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
                <form action="{{ route('register.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <!-- Kolom Kiri -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="NIK" class="form-label">NIK</label>
                                <input type="text" name="NIK" id="NIK" class="form-control"
                                    placeholder="Masukkan NIK" required>
                            </div>
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" name="username" id="username" class="form-control"
                                    placeholder="Masukkan username" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <input type="password" name="password" id="password" class="form-control"
                                        placeholder="Buat password" required>
                                    <span class="input-group-text" id="togglePassword" style="cursor: pointer;">
                                        <i class="bi bi-eye-slash" id="eyeIcon"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                                <input type="text" name="nama_lengkap" id="nama_lengkap" class="form-control"
                                    placeholder="Masukkan nama lengkap" required>
                            </div>
                            <div class="mb-3">
                                <label for="no_hp" class="form-label">No HP</label>
                                <input type="text" name="no_hp" id="no_hp" class="form-control"
                                    placeholder="08xxxxxxxxxx" required>
                            </div>
                        </div>

                        <!-- Kolom Kanan -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tgl_lahir" class="form-label">Tanggal Lahir</label>
                                <input type="date" name="tgl_lahir" id="tgl_lahir" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="alamat" class="form-label">Alamat</label>
                                <textarea name="alamat" id="alamat" class="form-control" rows="2" placeholder="Masukkan alamat lengkap"
                                    required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="divisi" class="form-label">Divisi</label>
                                <select name="role divisi" id="role" class="form-select" required>
                                    <option value="">-- Pilih Divisi --</option>
                                    <option value="sdm">SDM</option>
                                    <option value="keuangan">Keuangan</option>
                                    <option value="kebun">Kebun</option>
                                    <option value="mekanik">Mekanik</option>
                                    <option value="keamanan">Keamanan</option>
                                    <option value="lapangan">Lapangan</option>
                                    <option value="konsumsi">Konsumsi</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="id_jabatan" class="form-label">Nama Jabatan</label>
                                <select name="role jabatan divisi" id="role" class="form-select" required>
                                    <option value="">-- Jabatan Divisi --</option>
                                    <option value="kepala divisi">Kepala Divisi</option>
                                    <option value="staff">Staff</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="role" class="form-label">Role</label>
                                <input type="text" id="role" class="form-control" value="Karyawan" disabled>
                                <input type="hidden" name="role" value="karyawan">
                            </div>
                            <div class="mb-3">
                                <label for="foto" class="form-label">Foto</label>
                                <input type="file" name="foto" id="foto" class="form-control">
                            </div>
                        </div>
                    </div>

                    <!-- Tombol -->
                    <button type="submit" class="btn btn-register w-100">
                        <i class="bi bi-check-circle me-2"></i>Daftar
                    </button>
                </form>
            </div>

            <!-- Footer -->
            <div class="footer-text">
                © 2025 PT Madubaru - All Rights Reserved
            </div>
        </div>
    </div>

    <!-- Script Show/Hide Password -->
    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');
        const eyeIcon = document.querySelector('#eyeIcon');

        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            eyeIcon.classList.toggle('bi-eye');
            eyeIcon.classList.toggle('bi-eye-slash');
        });
    </script>
</body>

</html>
