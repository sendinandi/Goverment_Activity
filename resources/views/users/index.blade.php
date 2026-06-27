@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-end mb-3">
    <button type="button" class="btn btn-primary btn-sm fw-bold shadow-sm rounded-3 px-3 py-2" data-bs-toggle="modal" data-bs-target="#addModal">
        <i class="bi bi-person-plus-fill me-1"></i> Tambah Pengguna
    </button>
</div>

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th width="5%" class="text-center py-3">No</th>
                        <th class="py-3">Nama & Email Lengkap</th>
                        <th class="py-3 text-center">Hak Akses (Role)</th>
                        <th class="py-3">Perangkat Daerah (OPD)</th>
                        <th class="py-3 text-center">Status Akun</th>
                        <th width="15%" class="text-center py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($users as $index => $u)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $u->name }}</div>
                            <div class="small text-muted"><i class="bi bi-envelope me-1"></i> {{ $u->email }}</div>
                        </td>
                        <td class="text-center">
                            @if($u->role == 'admin' || $u->role == 'admin_opd')
                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle px-3 py-2 rounded-pill">Administrator</span>
                            @elseif($u->role == 'verifikator')
                            <span class="badge bg-info bg-opacity-10 text-info border border-info-subtle px-3 py-2 rounded-pill">Verifikator</span>
                            @elseif($u->role == 'pimpinan')
                            <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle px-3 py-2 rounded-pill">Pimpinan</span>
                            @else
                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle px-3 py-2 rounded-pill">Operator OPD</span>
                            @endif
                        </td>
                        <td>
                            <div class="fw-semibold text-dark small">{{ $u->opd->nama_opd ?? 'Dinas Terpusat / Eksekutif' }}</div>
                        </td>

                        <td class="text-center">
                            @if($u->is_approved)
                            <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle px-3 py-2 rounded-pill">
                                <i class="bi bi-shield-check me-1"></i> Aktif
                            </span>
                            @else
                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning-subtle px-3 py-2 rounded-pill">
                                <i class="bi bi-hourglass-split me-1"></i> Menunggu
                            </span>
                            @endif
                        </td>

                        <td class="text-center">
                            @if(!$u->is_approved)
                            <form action="{{ route('users.approve', $u->id) }}"
                                    method="POST"
                                    class="d-inline approve-confirm-form"
                                    data-title="Aktifkan Akun?"
                                    data-message="Yakin ingin menyetujui dan mengaktifkan akun {{ $u->name }}? Setelah disetujui, pengguna dapat login ke sistem sesuai role yang diberikan.">
                                @csrf
                                @method('PATCH')

                                <button type="submit"
                                        class="btn btn-sm btn-success shadow-sm rounded-3 me-1"
                                        title="Aktifkan Akun">
                                    <i class="bi bi-check-circle"></i>
                                </button>
                            </form>
                            @endif

                            <button class="btn btn-sm btn-warning text-dark fw-bold shadow-sm rounded-3 me-1" data-bs-toggle="modal" data-bs-target="#editModal{{ $u->id }}" title="Edit">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <form action="{{ route('users.destroy', $u->id) }}"
                                    method="POST"
                                    class="d-inline delete-confirm-form"
                                    data-title="Hapus Pengguna?"
                                    data-message="Yakin ingin menghapus akun {{ $u->name }}? Data akun yang dihapus tidak dapat dikembalikan.">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                    class="btn btn-sm btn-danger shadow-sm rounded-3"
                                    title="Hapus"
                                    {{ auth()->id() == $u->id ? 'disabled' : '' }}>
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">Belum ada data pengguna.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@foreach($users as $u)
<div class="modal fade" id="editModal{{ $u->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $u->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('users.update', $u->id) }}" method="POST" class="modal-content border-0 shadow-lg rounded-4">
            @csrf @method('PUT')
            <div class="modal-header bg-light border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold text-dark" id="editModalLabel{{ $u->id }}">Edit Data Pengguna</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Nama Lengkap</label>
                    <input type="text" name="name" class="form-control rounded-3" value="{{ $u->name }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Email Login</label>
                    <input type="email" name="email" class="form-control rounded-3" value="{{ $u->email }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Password Baru <span class="fw-normal">(Opsional)</span></label>
                    <input type="password" name="password" class="form-control rounded-3" placeholder="Isi jika ingin ganti password...">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Hak Akses (Role)</label>
                    <select name="role" class="form-select rounded-3" required>
                        <option value="operator" {{ $u->role == 'operator' ? 'selected' : '' }}>Operator OPD</option>
                        <option value="verifikator" {{ $u->role == 'verifikator' ? 'selected' : '' }}>Verifikator</option>
                        <option value="pimpinan" {{ $u->role == 'pimpinan' ? 'selected' : '' }}>Pimpinan (Dashboard Only)</option>
                        <option value="admin_opd" {{ ($u->role == 'admin' || $u->role == 'admin_opd') ? 'selected' : '' }}>Administrator Utama</option>
                    </select>
                </div>
                <div class="mb-0">
                    <label class="form-label small fw-bold text-muted">Perangkat Daerah (OPD)</label>
                    <select name="opd_id" class="form-select rounded-3">
                        <option value="">- Kosongkan jika Admin / Pimpinan -</option>
                        @foreach($opds as $opd)
                        <option value="{{ $opd->id }}" {{ $u->opd_id == $opd->id ? 'selected' : '' }}>{{ Str::limit($opd->nama_opd, 40) }}</option>
                        @endforeach
                    </select>
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

<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('users.store') }}" method="POST" class="modal-content border-0 shadow-lg rounded-4">
            @csrf
            <div class="modal-header bg-primary text-white border-bottom-0 pb-3 rounded-top-4">
                <h5 class="modal-title fw-bold" id="addModalLabel">Tambah Pengguna Baru</h5>
                <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Nama Lengkap</label>
                    <input type="text" name="name" class="form-control rounded-3" placeholder="Masukkan nama..." required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Email Login</label>
                    <input type="email" name="email" class="form-control rounded-3" placeholder="email@bekasikota.go.id" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Password Login</label>
                    <input type="password" name="password" class="form-control rounded-3" placeholder="Minimal 8 karakter..." required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Hak Akses (Role)</label>
                    <select name="role" class="form-select rounded-3" required>
                        <option value="operator">Operator OPD</option>
                        <option value="verifikator">Verifikator</option>
                        <option value="pimpinan">Pimpinan (Dashboard Only)</option>
                        <option value="admin_opd">Administrator Utama</option>
                    </select>
                </div>
                <div class="mb-0">
                    <label class="form-label small fw-bold text-muted">Perangkat Daerah (OPD)</label>
                    <select name="opd_id" class="form-select rounded-3">
                        <option value="">- Kosongkan jika Admin / Pimpinan -</option>
                        @foreach($opds as $opd)
                        <option value="{{ $opd->id }}">{{ Str::limit($opd->nama_opd, 40) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary px-4 shadow-sm"><i class="bi bi-save me-1"></i> Simpan</button>
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
<div class="modal fade" id="approveConfirmModal" tabindex="-1" aria-labelledby="approveConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-body p-4 text-center">
                <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle bg-success bg-opacity-10 text-success"
                     style="width: 70px; height: 70px;">
                    <i class="bi bi-check-circle-fill fs-2"></i>
                </div>

                <h5 class="fw-bold text-dark mb-2" id="approveConfirmModalLabel">
                    Aktifkan Akun?
                </h5>

                <p class="text-muted mb-4" id="approveConfirmMessage">
                    Apakah Anda yakin ingin mengaktifkan akun ini?
                </p>

                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-light border rounded-pill px-4" data-bs-dismiss="modal">
                        Batal
                    </button>

                    <button type="button" class="btn btn-success rounded-pill px-4 fw-bold" id="approveConfirmButton">
                        Ya, Aktifkan
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let selectedApproveForm = null;

        const approveModalElement = document.getElementById('approveConfirmModal');
        const approveModal = new bootstrap.Modal(approveModalElement);
        const approveTitle = document.getElementById('approveConfirmModalLabel');
        const approveMessage = document.getElementById('approveConfirmMessage');
        const approveButton = document.getElementById('approveConfirmButton');

        document.querySelectorAll('.approve-confirm-form').forEach(function (form) {
            form.addEventListener('submit', function (event) {
                event.preventDefault();

                selectedApproveForm = form;

                approveTitle.textContent = form.dataset.title || 'Aktifkan Akun?';
                approveMessage.textContent = form.dataset.message || 'Apakah Anda yakin ingin mengaktifkan akun ini?';

                approveModal.show();
            });
        });

        approveButton.addEventListener('click', function () {
            if (selectedApproveForm) {
                approveButton.disabled = true;
                approveButton.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Mengaktifkan...';
                selectedApproveForm.submit();
            }
        });

        approveModalElement.addEventListener('hidden.bs.modal', function () {
            selectedApproveForm = null;
            approveButton.disabled = false;
            approveButton.innerHTML = 'Ya, Aktifkan';
        });
    });
</script>

@endsection

