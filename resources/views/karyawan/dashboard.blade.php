@extends('layout.karyawan')

@section('content')
<div class="card profile-card p-3 mb-3 text-center">
    <div class="d-flex flex-column align-items-center position-relative">

        <!-- Foto Profil -->
        <img id="previewFoto"
            src="{{ asset(optional(Auth::user())->foto ?? 'img/profile.png') }}"
            class="rounded-circle mb-2"
            width="90" height="90"
            alt="Foto Karyawan"
            style="object-fit: cover;">

        <!-- Tombol Edit -->
        <input type="file" id="inputFoto" class="d-none" accept="image/*">
        <label for="inputFoto" class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle p-1" style="cursor: pointer;">
            <i class="bi bi-pencil-fill"></i>
        </label>

        <h6 class="mb-0 mt-2">{{ Auth::user()->name ?? 'Resty Aryanti' }}</h6>
        <small class="text-muted">{{ Auth::user()->bidang ?? 'HRD' }}</small>

        <!-- Tombol Logout -->

    </div>
</div>

<!-- Modal Preview Foto -->
<div class="modal fade" id="modalPreview" tabindex="-1" aria-labelledby="modalPreviewLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form action="{{ route('karyawan.uploadFoto') }}" method="POST" enctype="multipart/form-data" id="formFoto">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="modalPreviewLabel">Preview Foto</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center">
          <img id="modalFoto" src="" class="rounded-circle" width="150" height="150" style="object-fit: cover;">
          <input type="hidden" name="fotoBase64" id="fotoBase64">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Script Preview + Modal -->
<script>
document.getElementById('inputFoto').addEventListener('change', function(e) {
    let file = e.target.files[0];
    if(file){
        let reader = new FileReader();
        reader.onload = function(e){
            document.getElementById('modalFoto').src = e.target.result;
            document.getElementById('fotoBase64').value = e.target.result;

            // tampilkan modal
            var modal = new bootstrap.Modal(document.getElementById('modalPreview'));
            modal.show();
        };
        reader.readAsDataURL(file);
    }
});
</script>

<!-- Jam & Absensi -->
<div class="card p-3 mb-3 text-center">
    <h5 class="fw-bold">Live Attendance</h5>
    <h2 class="text-primary">08:34 AM</h2>
    <p class="mb-1">Fri, 14 April 2023</p>
    <p class="text-muted small">Office Hours: 08:00 AM - 05:00 PM</p>

    <div class="d-flex justify-content-between">
        <a href="{{ url('/absensi/masuk') }}" class="btn btn-primary btn-lg">Masuk</a>
        <a href="{{ url('/absensi/keluar') }}" class="btn btn-danger btn-lg">Keluar</a>
    </div>
</div>

<!-- Riwayat Presensi -->
<div class="card p-3">
    <h6 class="fw-bold">Attendance History</h6>
    <ul class="list-unstyled mt-2 mb-0">
        <li class="d-flex justify-content-between small border-bottom py-2">
            <span>Fri, 14 April 2023</span>
            <span>08:00 AM - 05:00 PM</span>
        </li>
        <li class="d-flex justify-content-between small border-bottom py-2 text-danger">
            <span>Thu, 13 April 2023</span>
            <span>08:45 AM - 05:00 PM</span>
        </li>
        <li class="d-flex justify-content-between small border-bottom py-2">
            <span>Wed, 12 April 2023</span>
            <span>07:55 AM - 05:00 PM</span>
        </li>
    </ul>
</div>
<form action="{{ route('logout') }}" method="POST" class="mt-3">
    @csrf
    <button type="submit" class="btn btn-outline-danger btn-sm">
        <i class="bi bi-box-arrow-right"></i> Logout
    </button>
</form>
@endsection
