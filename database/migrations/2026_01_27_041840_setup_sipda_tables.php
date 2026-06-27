<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tabel Master Wilayah (Kecamatan)
        Schema::create('districts', function (Blueprint $table) {
            $table->id();
            $table->string('kode_kecamatan')->unique();
            $table->string('nama_kecamatan');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->json('polygon_boundary')->nullable(); // Untuk batas wilayah peta (GeoJSON)
            $table->timestamps();
        });

        // 2. Tabel Master Sektor Pembangunan
        Schema::create('sectors', function (Blueprint $table) {
            $table->id();
            $table->string('nama_sektor'); // Contoh: Infrastruktur, Pendidikan
            $table->timestamps();
        });

        // 3. Tabel Organisasi Perangkat Daerah (OPD)
        Schema::create('opds', function (Blueprint $table) {
            $table->id();
            $table->string('nama_opd');
            $table->string('kode_opd')->unique();
            $table->timestamps();
        });

        // 4. Update Tabel Users (Menambahkan kolom Role & OPD ke tabel user bawaan Laravel)
        // Kita tidak membuat tabel users baru, tapi mengedit yang sudah ada
        // Schema::table('users', function (Blueprint $table) {
        //     // Role: admin_opd, validator_walidata, pimpinan
        //     $table->enum('role', ['admin_opd', 'validator_walidata', 'pimpinan'])->default('admin_opd')->after('email');

        //     // Relasi ke OPD (User ini milik dinas mana?)
        //     $table->foreignId('opd_id')->nullable()->after('role')->constrained('opds')->onDelete('set null');
        // });

        // 5. Tabel Data Pembangunan (Transaksi Utama)
        Schema::create('development_projects', function (Blueprint $table) {
            $table->id();
            // Siapa yang input?
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            // Program milik dinas mana?
            $table->foreignId('opd_id')->constrained('opds')->onDelete('cascade');
            // Lokasi di kecamatan mana?
            $table->foreignId('district_id')->constrained('districts')->onDelete('cascade');
            // Sektor apa?
            $table->foreignId('sector_id')->constrained('sectors')->onDelete('cascade');

            $table->string('nama_kegiatan');
            $table->year('tahun_anggaran');

            // Data Keuangan & Progres
            $table->decimal('pagu_anggaran', 15, 2);
            $table->decimal('realisasi_anggaran', 15, 2)->default(0);
            $table->decimal('progres_fisik', 5, 2)->default(0); // 0 - 100%

            // Status Workflow (Validasi)
            $table->enum('status', ['draft', 'pending_validation', 'revision', 'approved'])->default('draft');
            $table->text('catatan_revisi')->nullable(); // Jika ditolak validator

            $table->timestamps();
        });

        // 6. Tabel Log Aktivitas (Untuk Audit Trail)
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('action'); // Contoh: "Input Data", "Validasi"
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('development_projects');

        // Hapus kolom tambahan di users sebelum drop tabel lain
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['opd_id']);
            $table->dropColumn(['opd_id', 'role']);
        });

        Schema::dropIfExists('opds');
        Schema::dropIfExists('sectors');
        Schema::dropIfExists('districts');
    }
};
