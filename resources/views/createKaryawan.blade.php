@extends('layout.master')

@section('content')
<div class="container mt-4">
    <h2>Tambah Karyawan</h2>

    <form action="{{ route('karyawan.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="NIK" class="form-label">NIK</label>
            <input type="text" name="NIK" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" name="username" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
            <input type="text" name="nama_lengkap" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="no_hp" class="form-label">No HP</label>
            <input type="text" name="no_hp" class="form-control">
        </div>

        <div class="mb-3">
            <label for="tgl_lahir" class="form-label">Tanggal Lahir</label>
            <input type="date" name="tgl_lahir" class="form-control">
        </div>

        <div class="mb-3">
            <label for="alamat" class="form-label">Alamat</label>
            <textarea name="alamat" class="form-control"></textarea>
        </div>

        <div class="mb-3">
            <label for="role" class="form-label">Role</label>
            <select name="role" class="form-select">
                <option value="admin">Admin</option>
                <option value="karyawan">Karyawan</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="{{ url('/daftarKaryawan') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
