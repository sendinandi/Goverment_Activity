<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. BUAT TABEL PROGRAMS
        if (!Schema::hasTable('programs')) {
            Schema::create('programs', function (Blueprint $table) {
                $table->id();
                $table->string('nama_program');
                $table->timestamps();
            });
        }

        // 2. BUAT TABEL ACTIVITIES
        if (!Schema::hasTable('activities')) {
            Schema::create('activities', function (Blueprint $table) {
                $table->id();
                $table->foreignId('program_id')->nullable()->constrained('programs')->onDelete('cascade');
                $table->foreignId('opd_id')->nullable()->constrained('opds')->onDelete('cascade');
                $table->string('nama_kegiatan');
                $table->timestamps();
            });
        }

        // 3. STEP PERTAMA: RENAME KOLOM DULU (PENTING!)
        // Kita pisah Schema-nya biar dieksekusi duluan
        Schema::table('development_projects', function (Blueprint $table) {
            if (Schema::hasColumn('development_projects', 'nama_kegiatan')) {
                $table->renameColumn('nama_kegiatan', 'nama_sub_kegiatan');
            }
        });

        // 4. STEP KEDUA: BARU TAMBAH/HAPUS KOLOM LAIN
        Schema::table('development_projects', function (Blueprint $table) {

            // Hapus sector_id
            if (Schema::hasColumn('development_projects', 'sector_id')) {
                try {
                    $table->dropForeign(['sector_id']);
                } catch (\Exception $e) {
                }

                $table->dropColumn(['sector_id']);
            }

            // HAPUS DISTRICT_ID JUGA (INI YANG BIKIN ERROR)
            if (Schema::hasColumn('development_projects', 'district_id')) {
                try {
                    $table->dropForeign(['district_id']);
                } catch (\Exception $e) {
                }

                $table->dropColumn(['district_id']);
            }

            // Tambah Kolom Baru (Aman)
            if (!Schema::hasColumn('development_projects', 'sasaran_fisik_desc')) {
                $table->string('sasaran_fisik_desc')->nullable()->after('nama_sub_kegiatan')->comment('Deskripsi Sasaran');
            }

            if (!Schema::hasColumn('development_projects', 'total_target_fisik_tahunan')) {
                $table->float('total_target_fisik_tahunan')->default(0)->after('sasaran_fisik_desc');
            }

            if (!Schema::hasColumn('development_projects', 'target_persen_bulan_ini')) {
                $table->float('target_persen_bulan_ini')->default(0)->after('target_fisik_bulan_ini');
            }

            if (!Schema::hasColumn('development_projects', 'realisasi_persen_bulan_ini')) {
                $table->float('realisasi_persen_bulan_ini')->default(0)->after('realisasi_fisik_bulan_ini');
            }

            if (!Schema::hasColumn('development_projects', 'activity_id')) {
                $table->foreignId('activity_id')->nullable()->after('opd_id')->constrained('activities')->onDelete('cascade');
            }
        });
    }

    public function down(): void
    {
        // Rollback logic
    }
};
