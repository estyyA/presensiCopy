<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Karyawan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: #f5f7fa;
            font-size: 14px;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 500px; /* supaya fokus di mobile */
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
            margin-bottom: 15px;
            background: #fff;
        }
        .profile-card {
            background: linear-gradient(135deg, #0954cb, #3a8ef6);
            color: #fff;
            text-align: center;
            padding: 20px;
        }
        .profile-card img {
            border: 2px solid #fff;
            margin-bottom: 10px;
        }
        .btn-lg {
            font-size: 16px;
            padding: 12px;
            border-radius: 12px;
        }
        .btn-block {
            display: block;
            width: 100%;
        }
        .history-list li {
            font-size: 13px;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
    </style>
</head>
<body>
    <div class="container py-3 px-2">
        @yield('content')
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
