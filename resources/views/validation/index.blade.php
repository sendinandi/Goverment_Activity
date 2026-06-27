@extends('layouts.app')

@section('content')
<div class="card border-0 shadow-sm" style="border-radius: 15px;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="py-3 ps-4 border-0">Kegiatan</th>
                        <th class="py-3 border-0">Penginput (OPD)</th>
                        <th class="py-3 border-0">Anggaran</th>
                        <th class="py-3 border-0">Status</th>
                        <th class="py-3 pe-4 border-0 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($projects as $project)
                    <tr>
                        <td class="ps-4">
                            @php
                                $namaSub = trim($project->nama_sub_kegiatan);
                                $namaKeg = trim($project->nama_kegiatan);
                                
                                if (!empty($namaSub) && $namaSub !== '-') {
                                    $tampilNama = $namaSub;
                                } elseif (!empty($namaKeg) && $namaKeg !== '-') {
                                    $tampilNama = $namaKeg;
                                } else {
                                    // Tampilkan ini kalau di database beneran cuma tanda strip '-'
                                    $tampilNama = '⚠️ Judul Kegiatan Kosong (Isi di DB: -)'; 
                                }
                            @endphp

                            <div class="fw-bold text-dark">
                                {{ $tampilNama }}
                            </div>
                            
                            <div class="small text-muted mt-1">
                                <i class="bi bi-building me-1"></i> 
                                {{ $project->opd->nama_opd ?? $project->user->instansi ?? 'Instansi Tidak Diketahui' }}
                            </div>
                        </td>

                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 30px; height: 30px; font-size: 12px;">
                                    {{ substr($project->user->name ?? 'A', 0, 1) }}
                                </div>
                                <div>
                                    <div class="small fw-bold">{{ $project->user->name ?? 'Unknown' }}</div>
                                    <div class="small text-muted" style="font-size: 10px;">{{ $project->created_at->format('d M Y') }}</div>
                                </div>
                            </div>
                        </td>

                        <td class="fw-bold text-success">
                            Rp {{ number_format($project->pagu_anggaran, 0, ',', '.') }}
                        </td>
                        <td>
                            @if($project->status == 'approved')
                            <span class="badge bg-success">Disetujui</span>
                            @elseif($project->status == 'revision')
                            <span class="badge bg-danger">Revisi</span>
                            @else
                            <span class="badge bg-warning text-dark">Menunggu</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group shadow-sm" role="group">
                                <button type="button" class="btn btn-success btn-sm px-3"
                                    onclick="confirmApprove('{{ $project->id }}', '{{ addslashes($project->nama_kegiatan) }}')">
                                    <i class="bi bi-check-lg"></i>
                                </button>

                                <button type="button" class="btn btn-danger btn-sm px-3"
                                    onclick="confirmReject('{{ $project->id }}', '{{ addslashes($project->nama_kegiatan) }}')">
                                    <i class="bi bi-pencil-square"></i> Revisi
                                </button>
                            </div>

                            <form id="form-approve-{{ $project->id }}" action="{{ route('validation.approve', $project->id) }}" method="POST" style="display: none;">
                                @csrf @method('PATCH')
                            </form>

                            <form id="form-reject-{{ $project->id }}" action="{{ route('validation.revisi', $project->id) }}" method="POST" style="display: none;">
                                @csrf @method('PATCH')
                                <input type="hidden" name="catatan_revisi" id="catatan-input-{{ $project->id }}">
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">Data kosong.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-top">
            {{ $projects->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // 1. Popup Setuju
    function confirmApprove(id, nama) {
        Swal.fire({
            title: 'Setujui Proyek?',
            text: "Data \"" + nama + "\" akan dipublikasikan.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            confirmButtonText: 'Ya, Setujui'
        }).then((result) => {
            if (result.isConfirmed) document.getElementById('form-approve-' + id).submit();
        });
    }

    // 2. Popup Revisi
    function confirmReject(id, nama) {
        Swal.fire({
            title: 'Kirim Catatan Revisi',
            html: '<p class="small text-muted mb-2">Masukkan alasan kenapa data "' + nama + '" perlu diperbaiki:</p>',
            input: 'textarea',
            inputPlaceholder: 'Contoh: Anggaran tidak sesuai standar...',
            inputAttributes: {
                'aria-label': 'Tulis catatan revisi disini'
            },
            showCancelButton: true,
            confirmButtonText: 'Kirim Revisi',
            confirmButtonColor: '#dc3545',
            showLoaderOnConfirm: true,
            preConfirm: (catatan) => {
                if (!catatan || catatan.trim() === '') {
                    Swal.showValidationMessage('Catatan revisi wajib diisi!')
                }
                return catatan;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('catatan-input-' + id).value = result.value;
                document.getElementById('form-reject-' + id).submit();
            }
        });
    }
</script>
@endpush