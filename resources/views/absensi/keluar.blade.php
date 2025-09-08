@extends('layout.karyawan')

@section('content')
<div class="card p-3">
    <h5 class="mb-3 text-center fw-bold">Absensi Keluar</h5>

    <!-- Map lokasi -->
    <div class="mb-3">
        <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3952.058123456789!2d112.123456!3d-7.123456!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd7f9c123456789%3A0xabcdefabcdef!2sNama%20Kantor!5e0!3m2!1sid!2sid!4v1691234567890!5m2!1sid!2sid"
            width="100%" height="250" style="border:0; border-radius:10px;"
            allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
        </iframe>
    </div>

    <!-- Tombol absensi -->
    <button class="btn btn-danger btn-lg w-100">
        <i class="bi bi-box-arrow-right"></i> Absen Keluar
    </button>
</div>
@endsection
