@extends('layouts.app')

@section('content')

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th width="5%" class="text-center py-3">No</th>
                        <th class="py-3">Nama Sub Kegiatan</th>
                        <th class="py-3">Induk Kegiatan</th>
                        <th width="8%" class="text-center py-3">Bulan</th>
                        <th width="12%" class="text-center py-3">Capaian</th>
                        <th width="12%" class="text-center py-3">Status</th>
                        <th width="12%" class="text-center py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($projects as $index => $p)
                    <tr>
                        <td class="text-center text-muted">{{ $projects->firstItem() + $index ?? $index + 1 }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $p->nama_sub_kegiatan }}</div>
                            <div class="small text-muted mt-1"><i class="bi bi-wallet2 me-1"></i>Pagu: Rp {{ number_format($p->pagu_anggaran, 0, ',', '.') }}</div>
                        </td>
                        <td>
                            <div class="small fw-bold text-secondary bg-light d-inline-block px-2 py-1 rounded-2 mb-1">
                                {{ Str::limit($p->opd->nama_opd ?? 'Dinas Terkait', 25) }}
                            </div>
                            <div class="small text-muted">{{ Str::limit($p->activity->nama_kegiatan ?? '-', 45) }}</div>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-dark border px-2 py-2 rounded-3 shadow-sm">
                                {{ DateTime::createFromFormat('!m', $p->bulan)->format('M') }}
                            </span>
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
                            @elseif($p->status == 'revision' || $p->status == 'revisi' || $p->status == 'rejected')
                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle px-3 py-2 rounded-pill">Revisi</span>
                            @else
                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning-subtle px-3 py-2 rounded-pill">Draft</span>
                            @endif
                        </td>

                        <td class="text-center">
                            @if($p->status == 'revision' || $p->status == 'revisi' || $p->status == 'rejected')
                            <button type="button" class="btn btn-sm btn-danger shadow-sm rounded-3 me-1 px-2 py-1" data-bs-toggle="modal" data-bs-target="#infoRevisiModal{{ $p->id }}" title="Lihat Catatan Revisi">
                                <i class="bi bi-chat-left-text-fill"></i>
                            </button>
                            @endif
                            
                            <a href="{{ route('projects.edit', $p->id) }}" class="btn btn-sm btn-warning text-dark fw-bold shadow-sm rounded-3 px-3 py-1" title="Edit Data">
                                <i class="bi bi-pencil-square me-1"></i> Edit
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox display-4 text-light mb-3 d-block"></i>
                            Belum ada laporan sub kegiatan yang Anda buat.
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

@foreach($projects as $p)
@if($p->status == 'revision' || $p->status == 'revisi' || $p->status == 'rejected')
<div class="modal fade" id="infoRevisiModal{{ $p->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-danger text-white border-bottom-0 pb-3 rounded-top-4">
                <h5 class="modal-title fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i>Catatan Revisi dari Admin</h5>
                <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="bg-danger bg-opacity-10 p-3 rounded-3 text-danger mb-3 border border-danger border-opacity-25" style="text-align: left;">
                    {!! nl2br(e($p->catatan_revisi)) !!}
                </div>
                <p class="text-muted small m-0">Silakan klik tombol <b>Edit</b> pada tabel untuk memperbaiki data sesuai catatan di atas.</p>
            </div>
            <div class="modal-footer border-top-0 pt-0 justify-content-center pb-4">
                <button type="button" class="btn btn-secondary px-4 rounded-3 fw-bold" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endif
@endforeach

@endsection