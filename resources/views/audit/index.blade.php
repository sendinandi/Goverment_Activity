@extends('layouts.app')

@section('title', 'Audit Log Sistem')
@section('header', 'Audit Log Sistem') 

@section('content')

<div class="card border-0 shadow-sm" style="border-radius: 15px;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="py-3 ps-4 border-0">Waktu</th>
                        <th class="py-3 border-0">Pengguna (Aktor)</th>
                        <th class="py-3 border-0">Aksi</th>
                        <th class="py-3 pe-4 border-0">Keterangan Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td class="ps-4 text-muted small">
                            <i class="bi bi-calendar-event me-1"></i> {{ $log->created_at->format('d M Y') }}<br>
                            <i class="bi bi-clock me-1"></i> {{ $log->created_at->format('H:i:s') }} WIB
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $log->user->name ?? 'Sistem/Terhapus' }}</div>
                            <div class="small text-muted" style="font-size: 0.75rem;">
                                {{ $log->ip_address ?? 'IP Tidak Terdeteksi' }}
                            </div>
                        </td>
                        <td>
                            @if(str_contains(strtolower($log->aksi), 'setuju') || str_contains(strtolower($log->aksi), 'tambah'))
                                <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle px-2 py-1">{{ $log->aksi }}</span>
                            @elseif(str_contains(strtolower($log->aksi), 'revisi') || str_contains(strtolower($log->aksi), 'edit'))
                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning-subtle px-2 py-1">{{ $log->aksi }}</span>
                            @elseif(str_contains(strtolower($log->aksi), 'hapus'))
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle px-2 py-1">{{ $log->aksi }}</span>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle px-2 py-1">{{ $log->aksi }}</span>
                            @endif
                        </td>
                        <td class="pe-4 text-dark small">
                            {{ $log->keterangan }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">Belum ada rekaman aktivitas (Log kosong).</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-top">
            {{ $logs->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection