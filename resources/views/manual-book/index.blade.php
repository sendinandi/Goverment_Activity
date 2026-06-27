@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h3 class="fw-bold mb-1">Manual Book SIPDA</h3>
                <p class="text-muted mb-0">
                    Panduan penggunaan Sistem Informasi Pengelolaan Data Kegiatan Pemerintahan.
                </p>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ asset('docs/manual-book-sipda.pdf') }}" target="_blank" class="btn btn-outline-primary rounded-3">
                    <i class="bi bi-box-arrow-up-right me-1"></i> Buka PDF
                </a>

                <a href="{{ route('manual-book.download') }}" class="btn btn-primary rounded-3">
                    <i class="bi bi-download me-1"></i> Download
                </a>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <iframe 
                src="{{ asset('docs/manual-book-sipda.pdf') }}#toolbar=1"
                style="width: 100%; height: 80vh; border: none; border-radius: 16px;">
            </iframe>
        </div>
    </div>

</div>
@endsection