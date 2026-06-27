@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-end mb-3">
    <form action="{{ route('projects.history') }}" method="GET" class="d-flex gap-2 align-items-center">
        <span class="text-muted small fw-bold me-1">Arsip Tahun:</span>
        <select name="tahun" class="form-select form-select-sm shadow-sm border-0 rounded-3 px-3 py-2 bg-white" onchange="this.form.submit()" style="width: 180px;">
            <option value="">- Semua Tahun Lama -</option>
            @foreach($tahuns as $t)
            <option value="{{ $t }}" {{ request('tahun') == $t ? 'selected' : '' }}>Tahun Anggaran {{ $t }}</option>
            @endforeach
        </select>
        <a href="{{ route('projects.history') }}" class="btn btn-sm btn-white text-danger border-0 shadow-sm rounded-3 px-3 py-2" title="Reset Filter">
            <i class="bi bi-arrow-clockwise"></i>
        </a>
    </form>
</div>

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th width="8%" class="text-center py-3">Tahun</th>
                        <th class="py-3">Nama Sub Kegiatan</th>
                        <th width="15%" class="text-center py-3">Target Fisik</th>
                        <th width="15%" class="text-center py-3">Realisasi Fisik</th>
                        <th width="18%" class="py-3">Keuangan (Rp)</th>
                        <th width="10%" class="text-center py-3">Capaian</th>
                        <th width="12%" class="text-center py-3">Status Akhir</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($projects as $p)
                    <tr>
                        <td class="text-center">
                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle px-3 py-2 rounded-3 shadow-sm">
                                {{ $p->tahun_anggaran }}
                            </span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $p->nama_sub_kegiatan }}</div>
                            <div class="small text-muted mt-1">{{ Str::limit($p->activity->nama_kegiatan ?? '-', 45) }}</div>
                        </td>
                        <td class="text-center small">
                            <span class="fw-semibold text-dark">{{ $p->target_fisik_bulan_ini }}</span> <span class="text-muted">{{ $p->satuan }}</span>
                        </td>
                        <td class="text-center small">
                            <span class="fw-semibold text-success">{{ $p->realisasi_fisik_bulan_ini }}</span> <span class="text-muted">{{ $p->satuan }}</span>
                        </td>
                        <td>
                            <div class="small text-muted mb-1">Pagu: <span class="text-dark fw-semibold">{{ number_format($p->pagu_anggaran, 0, ',', '.') }}</span></div>
                            <div class="small text-muted">Real: <span class="text-success fw-bold">{{ number_format($p->realisasi_anggaran, 0, ',', '.') }}</span></div>
                        </td>
                        <td class="text-center">
                            @if($p->capaian_fisik >= 100)
                            <span class="fw-bold text-success">{{ number_format($p->capaian_fisik, 1) }}%</span>
                            @else
                            <span class="fw-bold text-warning">{{ number_format($p->capaian_fisik, 1) }}%</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($p->status == 'approved')
                            <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle px-3 py-2 rounded-pill">Disetujui</span>
                            @elseif($p->status == 'rejected')
                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle px-3 py-2 rounded-pill">Ditolak</span>
                            @else
                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle px-3 py-2 rounded-pill">Ditutup</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-clock-history display-4 text-light mb-3 d-block"></i>
                            Belum ada arsip kegiatan di tahun-tahun sebelumnya.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(method_exists($projects, 'hasPages') && $projects->hasPages())
    <div class="card-footer bg-white border-top-0 py-3 px-4 d-flex justify-content-end rounded-bottom-4">
        {{ $projects->links() }}
    </div>
    @endif
</div>

@endsection