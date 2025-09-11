@extends('layout.master')

@section('content')
<div class="container mt-4">
    <h2>Edit Karyawan</h2>

    <form action="{{ route('karyawan.update', $karyawan->NIK) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- Kolom Kiri -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="NIK" class="form-label">NIK</label>
                    <input type="text" name="NIK" class="form-control" value="{{ $karyawan->NIK }}" readonly>
                </div>

                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" value="{{ $karyawan->username }}">
                </div>

                <div class="mb-3">
                    <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" class="form-control" value="{{ $karyawan->nama_lengkap }}" readonly>
                </div>

                <div class="mb-3">
                    <label for="no_hp" class="form-label">No HP</label>
                    <input type="text" name="no_hp" class="form-control" value="{{ $karyawan->no_hp }}">
                </div>
            </div>

            <!-- Kolom Kanan -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="tgl_lahir" class="form-label">Tanggal Lahir</label>
                    <input type="date" name="tgl_lahir" class="form-control" value="{{ $karyawan->tgl_lahir }}">
                </div>

                <div class="mb-3">
                    <label for="alamat" class="form-label">Alamat</label>
                    <textarea name="alamat" class="form-control">{{ $karyawan->alamat }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="id_divisi" class="form-label">Divisi</label>
                    <select name="id_divisi" class="form-select">
                        @foreach($departements as $divisi)
                            <option value="{{ $divisi->id_divisi }}"
                                {{ $karyawan->id_divisi == $divisi->id_divisi ? 'selected' : '' }}>
                                {{ $divisi->nama_divisi }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="id_jabatan" class="form-label">Jabatan</label>
                    <select name="id_jabatan" class="form-select">
                        @foreach($jabatans as $jabatan)
                            <option value="{{ $jabatan->id_jabatan }}"
                                {{ $karyawan->id_jabatan == $jabatan->id_jabatan ? 'selected' : '' }}>
                                {{ $jabatan->nama_jabatan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="role" class="form-label">Role</label>
                    <select name="role" class="form-select">
                        <option value="admin" {{ $karyawan->role == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="karyawan" {{ $karyawan->role == 'karyawan' ? 'selected' : '' }}>Karyawan</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="Aktif" {{ $karyawan->status == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="Nonaktif" {{ $karyawan->status == 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                        <option value="Resign" {{ $karyawan->status == 'Resign' ? 'selected' : '' }}>Resign</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="foto" class="form-label">Foto</label><br>
                    @if($karyawan->foto)
                        <img src="{{ asset('storage/'.$karyawan->foto) }}" alt="Foto Karyawan" class="mb-2" width="80">
                    @endif
                    <input type="file" name="foto" class="form-control">
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ url('/daftarKaryawan') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
