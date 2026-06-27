@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-end mb-3">
    <button type="button" class="btn btn-primary btn-sm fw-bold shadow-sm rounded-3 px-3 py-2" data-bs-toggle="modal" data-bs-target="#addModal">
        <i class="bi bi-plus-lg me-1"></i> Tambah Kegiatan
    </button>
</div>

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th width="5%" class="text-center py-3">No</th>
                        <th width="25%" class="py-3">Program / Bagian</th>
                        <th class="py-3">Nama Kegiatan Induk</th>
                        <th width="15%" class="text-center py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($activities as $index => $act)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            @if($act->program_id && $act->program)
                            <span class="badge bg-info bg-opacity-10 text-info border border-info rounded-pill px-3">{{ $act->program->nama_program }}</span>
                            @else
                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary rounded-pill px-3">Belum Diatur</span>
                            @endif
                        </td>
                        <td class="fw-bold text-dark">{{ $act->nama_kegiatan }}</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-warning text-white shadow-sm rounded-3 me-1" data-bs-toggle="modal" data-bs-target="#editModal{{ $act->id }}" title="Edit">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <form action="{{ route('activities.destroy', $act->id) }}"
                                    method="POST"
                                    class="d-inline delete-confirm-form"
                                    data-title="Hapus Master Kegiatan?"
                                    data-message="Yakin ingin menghapus kegiatan {{ $act->nama_kegiatan }}? Data yang sudah dihapus tidak dapat dikembalikan.">
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn btn-sm btn-danger shadow-sm rounded-3" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-4 text-muted">Belum ada data Master Kegiatan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@foreach($activities as $act)
<div class="modal fade" id="editModal{{ $act->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('activities.update', $act->id) }}" method="POST" class="modal-content border-0 shadow-lg rounded-4">
            @csrf @method('PUT')
            <div class="modal-header bg-light border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold text-dark">Edit Kegiatan</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">

                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Pilih Program / Bagian <span class="text-danger">*</span></label>
                    <select name="program_id" class="form-select rounded-3" required>
                        <option value="">-- Pilih Program / Bagian --</option>
                        @foreach($programs as $prog)
                        <option value="{{ $prog->id }}">
                            {{ $prog->nama_bagian }} - {{ $prog->nama_program }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-2">
                    <label class="form-label small fw-bold text-muted">Nama Kegiatan Induk</label>
                    <input type="text" name="nama_kegiatan" class="form-control rounded-3" value="{{ $act->nama_kegiatan }}" required>
                </div>

            </div>
            <div class="modal-footer bg-light border-top-0 pt-0">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary px-4 shadow-sm">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endforeach

<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('activities.store') }}" method="POST" class="modal-content border-0 shadow-lg rounded-4">
            @csrf
            <div class="modal-header bg-primary text-white border-bottom-0 pb-3 rounded-top-4">
                <h5 class="modal-title fw-bold">Tambah Kegiatan Baru</h5>
                <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">

                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Pilih Program / Bagian <span class="text-danger">*</span></label>
                    <select name="program_id" class="form-select rounded-3" required>
                        <option value="">-- Pilih Program / Bagian --</option>
                        @foreach($programs as $prog)
                        <option value="{{ $prog->id }}">
                            {{ $prog->nama_bagian }} - {{ $prog->nama_program }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-2">
                    <label class="form-label small fw-bold text-muted">Nama Kegiatan Induk</label>
                    <input type="text" name="nama_kegiatan" class="form-control rounded-3" placeholder="Contoh: Penyediaan Jasa Pelayanan Umum Kantor" required>
                </div>

            </div>
            <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary px-4 shadow-sm"><i class="bi bi-save me-1"></i> Simpan Data</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-body p-4 text-center">
                <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle bg-danger bg-opacity-10 text-danger"
                     style="width: 70px; height: 70px;">
                    <i class="bi bi-exclamation-triangle-fill fs-2"></i>
                </div>

                <h5 class="fw-bold text-dark mb-2" id="deleteConfirmModalLabel">
                    Konfirmasi Hapus
                </h5>

                <p class="text-muted mb-4" id="deleteConfirmMessage">
                    Apakah Anda yakin ingin menghapus data ini?
                </p>

                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-light border rounded-pill px-4" data-bs-dismiss="modal">
                        Batal
                    </button>

                    <button type="button" class="btn btn-danger rounded-pill px-4 fw-bold" id="deleteConfirmButton">
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let selectedDeleteForm = null;

        const deleteModalElement = document.getElementById('deleteConfirmModal');
        const deleteModal = new bootstrap.Modal(deleteModalElement);
        const deleteTitle = document.getElementById('deleteConfirmModalLabel');
        const deleteMessage = document.getElementById('deleteConfirmMessage');
        const deleteButton = document.getElementById('deleteConfirmButton');

        document.querySelectorAll('.delete-confirm-form').forEach(function (form) {
            form.addEventListener('submit', function (event) {
                event.preventDefault();

                selectedDeleteForm = form;

                deleteTitle.textContent = form.dataset.title || 'Konfirmasi Hapus';
                deleteMessage.textContent = form.dataset.message || 'Apakah Anda yakin ingin menghapus data ini?';

                deleteModal.show();
            });
        });

        deleteButton.addEventListener('click', function () {
            if (selectedDeleteForm) {
                deleteButton.disabled = true;
                deleteButton.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menghapus...';
                selectedDeleteForm.submit();
            }
        });

        deleteModalElement.addEventListener('hidden.bs.modal', function () {
            selectedDeleteForm = null;
            deleteButton.disabled = false;
            deleteButton.innerHTML = 'Ya, Hapus';
        });
    });
</script>
@endsection