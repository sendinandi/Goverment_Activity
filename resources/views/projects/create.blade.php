@extends('layouts.app')

@section('content')

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

<div class="card border-0 shadow-sm rounded-4 mb-5 mt-2">
    <div class="card-body p-4 p-md-5">
        @if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4">
            <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i>Oops! Ada isian yang belum tepat:</div>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('projects.store') }}" method="POST">
            @csrf

            <h6 class="fw-bold text-primary mb-3"><i class="bi bi-folder2-open me-2"></i>Klasifikasi Kegiatan</h6>

            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <label class="form-label small fw-bold text-muted">Filter Program / Bagian (Opsional)</label>
                    <select id="filter_program" class="form-select form-select-lg rounded-3 select2-custom">
                        <option value="">-- Tampilkan Semua Program & Bagian --</option>
                        @foreach(\App\Models\Program::orderBy('nama_bagian')->get() as $prog)
                        <option value="{{ $prog->id }}">{{ $prog->nama_bagian }} - {{ $prog->nama_program }}</option>
                        @endforeach
                    </select>
                    <div class="form-text small text-muted mt-1"><i class="bi bi-filter-circle me-1"></i>Pilih program untuk menyaring daftar kegiatan di bawah.</div>
                </div>

                <div class="col-md-12">
                    <label class="form-label small fw-bold text-muted">Pilih Kegiatan Induk <span class="text-danger">*</span></label>
                    <select name="activity_id" id="activity_id" class="form-select form-select-lg rounded-3 select2-custom" required>
                        <option value="">- Ketik atau Pilih Kegiatan -</option>
                        @foreach(\App\Models\Activity::with('program')->orderBy('nama_kegiatan')->get() as $act)
                        <option value="{{ $act->id }}" data-program-id="{{ $act->program_id }}">
                            {{ $act->nama_kegiatan }}
                        </option>
                        @endforeach
                    </select>
                    <div class="form-text small text-muted mt-2"><i class="bi bi-search me-1"></i>Anda bisa langsung mengetik nama kegiatan untuk mencari.</div>
                </div>
            </div>

            <hr class="my-4 text-muted opacity-25">

            <h6 class="fw-bold text-primary mb-3"><i class="bi bi-card-text me-2"></i>Rincian Sub Kegiatan</h6>

            <div class="mb-3">
                <label class="form-label small fw-bold text-dark">Nama Sub Kegiatan <span class="text-danger">*</span></label>
                <input type="text" name="nama_sub_kegiatan" class="form-control rounded-3 py-2" placeholder="Contoh: Belanja Alat Tulis Kantor" required>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-dark">Bulan Pelaporan <span class="text-danger">*</span></label>
                    <select name="bulan" class="form-select rounded-3 py-2" required>
                        @foreach(range(1,12) as $m)
                        <option value="{{ $m }}" {{ date('n') == $m ? 'selected' : '' }}>
                            {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-dark">Penanggung Jawab (PPTK)</label>
                    <input type="text" name="penanggung_jawab" class="form-control rounded-3 py-2" placeholder="Nama Penanggung Jawab">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-dark">Sasaran Fisik (Output) <span class="text-danger">*</span></label>
                    <input type="text" name="sasaran_fisik_desc" class="form-control rounded-3 py-2" placeholder="Contoh: Tersedianya ATK" required>
                </div>
            </div>

            <div class="row g-4 mb-4">

                <div class="col-md-6">
                    <div class="bg-light bg-opacity-50 border rounded-4 p-4 h-100">
                        <h6 class="fw-bold text-dark mb-3 small"><i class="bi bi-bar-chart-fill text-warning me-2"></i>TARGET & REALISASI FISIK</h6>

                        <div class="p-3 mb-3 rounded-3 bg-white border">
                            <span class="badge bg-secondary mb-2"><i class="bi bi-calendar3 me-1"></i> Target Setahun Penuh</span>
                            <div class="row g-2">
                                <div class="col-sm-6">
                                    <label class="form-label small fw-bold text-muted" style="font-size: 0.8rem;">Satuan <span class="text-danger">*</span></label>
                                    <input type="text" name="satuan" class="form-control form-control-sm rounded-3" placeholder="Contoh: Laporan / Orang" required>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label small fw-bold text-muted" style="font-size: 0.8rem;">Target Setahun (Qty) <span class="text-danger">*</span></label>
                                    <input type="number" step="any" id="targetTahunan" name="total_target_fisik_tahunan" class="form-control form-control-sm rounded-3" placeholder="Contoh: 2520" required>
                                </div>
                            </div>
                        </div>

                        <div class="p-3 rounded-3" style="background-color: #f0fdf4; border: 1px solid #bbf7d0;">
                            <span class="badge bg-success mb-2"><i class="bi bi-calendar-check me-1"></i> Laporan Bulan Ini</span>

                            <div class="row g-2 mb-2">
                                <div class="col-sm-6">
                                    <label class="form-label small fw-bold text-success" style="font-size: 0.8rem;">Target Bulan Ini (Qty) <span class="text-danger">*</span></label>
                                    <input type="number" step="any" id="targetBulanIni" name="target_fisik_bulan_ini" class="form-control form-control-sm rounded-3 border-success border-opacity-50" placeholder="0" required>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label small fw-bold text-success" style="font-size: 0.8rem;">Target Bulan Ini (%)</label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" step="any" name="target_persen_bulan_ini" class="form-control rounded-3 border-success border-opacity-50" placeholder="0">
                                        <span class="input-group-text bg-white text-success border-success border-opacity-50">%</span>
                                    </div>
                                </div>
                            </div>

                            <hr class="border-success border-opacity-25 my-2">

                            <div class="row g-2 mb-3">
                                <div class="col-sm-6">
                                    <label class="form-label small fw-bold text-success" style="font-size: 0.8rem;">Realisasi Bulan Ini (Qty) <span class="text-danger">*</span></label>
                                    <input type="number" step="any" id="realisasiBulanIni" name="realisasi_fisik_bulan_ini" class="form-control form-control-sm rounded-3 border-success" placeholder="0">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label small fw-bold text-success" style="font-size: 0.8rem;">Capaian Bulan Ini (%)</label>
                                    <div class="input-group input-group-sm shadow-sm">
                                        <input type="number" step="any" id="capaianPersen" name="capaian_fisik" class="form-control rounded-3 border-success bg-white" placeholder="0" readonly>
                                        <span class="input-group-text bg-success bg-opacity-10 text-success rounded-3 border-success fw-bold">%</span>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-2">
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-primary" style="font-size: 0.8rem;">Progress Thd Target Setahun (%)</label>
                                    <div class="input-group input-group-sm shadow-sm">
                                        <input type="number" step="any" id="realisasiPersenTahunan" name="realisasi_persen_bulan_ini" class="form-control rounded-3 border-primary bg-white text-primary fw-bold" placeholder="0" readonly>
                                        <span class="input-group-text bg-primary bg-opacity-10 text-primary rounded-3 border-primary fw-bold">%</span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-text text-muted small mt-2" style="font-size: 0.72rem; line-height: 1.4;">
                                <i class="bi bi-info-circle me-1"></i> <b>Capaian</b> = (Realisasi / Target Bulan). <br> <b>Progress Setahun</b> = (Realisasi / Target Setahun).
                            </div>
                        </div>

                    </div>
                </div>

                <div class="col-md-6">
                    <div class="bg-light bg-opacity-50 border rounded-4 p-4 h-100">
                        <h6 class="fw-bold text-dark mb-3 small"><i class="bi bi-wallet2 text-success me-2"></i>ANGGARAN KEUANGAN</h6>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted">Pagu Anggaran Total <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white rounded-3 border-end-0 text-muted">Rp</span>
                                <input type="number" name="pagu_anggaran" class="form-control rounded-3 border-start-0 py-2 fw-semibold" placeholder="0" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-success">Realisasi Keuangan Saat Ini</label>
                            <div class="input-group shadow-sm">
                                <span class="input-group-text bg-success bg-opacity-10 text-success rounded-3 border-success border-end-0">Rp</span>
                                <input type="number" name="realisasi_anggaran" class="form-control rounded-3 border-success border-start-0 py-2 fw-semibold text-success" placeholder="0">
                            </div>
                            <div class="form-text small text-muted mt-1">Kosongkan jika belum ada pencairan/serapan.</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label small fw-bold text-dark">Kendala (Jika Ada)</label>
                    <textarea name="kendala" class="form-control rounded-3" rows="3" placeholder="Tuliskan kendala yang dihadapi di lapangan..."></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold text-dark">Upaya Tindak Lanjut</label>
                    <textarea name="tindak_lanjut" class="form-control rounded-3" rows="3" placeholder="Solusi atau langkah yang sudah/akan dilakukan..."></textarea>
                </div>
            </div>

            <hr class="my-4 text-muted opacity-25">

            <div class="d-flex justify-content-end gap-2">
                <button type="reset" class="btn btn-light border rounded-3 px-4 py-2">Reset Form</button>
                <button type="submit" class="btn btn-primary rounded-3 px-5 py-2 fw-bold shadow-sm">
                    <i class="bi bi-save-fill me-2"></i> Simpan Data Realisasi
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        // 1. INISIALISASI SELECT2
        $('.select2-custom').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: $(this).data('placeholder')
        });

        // 2. FITUR FILTER PROGRAM -> KEGIATAN
        var semuaKegiatan = $('#activity_id option').clone();

        $('#filter_program').on('change', function() {
            var selectedProgram = $(this).val();

            $('#activity_id').empty().append(semuaKegiatan);

            if (selectedProgram) {
                $('#activity_id option').each(function() {
                    if ($(this).val() !== "" && $(this).data('program-id') != selectedProgram) {
                        $(this).remove();
                    }
                });
            }

            $('#activity_id').val('').trigger('change');
        });
    });

    // 3. FITUR HITUNG OTOMATIS PERSENTASE (DIREVISI: TANPA PEMBULATAN KE ATAS)
    document.addEventListener('DOMContentLoaded', function() {
        const targetTahunan = document.getElementById('targetTahunan');
        const targetBulanIni = document.getElementById('targetBulanIni');
        const realisasiBulanIni = document.getElementById('realisasiBulanIni');
        const capaianPersen = document.getElementById('capaianPersen');
        const realisasiPersenTahunan = document.getElementById('realisasiPersenTahunan');

        // Fungsi khusus untuk memotong 2 desimal tanpa membulatkan ke atas
        function potongDesimal(angka) {
            return Math.floor(angka * 100) / 100;
        }

        function hitungSemua() {
            let tTahunan = parseFloat(targetTahunan.value) || 0;
            let tBulan = parseFloat(targetBulanIni.value) || 0;
            let rBulan = parseFloat(realisasiBulanIni.value) || 0;

            // Hitung Capaian Bulan Ini (%)
            if (tBulan > 0) {
                let cap = (rBulan / tBulan) * 100;
                capaianPersen.value = potongDesimal(cap);
            } else {
                capaianPersen.value = 0;
            }

            // Hitung Progress Thd Target Setahun (%)
            if (tTahunan > 0) {
                let prog = (rBulan / tTahunan) * 100;
                realisasiPersenTahunan.value = potongDesimal(prog);
            } else {
                realisasiPersenTahunan.value = 0;
            }
        }

        targetTahunan.addEventListener('input', hitungSemua);
        targetBulanIni.addEventListener('input', hitungSemua);
        realisasiBulanIni.addEventListener('input', hitungSemua);
    });
</script>
@endpush