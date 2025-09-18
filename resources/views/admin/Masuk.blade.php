@extends('layout.master')

@section('content')
<div class="d-flex justify-content-center mt-5">
    <div class="card shadow-lg p-4" style="width: 500px; border-radius: 15px;">
        <h5 class="text-center fw-bold mb-3">Absensi Masuk</h5>

        {{-- Peta Lokasi --}}
        <div class="mb-3">
            <iframe
                src="https://www.google.com/maps?q=-7.7707,110.3776&hl=es;z=14&output=embed"
                width="100%" height="250" style="border-radius: 10px;" allowfullscreen="" loading="lazy">
            </iframe>
            <p class="mt-2">
                <strong>Lokasi Anda:</strong>
                Jalan Karangwaru Lor, Karangwaru, Tegalrejo, Yogyakarta, Depok, Yogyakarta, Indonesia
            </p>
        </div>

        {{-- Jam Masuk --}}
        <form method="POST" action="{{ route('admin.presensi.submit') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Jam Masuk</label>
                <input type="text" class="form-control" value="{{ now()->format('H:i A') }}" readonly>
            </div>

            <button type="submit" name="tipe" value="masuk" class="btn btn-success w-100">
                ✅ Absen Masuk
            </button>
        </form>

        <div class="text-center mt-3 text-muted">
            Jam kerja: 07:00 - 15:30 WIB
        </div>
    </div>
</div>
@endsection
