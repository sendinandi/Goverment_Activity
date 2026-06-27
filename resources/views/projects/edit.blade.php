@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold text-primary m-0">
        <i class="bi bi-pencil-square me-2"></i>Edit Sub Kegiatan
    </h4>
    <a href="{{ route('projects.index') }}" class="btn btn-light border fw-bold btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

@if($project->status == 'revision' && $project->catatan_revisi)
<div class="alert alert-danger shadow-sm mb-4 border-0 border-start border-5 border-danger">
    <h6 class="fw-bold"><i class="bi bi-exclamation-circle-fill me-2"></i>Catatan Revisi dari Verifikator:</h6>
    <p class="mb-0">{{ $project->catatan_revisi }}</p>
</div>
@endif

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        <form action="{{ route('projects.update', $project->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label small fw-bold text-muted">PROGRAM</label>
                    <select id="programSelect" class="form-select bg-light" required>
                        <option value="">- Pilih Program -</option>
                        @foreach($programs as $prog)
                        <option value="{{ $prog->id }}" {{ $currentProgramId == $prog->id ? 'selected' : '' }}>
                            {{ $prog->nama_program }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold text-muted">KEGIATAN</label>
                    <select name="activity_id" id="activitySelect" class="form-select bg-light" required>
                        @foreach($activities as $act)
                        <option value="{{ $act->id }}" {{ $project->activity_id == $act->id ? 'selected' : '' }}>
                            {{ $act->nama_kegiatan }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <hr class="my-4 text-muted opacity-25">

            <h6 class="fw-bold text-dark mb-3">Rincian Sub Kegiatan</h6>

            <div class="mb-3">
                <label class="form-label small fw-bold">Nama Sub Kegiatan <span class="text-danger">*</span></label>
                <input type="text" name="nama_sub_kegiatan" class="form-control" value="{{ $project->nama_sub_kegiatan }}" required>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Bulan Pelaporan</label>
                    <select name="bulan" class="form-select" required>
                        @foreach(range(1,12) as $m)
                        <option value="{{ $m }}" {{ $project->bulan == $m ? 'selected' : '' }}>
                            {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Penanggung Jawab (PPTK)</label>
                    <input type="text" name="penanggung_jawab" class="form-control" value="{{ $project->penanggung_jawab }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Sasaran Fisik (Output)</label>
                    <input type="text" name="sasaran_fisik_desc" class="form-control" value="{{ $project->sasaran_fisik_desc }}" required>
                </div>
            </div>

            <div class="row g-3 mb-3 bg-light p-3 rounded mx-0 border">
                <div class="col-md-12 mb-2">
                    <h6 class="fw-bold text-primary m-0 small">TARGET & REALISASI FISIK</h6>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Satuan</label>
                    <input type="text" name="satuan" class="form-control" value="{{ $project->satuan }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Target (Qty)</label>
                    <input type="number" step="any" name="target_fisik_bulan_ini" class="form-control" value="{{ $project->target_fisik_bulan_ini }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Target (%)</label>
                    <div class="input-group">
                        <input type="number" step="any" name="target_persen_bulan_ini" class="form-control" value="{{ $project->target_persen_bulan_ini }}" required>
                        <span class="input-group-text">%</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-success">Realisasi (%)</label>
                    <div class="input-group">
                        <input type="number" step="any" name="realisasi_persen_bulan_ini" class="form-control border-success" value="{{ $project->realisasi_persen_bulan_ini }}">
                        <span class="input-group-text bg-success text-white">%</span>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-3 bg-light p-3 rounded mx-0 border">
                <div class="col-md-12 mb-2">
                    <h6 class="fw-bold text-primary m-0 small">ANGGARAN KEUANGAN</h6>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold">Pagu Anggaran</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" name="pagu_anggaran" class="form-control" value="{{ $project->pagu_anggaran }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold">Realisasi Keuangan</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" name="realisasi_anggaran" class="form-control" value="{{ $project->realisasi_anggaran }}">
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label small fw-bold">Kendala (Jika Ada)</label>
                    <textarea name="kendala" class="form-control" rows="2">{{ $project->kendala }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold">Upaya Tindak Lanjut</label>
                    <textarea name="tindak_lanjut" class="form-control" rows="2">{{ $project->tindak_lanjut }}</textarea>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary fw-bold py-2 shadow-sm">
                    <i class="bi bi-save-fill me-2"></i> SIMPAN PERUBAHAN
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.getElementById('programSelect').addEventListener('change', function() {
        var programId = this.value;
        var activitySelect = document.getElementById('activitySelect');

        activitySelect.innerHTML = '<option value="">Loading...</option>';
        activitySelect.disabled = true;

        if (programId) {
            fetch('/api/get-activities/' + programId)
                .then(response => response.json())
                .then(data => {
                    activitySelect.innerHTML = '<option value="">- Pilih Kegiatan -</option>';
                    data.forEach(function(activity) {
                        var option = document.createElement('option');
                        option.value = activity.id;
                        option.text = activity.nama_kegiatan;
                        activitySelect.appendChild(option);
                    });
                    activitySelect.disabled = false;
                })
                .catch(error => {
                    console.error('Error:', error);
                    activitySelect.innerHTML = '<option value="">Gagal memuat data</option>';
                });
        }
    });
</script>
@endpush