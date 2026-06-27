@extends('layouts.app')
@section('content')

<style>
    /* CSS Khusus Cetak */
    @media print {
        .no-print, .sidebar, .top-bar, footer, .btn, .form-control, .form-select {
            display: none !important;
        }
        .main-content { margin: 0 !important; padding: 0 !important; width: 100% !important; }
        .card { border: none !important; box-shadow: none !important; }
        table { width: 100%; border: 1px solid #000; border-collapse: collapse; font-size: 8pt; }
        th, td { border: 1px solid #000 !important; padding: 3px !important; }
        .badge { border: 1px solid #000; color: #000 !important; background: none !important; padding: 0; }
    }
    
    /* Mencegah teks kendala terlalu berantakan */
    .text-wrap-column {
        min-width: 150px;
        max-width: 200px;
        word-wrap: break-word;
        white-space: normal !important;
        line-height: 1.2;
    }
</style>

<div class="d-flex justify-content-end mb-4">
    <div class="no-print">
        <a href="{{ route('reports.excel', request()->query()) }}" class="btn btn-sm btn-success shadow-sm fw-bold">
            <i class="bi bi-file-earmark-excel-fill me-1"></i> Export Excel
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4 no-print">
    <div class="card-body bg-light rounded">
        <form action="{{ route('reports.index') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="small fw-bold text-muted mb-1">Bidang / Bagian</label>
                <select name="bagian" class="form-select form-select-sm select2-rekap">
                    <option value="">- Semua Bagian -</option>
                    @foreach(\App\Models\Program::select('nama_bagian')->distinct()->get() as $bg)
                    <option value="{{ $bg->nama_bagian }}" {{ request('bagian') == $bg->nama_bagian ? 'selected' : '' }}>
                        {{ $bg->nama_bagian }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label class="small fw-bold text-muted mb-1">Program Utama</label>
                <select name="program_id" class="form-select form-select-sm select2-rekap">
                    <option value="">- Semua Program -</option>
                    @foreach(\App\Models\Program::all() as $prog)
                    <option value="{{ $prog->id }}" {{ request('program_id') == $prog->id ? 'selected' : '' }}>
                        {{ Str::limit($prog->nama_program, 50) }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold">
                    <i class="bi bi-search"></i> Tampilkan
                </button>
            </div>

            <div class="col-md-1">
                <a href="{{ route('reports.index') }}" class="btn btn-light border btn-sm w-100" title="Reset Filter">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle mb-0" width="100%">
                <thead class="bg-primary text-white text-center small align-middle">
                    <tr>
                        <th rowspan="2" width="3%">No</th>
                        <th rowspan="2" width="15%">Sub Kegiatan & Penanggung Jawab</th>
                        <th colspan="3">Fisik</th>
                        <th colspan="2">Keuangan (Rp)</th>
                        <th rowspan="2" width="5%">Capaian<br>(%)</th>
                        <th rowspan="2" width="12%">Kendala</th>
                        <th rowspan="2" width="12%">Tindak Lanjut</th>
                        <th rowspan="2" width="8%" class="no-print">Aksi</th>
                    </tr>
                    <tr>
                        <th width="5%">Target</th>
                        <th width="5%">Realiz.</th>
                        <th width="5%">Satuan</th>
                        <th width="10%">Pagu Anggaran</th>
                        <th width="10%">Realisasi</th>
                    </tr>
                </thead>
                <tbody class="small">
                    @forelse($projects as $index => $p)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>
                            <div class="fw-bold text-dark" style="font-size: 0.85rem;">{{ $p->nama_sub_kegiatan }}</div>
                            <div class="badge bg-light text-secondary border mt-1" style="font-size: 0.7rem;">
                                {{ $p->penanggung_jawab ?? '-' }}
                            </div>
                        </td>

                        <td class="text-center">{{ $p->target_fisik_bulan_ini }}</td>
                        <td class="text-center fw-bold">{{ $p->realisasi_fisik_bulan_ini }}</td>
                        <td class="text-center text-muted">{{ $p->satuan }}</td>

                        <td class="text-end">{{ number_format($p->pagu_anggaran, 0, ',', '.') }}</td>
                        <td class="text-end fw-semibold text-primary">{{ number_format($p->realisasi_anggaran, 0, ',', '.') }}</td>

                        <td class="text-center">
                            @php
                            $capaian = $p->capaian_fisik;
                            $bg = $capaian >= 100 ? 'success' : ($capaian >= 80 ? 'info' : ($capaian >= 50 ? 'warning' : 'danger'));
                            @endphp
                            <span class="badge bg-{{ $bg }}">{{ number_format($capaian, 1) }}%</span>
                        </td>

                        <td class="text-wrap-column small">
                            {{ $p->kendala ?? '-' }}
                        </td>

                        <td class="text-wrap-column small italic text-muted">
                            {{ $p->tindak_lanjut ?? '-' }}
                        </td>

                        <td class="text-center no-print">
                            <a href="{{ route('reports.show', $p->id) }}" class="btn btn-sm btn-info text-white p-1" style="font-size: 0.7rem;" title="Lihat Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="text-center py-5 text-muted fst-italic">
                            Tidak ada data laporan yang ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($projects->count() > 0)
                <tfoot class="bg-light fw-bold small">
                    <tr>
                        <td colspan="5" class="text-end pe-3">TOTAL KESELURUHAN</td>
                        <td class="text-end">{{ number_format($projects->sum('pagu_anggaran'), 0, ',', '.') }}</td>
                        <td class="text-end">{{ number_format($projects->sum('realisasi_anggaran'), 0, ',', '.') }}</td>
                        <td class="text-center">
                            {{ number_format($projects->avg('capaian_fisik'), 1) }}%
                        </td>
                        <td></td>
                        <td></td>
                        <td class="no-print"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2-rekap').select2({
            theme: 'bootstrap-5'
        });
    });
</script>
@endpush