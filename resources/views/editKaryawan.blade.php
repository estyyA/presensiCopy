@extends('layout.master')

@section('content')
<div class="d-flex align-items-center justify-content-center min-vh-100">
    <div class="col-lg-10 col-xl-8">
        <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
            <!-- Header -->
            <div class="card-header text-white rounded-top-4 py-3"
                 style="background: linear-gradient(135deg, #4facfe, #0e3132); box-shadow: 0 3px 10px rgba(0,0,0,0.15);">
                <h4 class="mb-0 fw-bold">
                    <i class="fa fa-user-edit me-2"></i> Edit Data Karyawan
                </h4>
            </div>

            <!-- Body -->
            <div class="card-body p-5 bg-light">
                <form action="{{ route('karyawan.update', $karyawan->NIK) }}"
                      method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row g-4">
                        <!-- Kolom Kiri -->
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text" name="NIK" class="form-control shadow-sm"
                                       value="{{ $karyawan->NIK }}" readonly>
                                <label for="NIK">NIK</label>
                            </div>

                            <div class="form-floating mb-3">
                                <input type="text" name="username" class="form-control shadow-sm"
                                       value="{{ $karyawan->username }}">
                                <label for="username">Username</label>
                            </div>

                            <div class="form-floating mb-3">
                                <input type="text" name="nama_lengkap" class="form-control shadow-sm"
                                       value="{{ $karyawan->nama_lengkap }}" readonly>
                                <label for="nama_lengkap">Nama Lengkap</label>
                            </div>

                            <div class="form-floating mb-3">
                                <input type="text" name="no_hp" class="form-control shadow-sm"
                                       value="{{ $karyawan->no_hp }}">
                                <label for="no_hp">No HP</label>
                            </div>

                            <div class="form-floating mb-3">
                                <input type="date" name="tgl_lahir" class="form-control shadow-sm"
                                       value="{{ $karyawan->tgl_lahir }}">
                                <label for="tgl_lahir">Tanggal Lahir</label>
                            </div>
                        </div>

                        <!-- Kolom Kanan -->
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <textarea name="alamat" class="form-control shadow-sm" style="height: 100px">{{ $karyawan->alamat }}</textarea>
                                <label for="alamat">Alamat</label>
                            </div>

                            <!-- Divisi -->
                            <div class="form-floating mb-3">
                                <select name="id_divisi" id="id_divisi" class="form-select shadow-sm">
                                    <option value="">-- Pilih Divisi --</option>
                                    @foreach($departements as $divisi)
                                        <option value="{{ $divisi->id_divisi }}"
                                            {{ $karyawan->id_divisi == $divisi->id_divisi ? 'selected' : '' }}>
                                            {{ $divisi->nama_divisi }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="id_divisi">Divisi</label>
                            </div>

                            <!-- Sub Divisi -->
                            <div class="form-floating mb-3">
                                <select name="id_subdivisi" id="id_subdivisi" class="form-select shadow-sm">
                                    <option value="">-- Pilih Sub Divisi --</option>
                                    @foreach($subdepartements as $sub)
                                        <option value="{{ $sub->id_subdivisi }}"
                                            data-divisi="{{ $sub->id_divisi }}"
                                            {{ $karyawan->id_subdivisi == $sub->id_subdivisi ? 'selected' : '' }}>
                                            {{ $sub->nama_subdivisi }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="id_subdivisi">Sub Divisi</label>
                            </div>

                            <!-- Jabatan -->
                            <div class="form-floating mb-3">
                                <select name="id_jabatan" class="form-select shadow-sm">
                                    @foreach($jabatans as $jabatan)
                                        <option value="{{ $jabatan->id_jabatan }}"
                                            {{ $karyawan->id_jabatan == $jabatan->id_jabatan ? 'selected' : '' }}>
                                            {{ $jabatan->nama_jabatan }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="id_jabatan">Jabatan</label>
                            </div>

                            {{-- Role disembunyikan, default karyawan --}}
                            <input type="hidden" name="role" value="karyawan">

                            <div class="form-floating mb-3">
                                <select name="status" class="form-select shadow-sm">
                                    <option value="Aktif" {{ $karyawan->status == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="Nonaktif" {{ $karyawan->status == 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                                    <option value="Resign" {{ $karyawan->status == 'Resign' ? 'selected' : '' }}>Resign</option>
                                </select>
                                <label for="status">Status</label>
                            </div>

                            <!-- Foto -->
                            <div class="mb-3 text-center">
                                <label for="foto" class="form-label fw-bold d-block">Foto</label>
                                @if($karyawan->foto)
                                    <div class="position-relative d-inline-block">
                                        <img src="{{ asset('storage/'.$karyawan->foto) }}"
                                             alt="Foto Karyawan"
                                             class="rounded-circle shadow mb-3 border border-3 border-white"
                                             style="width:120px; height:120px; object-fit:cover; transition: transform 0.3s;">
                                    </div>
                                @else
                                    <div class="bg-white rounded-circle shadow-sm mb-3 d-flex align-items-center justify-content-center border"
                                         style="width: 120px; height: 120px; margin: auto;">
                                        <span class="text-muted">No Foto</span>
                                    </div>
                                @endif
                                <input type="file" name="foto" class="form-control shadow-sm">
                            </div>
                        </div>
                    </div>

                    <!-- Button -->
                    <div class="mt-4 text-end">
                        <a href="{{ url('/daftarKaryawan') }}" class="btn btn-outline-secondary px-4 me-2">
                            <i class="fa fa-arrow-left"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary px-4"
                                style="background: linear-gradient(135deg, #4facfe, #00f2fe); border: none;">
                            <i class="fa fa-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- 🔽 AJAX Filter Sub Divisi Berdasarkan Divisi --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const divisiSelect = document.getElementById('id_divisi');
    const subSelect = document.getElementById('id_subdivisi');

    divisiSelect.addEventListener('change', function() {
        let idDivisi = this.value;
        subSelect.innerHTML = '<option value="">Memuat...</option>';

        fetch('/get-subdivisi/' + idDivisi)
            .then(res => res.json())
            .then(data => {
                subSelect.innerHTML = '<option value="">-- Pilih Sub Divisi --</option>';
                data.forEach(sub => {
                    subSelect.innerHTML += `<option value="${sub.id_subdivisi}">${sub.nama_subdivisi}</option>`;
                });
            })
            .catch(err => {
                subSelect.innerHTML = '<option value="">Gagal memuat data</option>';
                console.error(err);
            });
    });
});
</script>
@endsection
