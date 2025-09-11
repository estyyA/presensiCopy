@extends('layout.master')

@section('content')
<div class="d-flex align-items-center justify-content-center min-vh-100">
    <div class="col-lg-10 col-xl-8">
        <div class="card shadow-lg border-0 rounded-4 w-100">
            <!-- Header -->
            <div class="card-header text-white rounded-top-4"
                 style="background: linear-gradient(135deg, #007bff, #00c6ff);">
                <h4 class="mb-0"><i class="fa fa-user-edit"></i> Edit Data Karyawan</h4>
            </div>

            <!-- Body -->
            <div class="card-body p-5">
                <form action="{{ route('karyawan.update', $karyawan->NIK) }}"
                      method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row g-4">
                        <!-- Kolom Kiri -->
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text" name="NIK" class="form-control"
                                       value="{{ $karyawan->NIK }}" readonly>
                                <label for="NIK">NIK</label>
                            </div>

                            <div class="form-floating mb-3">
                                <input type="text" name="username" class="form-control"
                                       value="{{ $karyawan->username }}">
                                <label for="username">Username</label>
                            </div>

                            <div class="form-floating mb-3">
                                <input type="text" name="nama_lengkap" class="form-control"
                                       value="{{ $karyawan->nama_lengkap }}" readonly>
                                <label for="nama_lengkap">Nama Lengkap</label>
                            </div>

                            <div class="form-floating mb-3">
                                <input type="text" name="no_hp" class="form-control"
                                       value="{{ $karyawan->no_hp }}">
                                <label for="no_hp">No HP</label>
                            </div>

                            <div class="form-floating mb-3">
                                <input type="date" name="tgl_lahir" class="form-control"
                                       value="{{ $karyawan->tgl_lahir }}">
                                <label for="tgl_lahir">Tanggal Lahir</label>
                            </div>
                        </div>

                        <!-- Kolom Kanan -->
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <textarea name="alamat" class="form-control" style="height: 100px">{{ $karyawan->alamat }}</textarea>
                                <label for="alamat">Alamat</label>
                            </div>

                            <div class="form-floating mb-3">
                                <select name="id_divisi" class="form-select">
                                    @foreach($departements as $divisi)
                                        <option value="{{ $divisi->id_divisi }}"
                                            {{ $karyawan->id_divisi == $divisi->id_divisi ? 'selected' : '' }}>
                                            {{ $divisi->nama_divisi }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="id_divisi">Divisi</label>
                            </div>

                            <div class="form-floating mb-3">
                                <select name="id_jabatan" class="form-select">
                                    @foreach($jabatans as $jabatan)
                                        <option value="{{ $jabatan->id_jabatan }}"
                                            {{ $karyawan->id_jabatan == $jabatan->id_jabatan ? 'selected' : '' }}>
                                            {{ $jabatan->nama_jabatan }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="id_jabatan">Jabatan</label>
                            </div>

                            <div class="form-floating mb-3">
                                <select name="role" class="form-select">
                                    <option value="admin" {{ $karyawan->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="karyawan" {{ $karyawan->role == 'karyawan' ? 'selected' : '' }}>Karyawan</option>
                                </select>
                                <label for="role">Role</label>
                            </div>

                            <div class="form-floating mb-3">
                                <select name="status" class="form-select">
                                    <option value="Aktif" {{ $karyawan->status == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="Nonaktif" {{ $karyawan->status == 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                                    <option value="Resign" {{ $karyawan->status == 'Resign' ? 'selected' : '' }}>Resign</option>
                                </select>
                                <label for="status">Status</label>
                            </div>

                            <div class="mb-3 text-center">
                                <label for="foto" class="form-label fw-bold d-block">Foto</label>
                                @if($karyawan->foto)
                                    <img src="{{ asset('storage/'.$karyawan->foto) }}"
                                         alt="Foto Karyawan"
                                         class="rounded-circle shadow mb-3"
                                         width="120" height="120">
                                @else
                                    <div class="bg-light rounded-circle shadow-sm mb-3 d-flex align-items-center justify-content-center"
                                         style="width: 120px; height: 120px; margin: auto;">
                                        <span class="text-muted">No Foto</span>
                                    </div>
                                @endif
                                <input type="file" name="foto" class="form-control">
                            </div>
                        </div>
                    </div>

                    <!-- Button -->
                    <div class="mt-4 text-end">
                        <a href="{{ url('/daftarKaryawan') }}" class="btn btn-outline-secondary px-4">
                            <i class="fa fa-arrow-left"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fa fa-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
