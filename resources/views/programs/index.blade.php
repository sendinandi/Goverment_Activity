@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-end align-items-center mb-4">
    <button type="button" class="btn btn-primary btn-sm fw-bold shadow-sm rounded-3 px-3 py-2" data-bs-toggle="modal" data-bs-target="#addModal">
        <i class="bi bi-plus-lg me-1"></i> Tambah Program
    </button>
</div>

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th width="5%" class="text-center py-3">No</th>
                        <th width="30%" class="py-3">Nama Bidang / Bagian</th>
                        <th class="py-3">Nama Program Utama</th>
                        <th width="15%" class="text-center py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($programs as $index => $prog)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td><span class="badge bg-primary bg-opacity-10 text-primary border border-primary rounded-pill px-3">{{ $prog->nama_bagian }}</span></td>
                        <td class="fw-bold text-dark">{{ $prog->nama_program }}</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-warning text-white shadow-sm rounded-3 me-1" data-bs-toggle="modal" data-bs-target="#editModal{{ $prog->id }}" title="Edit">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <form action="{{ route('programs.destroy', $prog->id) }}"
                                    method="POST"
                                    class="d-inline delete-confirm-form"
                                    data-title="Hapus Program & Bagian?"
                                    data-message="Yakin ingin menghapus data ini? Kegiatan yang terhubung dengan program ini mungkin akan terpengaruh.">
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
                        <td colspan="4" class="text-center py-4 text-muted">Belum ada data Program & Bagian.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@foreach($programs as $prog)
<div class="modal fade" id="editModal{{ $prog->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('programs.update', $prog->id) }}" method="POST" class="modal-content border-0 shadow-lg rounded-4">
            @csrf @method('PUT')
            <div class="modal-header bg-light border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold text-dark">Edit Program & Bagian</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">

                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Nama Bidang / Bagian <span class="text-danger">*</span></label>
                    <input type="text" name="nama_bagian" class="form-control rounded-3" value="{{ $prog->nama_bagian }}" placeholder="Contoh: Bidang E-Government" required>
                </div>

                <div class="mb-2">
                    <label class="form-label small fw-bold text-muted">Nama Program Utama <span class="text-danger">*</span></label>
                    <input type="text" name="nama_program" class="form-control rounded-3" value="{{ $prog->nama_program }}" placeholder="Contoh: Program Aplikasi Informatika" required>
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
        <form action="{{ route('programs.store') }}" method="POST" class="modal-content border-0 shadow-lg rounded-4">
            @csrf
            <div class="modal-header bg-primary text-white border-bottom-0 pb-3 rounded-top-4">
                <h5 class="modal-title fw-bold">Tambah Program & Bagian</h5>
                <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">

                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Nama Bidang / Bagian <span class="text-danger">*</span></label>
                    <input type="text" name="nama_bagian" class="form-control rounded-3" placeholder="Contoh: Bidang Informasi Publik" required>
                </div>

                <div class="mb-2">
                    <label class="form-label small fw-bold text-muted">Nama Program Utama <span class="text-danger">*</span></label>
                    <input type="text" name="nama_program" class="form-control rounded-3" placeholder="Contoh: Program Komunikasi Publik" required>
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