<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('development_projects', function (Blueprint $table) {
            // 1. HAPUS Kolom yang tidak perlu (Peta & Lokasi Fisik)
            // Kita pakai dropColumn biar database bersih
            // $table->dropColumn(['latitude', 'longitude', 'district_id']);

            // Note: sector_id kita pertahankan tapi nanti kita anggap sebagai "Program" atau "Bidang"

            // 2. TAMBAH Kolom Baru sesuai Request Mas Sendi
            $table->tinyInteger('bulan')->after('nama_kegiatan')->comment('1=Januari, 2=Februari, dst');
            $table->string('satuan')->nullable()->after('pagu_anggaran')->comment('Unit, Paket, Orang, Dokumen');

            // Target & Realisasi Fisik Bulan Ini
            $table->float('target_fisik_bulan_ini')->default(0)->after('satuan');
            $table->float('realisasi_fisik_bulan_ini')->default(0)->after('target_fisik_bulan_ini');
            $table->float('capaian_fisik')->default(0)->after('realisasi_fisik_bulan_ini')->comment('Persentase hasil hitungan');

            // Masalah & Solusi
            $table->text('kendala')->nullable()->after('status');
            $table->text('tindak_lanjut')->nullable()->after('kendala');
            $table->string('penanggung_jawab')->nullable()->after('tindak_lanjut');
        });
    }

    public function down(): void
    {
        // Kembalikan seperti semula jika rollback
        Schema::table('development_projects', function (Blueprint $table) {
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
            $table->foreignId('district_id')->constrained('districts');

            $table->dropColumn([
                'bulan',
                'satuan',
                'target_fisik_bulan_ini',
                'realisasi_fisik_bulan_ini',
                'capaian_fisik',
                'kendala',
                'tindak_lanjut',
                'penanggung_jawab'
            ]);
        });
    }
};
