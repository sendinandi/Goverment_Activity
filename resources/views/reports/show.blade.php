@extends('layouts.app')

@section('content')
<style>
    /* CSS Khusus Cetak Satuan */
    @media print {
        body * {
            visibility: hidden;
            /* Sembunyikan semua element lain (sidebar, navbar) */
        }

        #printable-area,
        #printable-area * {
            visibility: visible;
            /* Hanya munculkan area laporan */
        }

        #printable-area {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            padding: 20px;
        }

        .no-print {
            display: none !important;
        }
    }

    .label-field {
        font-weight: bold;
        color: #555;
        width: 200px;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4 no-print">
    <a href="{{ route('reports.index') }}" class="btn btn-light border fw-bold">
        <i class="bi bi-arrow-left me-2"></i> Kembali ke Rekap
    </a>
    <button onclick="window.print()" class="btn btn-primary fw-bold px-4">
        <i class="bi bi-printer-fill me-2"></i> Cetak Lembar Ini
    </button>
</div>

<div id="printable-area" class="card border-0 shadow-sm" style="border-radius: 15px;">
    <div class="card-body p-5">

        <div class="text-center mb-5">
            <div class="d-flex justify-content-center align-items-center gap-3 mb-3">
                <img src="{{ asset('images/logo-bekasi.png') }}" alt="Logo" width="60">
                <div class="text-start">
                    <h4 class="fw-bold m-0 text-uppercase">Pemerintah Kota Bekasi</h4>
                    <p class="m-0 text-muted small">Sistem Informasi Pengendalian & Monitoring Pembangunan</p>
                </div>
            </div>
            <hr style="border: 2px solid #000; opacity: 1;">
            <h5 class="fw-bold text-uppercase mt-4">Lembar Detail Kegiatan Pembangunan</h5>
            <p class="text-muted">Nomor Registrasi: #PRJ-{{ str_pad($project->id, 5, '0', STR_PAD_LEFT) }}</p>
        </div>

        <div class="row">
            <div class="col-12">
                <table class="table table-borderless align-middle">
                    <tr>
                        <td class="label-field">Nama Kegiatan</td>
                        <td class="fw-bold fs-5">: {{ $project->nama_kegiatan }}</td>
                    </tr>
                    <tr>
                        <td class="label-field">Lokasi / Kecamatan</td>
                        <td>: {{ $project->district->nama_kecamatan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label-field">Sektor Pembangunan</td>
                        <td>: <span class="badge bg-primary">{{ $project->sector->nama_sektor ?? '-' }}</span></td>
                    </tr>
                    <tr>
                        <td class="label-field">Tahun Anggaran</td>
                        <td>: {{ $project->tahun_anggaran }}</td>
                    </tr>
                    <tr>
                        <td class="label-field">Input Oleh</td>
                        <td>: {{ $project->user->name ?? 'Admin' }} ({{ $project->created_at->format('d M Y') }})</td>
                    </tr>
                </table>
            </div>
        </div>

        <hr class="my-4 border-secondary opacity-25">

        <div class="row g-4">
            <div class="col-md-6">
                <div class="p-4 bg-light rounded border h-100">
                    <h6 class="fw-bold text-primary mb-3"><i class="bi bi-wallet2 me-2"></i>Informasi Anggaran</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Pagu Anggaran</span>
                        <span class="fw-bold">Rp {{ number_format($project->pagu_anggaran, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Realisasi</span>
                        <span class="fw-bold">Rp {{ number_format($project->realisasi_anggaran, 0, ',', '.') }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Sisa Anggaran</span>
                        <span class="fw-bold text-success">Rp {{ number_format($project->pagu_anggaran - $project->realisasi_anggaran, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="p-4 bg-light rounded border h-100">
                    <h6 class="fw-bold text-primary mb-3"><i class="bi bi-graph-up-arrow me-2"></i>Status & Progres</h6>
                    <div class="mb-3">
                        <span class="d-block text-muted mb-1">Status Verifikasi</span>
                        @if($project->status == 'approved')
                        <span class="badge bg-success px-3 py-2">DISETUJUI / APPROVED</span>
                        @else
                        <span class="badge bg-warning text-dark px-3 py-2">{{ strtoupper($project->status) }}</span>
                        @endif
                    </div>
                    <div>
                        <span class="d-block text-muted mb-1">Progres Fisik Lapangan</span>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar 
                                {{ $project->progres_fisik >= 80 ? 'bg-success' : ($project->progres_fisik >= 50 ? 'bg-warning text-dark' : 'bg-danger') }}"
                                role="progressbar"
                                style="width: {{ $project->progres_fisik }}%">
                                {{ $project->progres_fisik }}%
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-5 pt-5">
            <div class="col-4 text-center offset-8">
                <p class="mb-5">Bekasi, {{ date('d F Y') }}<br>Mengetahui,</p>
                <br><br>
                <p class="fw-bold text-decoration-underline mb-0">Kepala Bappelitbangda</p>
                <small class="text-muted">NIP. 19820301 200501 1 003</small>
            </div>
        </div>

    </div>
</div>
@endsection