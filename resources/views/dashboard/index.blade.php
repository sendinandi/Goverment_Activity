@extends('layouts.app')

@section('content')

@php
    use Illuminate\Support\Str;

    $chartLabels = $chartData->pluck('nama_sub_kegiatan')->map(function ($name) {
        return Str::limit($name, 24);
    })->toArray();

    $chartCapaianFisik = $chartData->pluck('capaian_fisik_normalized')->map(function ($value) {
        return round((float) $value, 2);
    })->toArray();

    $sisaAnggaran = $sisaAnggaran ?? max(($totalPagu ?? 0) - ($totalRealisasi ?? 0), 0);

    $persenKeuanganAsli = (float) ($persenKeuangan ?? 0);
    $persenSisaKeuangan = max(100 - $persenKeuanganAsli, 0);
@endphp

<style>
    .dashboard-title {
        font-weight: 800;
        color: #1f2937;
        letter-spacing: -0.4px;
    }

    .dashboard-subtitle {
        color: #6b7280;
        font-size: 0.92rem;
    }

    .filter-card,
    .card-soft,
    .kpi-card {
        border: 0;
        border-radius: 1.25rem;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.06);
        background: #fff;
    }

    .kpi-card {
        transition: transform 0.25s ease, box-shadow 0.25s ease;
        overflow: hidden;
        height: 100%;
    }

    .kpi-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 18px 35px rgba(15, 23, 42, 0.08);
    }

    .kpi-label {
        text-transform: uppercase;
        font-size: 0.72rem;
        letter-spacing: 0.7px;
        font-weight: 800;
        color: #8b95a5;
    }

    .kpi-value {
        font-size: 1.35rem;
        font-weight: 800;
        color: #111827;
        margin-bottom: 0;
    }

    .icon-box {
        width: 54px;
        height: 54px;
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
    }

    .card-header-custom {
        background-color: #fff;
        border-bottom: 1px solid #eef2f7;
        padding: 1.15rem 1.35rem;
        border-radius: 1.25rem 1.25rem 0 0 !important;
    }

    .table-custom thead th {
        text-transform: uppercase;
        font-size: 0.72rem;
        letter-spacing: 0.5px;
        font-weight: 800;
        color: #8b95a5;
        background-color: #f8fafc;
        border-bottom: 1px solid #eef2f7;
        white-space: nowrap;
    }

    .table-custom tbody td {
        vertical-align: middle;
    }

    .badge-soft {
        border-radius: 50rem;
        padding: 0.48rem 0.75rem;
        font-weight: 700;
        font-size: 0.75rem;
    }

    .progress-thin {
        height: 7px;
        border-radius: 50rem;
        background: #eef2f7;
    }

    .progress-thin .progress-bar {
        border-radius: 50rem;
    }

    .info-box {
        border-radius: 1.1rem;
        background: linear-gradient(135deg, rgba(13, 110, 253, 0.08), rgba(13, 202, 240, 0.08));
        border: 1px solid rgba(13, 110, 253, 0.08);
    }

    .small-muted {
        color: #6b7280;
        font-size: 0.82rem;
    }

    .gap-positive {
        color: #198754;
        font-weight: 800;
    }

    .gap-negative {
        color: #dc3545;
        font-weight: 800;
    }

    .modal-content {
        border-radius: 1.25rem;
    }
</style>

<div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div class="text-end">
        <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle px-3 py-2 rounded-pill">
            Data Ditampilkan: Approved
        </span>
    </div>
</div>

<div class="card filter-card mb-4">
    <div class="card-body p-3 p-md-4">
        <form action="{{ route('dashboard') }}" method="GET" class="row g-2 align-items-end">
            <div class="col-12 col-md-2">
                <label class="form-label small fw-bold text-muted mb-1">Tahun Anggaran</label>
                <select name="tahun" class="form-select form-select-sm rounded-3">
                    <option value="">Semua Tahun</option>
                    @foreach($tahuns as $t)
                        <option value="{{ $t }}" {{ request('tahun') == $t ? 'selected' : '' }}>
                            {{ $t }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-6 col-md-2">
                <label class="form-label small fw-bold text-muted mb-1">Bulan Awal</label>
                <select name="start_month" class="form-select form-select-sm rounded-3">
                    <option value="">Semua</option>
                    @foreach($bulanNama as $num => $nama)
                        <option value="{{ $num }}" {{ request('start_month') == $num ? 'selected' : '' }}>
                            {{ $nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-6 col-md-2">
                <label class="form-label small fw-bold text-muted mb-1">Bulan Akhir</label>
                <select name="end_month" class="form-select form-select-sm rounded-3">
                    <option value="">Semua</option>
                    @foreach($bulanNama as $num => $nama)
                        <option value="{{ $num }}" {{ request('end_month') == $num ? 'selected' : '' }}>
                            {{ $nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-md-3">
                <label class="form-label small fw-bold text-muted mb-1">Bidang / Bagian</label>
                <select name="bagian" id="filter_bagian" class="form-select form-select-sm select2-dashboard rounded-3">
                    <option value="">Semua Bidang / Bagian</option>
                    @foreach($bagianList as $bagian)
                        <option value="{{ $bagian }}" {{ request('bagian') == $bagian ? 'selected' : '' }}>
                            {{ $bagian }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-md-3">
                <label class="form-label small fw-bold text-muted mb-1">Program Utama</label>
                <select name="program_id" id="filter_program" class="form-select form-select-sm select2-dashboard rounded-3" disabled>
                    <option value="">Semua Program Utama</option>
                    @foreach($programs as $prog)
                        <option value="{{ $prog->id }}"
                                data-bagian="{{ $prog->nama_bagian }}"
                                {{ request('program_id') == $prog->id ? 'selected' : '' }}>
                            {{ $prog->nama_program }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-md-4">
                <label class="form-label small fw-bold text-muted mb-1">Kegiatan Induk</label>
                <select name="activity_id" id="filter_kegiatan" class="form-select form-select-sm select2-dashboard rounded-3" disabled>
                    <option value="">Semua Kegiatan Induk</option>
                    @foreach($activities as $act)
                        <option value="{{ $act->id }}"
                                data-program="{{ $act->program_id }}"
                                {{ request('activity_id') == $act->id ? 'selected' : '' }}>
                            {{ $act->nama_kegiatan }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-md-3">
                <label class="form-label small fw-bold text-muted mb-1">Cari Sub-Kegiatan</label>
                <input type="text"
                       name="q"
                       value="{{ request('q') }}"
                       class="form-control form-control-sm rounded-3"
                       placeholder="Masukkan kata kunci...">
            </div>

            <div class="col-12 col-md-3">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm rounded-3 px-3 w-100">
                        <i class="bi bi-search me-1"></i> Terapkan
                    </button>

                    <a href="{{ route('dashboard') }}" class="btn btn-light btn-sm rounded-3 px-3 border" title="Reset Filter">
                        <i class="bi bi-arrow-clockwise"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="info-box p-3 p-md-4 mb-4">
    <div class="row g-3 align-items-center">
        <div class="col-md-8">
            <div class="fw-bold text-dark mb-1">
                <i class="bi bi-info-circle text-primary me-1"></i>
                Ringkasan Periode Monitoring
            </div>
            <div class="small-muted">
                Data kegiatan disusun berdasarkan <strong>tahun anggaran</strong>, sedangkan pemantauan dilakukan secara
                <strong>bulanan</strong>. Dashboard hanya menampilkan data yang sudah divalidasi dan berstatus
                <strong>approved</strong>.
            </div>
        </div>

        <div class="col-md-4">
            <div class="row g-2 text-center">
                <div class="col-6">
                    <div class="small-muted">Tahun</div>
                    <div class="fw-bold text-dark">{{ $tahunTampil ?? 'Semua Tahun' }}</div>
                </div>

                <div class="col-6">
                    <div class="small-muted">Periode</div>
                    <div class="fw-bold text-dark">{{ $periodeMonitoring }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card kpi-card">
            <div class="card-body p-4 d-flex justify-content-between align-items-center">
                <div>
                    <div class="kpi-label mb-2">Total Pagu Anggaran</div>
                    <div class="kpi-value">Rp {{ number_format($totalPagu, 0, ',', '.') }}</div>
                    <div class="small-muted mt-2">{{ $tahunTampil ?? 'Semua Tahun' }}</div>
                </div>
                <div class="icon-box bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-wallet2"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card kpi-card">
            <div class="card-body p-4 d-flex justify-content-between align-items-center">
                <div>
                    <div class="kpi-label mb-2">Total Realisasi</div>
                    <div class="kpi-value">Rp {{ number_format($totalRealisasi, 0, ',', '.') }}</div>
                    <div class="small-muted mt-2">Periode {{ $periodeMonitoring }}</div>
                </div>
                <div class="icon-box bg-success bg-opacity-10 text-success">
                    <i class="bi bi-cash-stack"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card kpi-card">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <div class="kpi-label mb-2">Serapan Keuangan</div>
                        <div class="kpi-value">{{ number_format($persenKeuanganAsli, 2) }}%</div>
                    </div>
                    <div class="icon-box bg-info bg-opacity-10 text-info">
                        <i class="bi bi-pie-chart-fill"></i>
                    </div>
                </div>

                <div class="progress progress-thin">
                    <div class="progress-bar bg-info" style="width: {{ min($persenKeuanganAsli, 100) }}%"></div>
                </div>

                <div class="small-muted mt-2">Realisasi terhadap pagu anggaran</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card kpi-card">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <div class="kpi-label mb-2">Rata-rata Capaian Fisik</div>
                        <div class="kpi-value">{{ number_format($avgFisik, 2) }}%</div>
                    </div>
                    <div class="icon-box bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-bar-chart-line-fill"></i>
                    </div>
                </div>

                <div class="progress progress-thin">
                    <div class="progress-bar bg-warning" style="width: {{ min($avgFisik, 100) }}%"></div>
                </div>

                <div class="small-muted mt-2">Rata-rata capaian data approved</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card card-soft h-100">
            <div class="card-body p-4">
                <div class="kpi-label mb-2">Sesuai Target Bulan Ini</div>
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="fw-bold text-success mb-0">{{ $jumlahSesuaiTarget }}</h3>
                    <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle rounded-pill px-3 py-2">
                        {{ number_format($persenDataSesuaiTarget, 1) }}%
                    </span>
                </div>
                <div class="small-muted mt-2">
                    Realisasi fisik sudah mencapai atau melebihi target periode.
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-soft h-100">
            <div class="card-body p-4">
                <div class="kpi-label mb-2">Ada Kendala</div>
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="fw-bold text-danger mb-0">{{ $jumlahAdaKendala }}</h3>
                    <i class="bi bi-exclamation-triangle text-danger fs-3"></i>
                </div>
                <div class="small-muted mt-2">
                    Realisasi belum mencapai target dan terdapat kendala yang dilaporkan.
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-soft h-100">
            <div class="card-body p-4">
                <div class="kpi-label mb-2">Dalam Pemantauan</div>
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="fw-bold text-warning mb-0">{{ $jumlahDalamPemantauan }}</h3>
                    <i class="bi bi-eye text-warning fs-3"></i>
                </div>
                <div class="small-muted mt-2">
                    Realisasi belum mencapai target, tetapi belum ada kendala yang dicatat.
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card card-soft h-100">
            <div class="card-header card-header-custom d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h6 class="fw-bold text-dark m-0">Capaian Fisik per Sub-Kegiatan</h6>
                    <div class="small-muted mt-1">
                        Menampilkan capaian fisik maksimal 100% agar grafik lebih mudah dibaca.
                    </div>
                </div>
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">
                    {{ $projects->count() }} Data
                </span>
            </div>
            <div class="card-body p-4">
                <div style="height: 330px;">
                    <canvas id="chartBarFisik"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card card-soft h-100">
            <div class="card-header card-header-custom">
                <h6 class="fw-bold text-dark m-0">Serapan Anggaran</h6>
                <div class="small-muted mt-1">
                    Perbandingan realisasi anggaran terhadap pagu.
                </div>
            </div>

            <div class="card-body p-4">
                <div style="height: 250px;">
                    <canvas id="chartPieKeuangan"></canvas>
                </div>

                <div class="row text-center mt-3">
                    <div class="col-6">
                        <div class="small-muted">Realisasi</div>
                        <div class="fw-bold text-success">{{ number_format($persenKeuanganAsli, 2) }}%</div>
                    </div>

                    <div class="col-6">
                        <div class="small-muted">Sisa</div>
                        <div class="fw-bold text-dark">{{ number_format($persenSisaKeuangan, 2) }}%</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card card-soft mb-4">
    <div class="card-header card-header-custom">
        <h6 class="fw-bold text-dark m-0">
            Tren Bulanan Target vs Realisasi Fisik {{ $tahunTampil ?? 'Semua Tahun' }}
        </h6>
        <div class="small-muted mt-1">
            Menampilkan perkembangan rata-rata target dan realisasi fisik berdasarkan periode bulan yang dipilih.
            Bulan tanpa data approved ditampilkan sebagai 0%.
        </div>
    </div>

    <div class="card-body p-4">
        <div style="height: 330px;">
            <canvas id="chartLineTren"></canvas>
        </div>

        <div class="mt-3 small text-muted">
            <i class="bi bi-info-circle me-1 text-primary"></i>
            Grafik tren digunakan untuk membantu pimpinan membaca perkembangan capaian bulanan.
            Nilai 0% pada bulan tertentu menunjukkan bahwa belum ada data approved pada bulan tersebut.
        </div>
    </div>
</div>

<div class="card card-soft mb-5">
    <div class="card-header card-header-custom d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h6 class="m-0 fw-bold text-dark">Rincian Data Sub-Kegiatan</h6>
            <div class="small-muted mt-1">
                Tabel menampilkan target, realisasi, selisih, status kondisi, serta detail kendala dan tindak lanjut.
            </div>
        </div>

        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">
            Total: {{ $projects->count() }} Data
        </span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover table-custom align-middle mb-0">
            <thead>
                <tr>
                    <th class="ps-4 py-3">Sub-Kegiatan</th>
                    <th class="py-3 text-center">Periode</th>
                    <th class="py-3">Penanggung Jawab</th>
                    <th class="py-3 text-center">Target</th>
                    <th class="py-3 text-center">Realisasi</th>
                    <th class="py-3 text-center">Selisih</th>
                    <th class="py-3 text-center">Capaian</th>
                    <th class="py-3 text-center">Kondisi</th>
                    <th class="py-3 text-center pe-4">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($projects as $p)
                    @php
                        $targetPersen = (float) $p->target_persen_normalized;
                        $realisasiPersen = (float) $p->realisasi_persen_normalized;
                        $capaianFisik = (float) $p->capaian_fisik_normalized;
                        $gap = $realisasiPersen - $targetPersen;

                        $sesuaiTarget = $realisasiPersen >= $targetPersen;
                        $adaKendala = !$sesuaiTarget && !empty($p->kendala) && $p->kendala !== '-';

                        if ($sesuaiTarget) {
                            $badgeClass = 'bg-success bg-opacity-10 text-success border border-success-subtle';
                            $badgeText = 'Sesuai Target';
                            $badgeIcon = 'bi-check-circle';
                        } elseif ($adaKendala) {
                            $badgeClass = 'bg-danger bg-opacity-10 text-danger border border-danger-subtle';
                            $badgeText = 'Ada Kendala';
                            $badgeIcon = 'bi-exclamation-triangle';
                        } else {
                            $badgeClass = 'bg-warning bg-opacity-10 text-warning border border-warning-subtle';
                            $badgeText = 'Dalam Pemantauan';
                            $badgeIcon = 'bi-eye';
                        }
                    @endphp

                    <tr>
                        <td class="ps-4 py-3">
                            <div class="fw-bold text-dark mb-1">{{ Str::limit($p->nama_sub_kegiatan, 55) }}</div>
                            <div class="small-muted">
                                <i class="bi bi-wallet2 me-1"></i>
                                Pagu: Rp {{ number_format($p->pagu_anggaran, 0, ',', '.') }}
                            </div>
                        </td>

                        <td class="py-3 text-center">
                            <div class="fw-bold text-dark">{{ $bulanNama[(int) $p->bulan] ?? '-' }}</div>
                            <div class="small-muted">{{ $p->tahun_anggaran }}</div>
                        </td>

                        <td class="py-3">
                            <div class="fw-semibold text-dark small">{{ $p->penanggung_jawab ?? '-' }}</div>
                            <div class="small-muted">{{ Str::limit($p->activity->nama_kegiatan ?? '-', 42) }}</div>
                        </td>

                        <td class="py-3 text-center">
                            <div class="fw-bold text-dark">{{ number_format($targetPersen, 2) }}%</div>
                            <div class="small-muted">
                                {{ $p->target_fisik_bulan_ini }} {{ $p->satuan }}
                            </div>
                        </td>

                        <td class="py-3 text-center">
                            <div class="fw-bold text-success">{{ number_format($realisasiPersen, 2) }}%</div>
                            <div class="small-muted">
                                {{ $p->realisasi_fisik_bulan_ini }} {{ $p->satuan }}
                            </div>
                        </td>

                        <td class="py-3 text-center">
                            <span class="{{ $gap >= 0 ? 'gap-positive' : 'gap-negative' }}">
                                {{ $gap >= 0 ? '+' : '' }}{{ number_format($gap, 2) }}%
                            </span>
                            <div class="small-muted">Realisasi - Target</div>
                        </td>

                        <td class="py-3 text-center" style="min-width: 140px;">
                            <div class="fw-bold text-dark mb-1">{{ number_format($capaianFisik, 2) }}%</div>
                            <div class="progress progress-thin">
                                <div class="progress-bar bg-primary" style="width: {{ min($capaianFisik, 100) }}%"></div>
                            </div>
                        </td>

                        <td class="py-3 text-center">
                            <span class="badge badge-soft {{ $badgeClass }}">
                                <i class="bi {{ $badgeIcon }} me-1"></i>{{ $badgeText }}
                            </span>

                            <div class="mt-2">
                                <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle rounded-pill px-2 py-1">
                                    {{ ucfirst($p->status) }}
                                </span>
                            </div>
                        </td>

                        <td class="py-3 text-center pe-4">
                            <button class="btn btn-sm btn-outline-primary rounded-pill px-3"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalDetail{{ $p->id }}">
                                <i class="bi bi-info-circle me-1"></i> Detail
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="bi bi-folder-x display-4 text-light mb-3 d-block"></i>
                            Belum ada data sub-kegiatan untuk filter ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@foreach($projects as $p)
    <div class="modal fade" id="modalDetail{{ $p->id }}" tabindex="-1" aria-labelledby="modalLabel{{ $p->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-light border-0">
                    <div>
                        <h6 class="modal-title fw-bold text-dark" id="modalLabel{{ $p->id }}">
                            <i class="bi bi-file-earmark-text text-primary me-2"></i>
                            Detail Kendala dan Tindak Lanjut
                        </h6>
                        <div class="small-muted mt-1">
                            {{ $p->nama_sub_kegiatan }}
                        </div>
                    </div>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-4">
                                <div class="small-muted">Periode</div>
                                <div class="fw-bold text-dark">
                                    {{ $bulanNama[(int) $p->bulan] ?? '-' }} {{ $p->tahun_anggaran }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-4">
                                <div class="small-muted">Target Fisik</div>
                                <div class="fw-bold text-dark">
                                    {{ number_format((float) $p->target_persen_normalized, 2) }}%
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-4">
                                <div class="small-muted">Realisasi Fisik</div>
                                <div class="fw-bold text-success">
                                    {{ number_format((float) $p->realisasi_persen_normalized, 2) }}%
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="fw-bold text-uppercase text-muted small mb-2">
                            <i class="bi bi-exclamation-triangle text-warning me-1"></i>
                            Kendala yang Dihadapi
                        </h6>
                        <div class="p-3 bg-light rounded-4 text-dark">
                            {{ ($p->kendala && $p->kendala !== '-') ? $p->kendala : 'Tidak ada kendala yang dilaporkan pada kegiatan ini.' }}
                        </div>
                    </div>

                    <div>
                        <h6 class="fw-bold text-uppercase text-muted small mb-2">
                            <i class="bi bi-tools text-success me-1"></i>
                            Tindak Lanjut
                        </h6>
                        <div class="p-3 bg-light rounded-4 text-dark">
                            {{ ($p->tindak_lanjut && $p->tindak_lanjut !== '-') ? $p->tindak_lanjut : 'Belum ada tindak lanjut yang dicatat.' }}
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pb-4 pt-0 pe-4">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
@endforeach

@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    $(document).ready(function() {
        $('.select2-dashboard').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });

        const opsiProgram = $('#filter_program option').clone();
        const opsiKegiatan = $('#filter_kegiatan option').clone();

        $('#filter_bagian').on('change', function() {
            const bagian = $(this).val();

            $('#filter_program').empty().append(opsiProgram);
            $('#filter_kegiatan').empty().append(opsiKegiatan).attr('disabled', true).val('').trigger('change');

            if (bagian) {
                $('#filter_program').removeAttr('disabled');

                $('#filter_program option').each(function() {
                    if ($(this).val() !== '' && $(this).data('bagian') !== bagian) {
                        $(this).remove();
                    }
                });
            } else {
                $('#filter_program').attr('disabled', true);
            }

            $('#filter_program').val('').trigger('change');
        });

        $('#filter_program').on('change', function() {
            const programId = $(this).val();

            $('#filter_kegiatan').empty().append(opsiKegiatan);

            if (programId) {
                $('#filter_kegiatan').removeAttr('disabled');

                $('#filter_kegiatan option').each(function() {
                    if ($(this).val() !== '' && String($(this).data('program')) !== String(programId)) {
                        $(this).remove();
                    }
                });
            } else {
                $('#filter_kegiatan').attr('disabled', true);
            }

            $('#filter_kegiatan').val('').trigger('change');
        });

        if ($('#filter_bagian').val()) {
            $('#filter_bagian').trigger('change');

            setTimeout(function() {
                $('#filter_program').val("{{ request('program_id') }}").trigger('change');

                setTimeout(function() {
                    $('#filter_kegiatan').val("{{ request('activity_id') }}").trigger('change');
                }, 150);
            }, 150);
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        Chart.defaults.font.family = "'Poppins', sans-serif";
        Chart.defaults.color = '#6b7280';

        const chartLabels = @json($chartLabels);
        const chartCapaianFisik = @json($chartCapaianFisik);

        const trendLabels = @json($trendLabels);
        const trendTarget = @json($trendTarget);
        const trendRealisasi = @json($trendRealisasi);
        const trendHasData = @json($trendHasData);

        const persenKeuangan = {{ number_format((float) $persenKeuanganAsli, 4, '.', '') }};
        const persenSisaKeuangan = {{ number_format((float) $persenSisaKeuangan, 4, '.', '') }};

        const barCanvas = document.getElementById('chartBarFisik');

        if (barCanvas) {
            const ctxBar = barCanvas.getContext('2d');
            const gradientBar = ctxBar.createLinearGradient(0, 0, 0, 360);

            gradientBar.addColorStop(0, '#0d6efd');
            gradientBar.addColorStop(1, '#9ec5fe');

            new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: 'Capaian Fisik (%)',
                        data: chartCapaianFisik,
                        backgroundColor: gradientBar,
                        borderRadius: 10,
                        barPercentage: 0.55
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Capaian Fisik: ' + context.parsed.y + '%';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                maxRotation: 0,
                                autoSkip: true
                            }
                        },
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                }
                            },
                            grid: {
                                borderDash: [5, 5]
                            }
                        }
                    }
                }
            });
        }

        const pieCanvas = document.getElementById('chartPieKeuangan');

        const centerTextPlugin = {
            id: 'centerTextPlugin',
            beforeDraw(chart) {
                const { width, height, ctx } = chart;

                ctx.save();
                ctx.font = '700 20px Poppins, sans-serif';
                ctx.fillStyle = '#111827';
                ctx.textBaseline = 'middle';
                ctx.textAlign = 'center';
                ctx.fillText(persenKeuangan.toFixed(2) + '%', width / 2, height / 2 - 8);

                ctx.font = '400 12px Poppins, sans-serif';
                ctx.fillStyle = '#6b7280';
                ctx.fillText('Serapan', width / 2, height / 2 + 16);
                ctx.restore();
            }
        };

        if (pieCanvas) {
            const visualRealisasi = persenKeuangan > 0 && persenKeuangan < 1 ? 1 : persenKeuangan;
            const visualSisa = Math.max(100 - visualRealisasi, 0);

            new Chart(pieCanvas.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Realisasi', 'Sisa Anggaran'],
                    datasets: [{
                        data: [visualRealisasi, visualSisa],
                        backgroundColor: ['#198754', '#eef2f7'],
                        borderWidth: 0,
                        cutout: '74%'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    if (context.label === 'Realisasi') {
                                        return 'Realisasi: ' + persenKeuangan.toFixed(2) + '%';
                                    }

                                    return 'Sisa: ' + persenSisaKeuangan.toFixed(2) + '%';
                                }
                            }
                        }
                    }
                },
                plugins: [centerTextPlugin]
            });
        }

        const lineCanvas = document.getElementById('chartLineTren');

        if (lineCanvas) {
            const ctxLine = lineCanvas.getContext('2d');

            new Chart(ctxLine, {
                type: 'line',
                data: {
                    labels: trendLabels,
                    datasets: [
                        {
                            label: 'Target Fisik (%)',
                            data: trendTarget,
                            borderColor: '#f0ad4e',
                            backgroundColor: 'transparent',
                            borderWidth: 3,
                            borderDash: [6, 6],
                            pointRadius: 5,
                            pointHoverRadius: 7,
                            tension: 0.35
                        },
                        {
                            label: 'Realisasi Fisik (%)',
                            data: trendRealisasi,
                            borderColor: '#198754',
                            backgroundColor: 'rgba(25, 135, 84, 0.10)',
                            fill: true,
                            borderWidth: 3,
                            pointRadius: 5,
                            pointHoverRadius: 7,
                            tension: 0.35
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                boxWidth: 8
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const index = context.dataIndex;

                                    if (!trendHasData[index]) {
                                        return context.dataset.label + ': belum ada data approved';
                                    }

                                    return context.dataset.label + ': ' + context.parsed.y + '%';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                }
                            },
                            grid: {
                                borderDash: [5, 5]
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush