<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Activity;
use App\Models\Project;
use App\Models\User;
use App\Models\Opd;
use Illuminate\Support\Facades\Hash;

class PembangunanSeeder extends Seeder
{
    public function run()
    {
        // 1. HANYA KOSONGKAN KEGIATAN DAN SUB KEGIATAN (User & OPD Aman Tidak Dihapus)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Activity::truncate();
        Project::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. AMBIL DATA OPD & USER YANG SUDAH ADA DI DATABASE
        // Jika tidak ketemu, baru dia bikin otomatis. Jadi akun Mas Sendi aman!
        $opd = Opd::firstOrCreate(
            ['nama_opd' => 'DISKOMINFOSTANDI KOTA BEKASI'],
            ['kode_opd' => '2.16.01']
        );

        // 1. USER ADMIN OPD
        User::updateOrCreate(
            ['email' => 'admin@diskominfostandi.go.id'],
            [
                'name' => 'Admin Program',
                'password' => Hash::make('password'),
                'role' => 'admin_opd',
                'opd_id' => $opd->id,
                'email_verified_at' => now()
            ]
        );

        // 2. USER OPERATOR (Kita simpan di variabel $user agar otomatis masuk ke pembuat sub kegiatan di bawahnya)
        $user = User::updateOrCreate(
            ['email' => 'operator@diskominfostandi.go.id'],
            [
                'name' => 'Operator Kegiatan',
                'password' => Hash::make('password'),
                'role' => 'operator',
                'opd_id' => $opd->id,
                'email_verified_at' => now()
            ]
        );

        // 3. USER KEPALA DINAS (Pimpinan)
        User::updateOrCreate(
            ['email' => 'kadis@diskominfostandi.go.id'],
            [
                'name' => 'Kepala Dinas',
                'password' => Hash::make('password'),
                'role' => 'pimpinan',
                'opd_id' => $opd->id,
                'email_verified_at' => now()
            ]
        );

        // 4. USER VERIFIKATOR (Tambahan Baru)
        User::updateOrCreate(
            ['email' => 'verifikator@diskominfostandi.go.id'],
            [
                'name' => 'Verifikator Data',
                'password' => Hash::make('password'),
                'role' => 'verifikator',
                'opd_id' => $opd->id,
                'email_verified_at' => now()
            ]
        );

        // 3. DATA MASTER KEGIATAN (REAL DARI EXCEL)
        $kegiatans = [
            ['id' => 1, 'nama_kegiatan' => 'Kegiatan Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah'],
            ['id' => 2, 'nama_kegiatan' => 'Kegiatan Administrasi Keuangan Perangkat Daerah'],
            ['id' => 3, 'nama_kegiatan' => 'Kegiatan Administrasi Kepegawaian Perangkat Daerah'],
            ['id' => 4, 'nama_kegiatan' => 'Kegiatan Administrasi Umum Perangkat Daerah'],
            ['id' => 5, 'nama_kegiatan' => 'Kegiatan Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah'],
        ];

        Activity::insert($kegiatans);

        // 4. DATA SUB KEGIATAN (REAL DARI SCREENSHOT EXCEL)
        $subKegiatans = [
            [
                'user_id' => $user->id,
                'opd_id' => $opd->id,
                'activity_id' => 1,
                'nama_sub_kegiatan' => 'Penyusunan Dokumen Perencanaan Perangkat Daerah',
                'penanggung_jawab' => 'Sekretariat / Perencanaan',
                'sasaran_fisik_desc' => 'Jumlah Dokumen Perencanaan Perangkat Daerah (9 Dokumen)',
                'satuan' => 'Dokumen',

                // Target & Realisasi berdasarkan Excel
                'target_fisik_bulan_ini' => 0,
                'target_persen_bulan_ini' => 0,
                'realisasi_fisik_bulan_ini' => 0,
                'realisasi_persen_bulan_ini' => 0,
                'capaian_fisik' => 0,

                'pagu_anggaran' => 0, // Bisa diupdate nanti via web
                'bulan' => 1,
                'tahun_anggaran' => 2026,
                'status' => 'approved',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $user->id,
                'opd_id' => $opd->id,
                'activity_id' => 1,
                'nama_sub_kegiatan' => 'Koordinasi dan Penyusunan Laporan Capaian Kinerja dan Ikhtisar Realisasi Kinerja SKPD',
                'penanggung_jawab' => 'Sekretariat / Perencanaan',
                'sasaran_fisik_desc' => 'Jumlah Laporan Capaian Kinerja dan Ikhtisar Realisasi SKPD dan Laporan Hasil Koordinasi Penyusunan Laporan Capaian Kinerja dan Ikhtisar Realisasi Kinerja SKPD (21 Laporan)',
                'satuan' => 'Laporan',

                // Target & Realisasi berdasarkan Excel (Target 4.75%, Realisasi 4.75%)
                'target_fisik_bulan_ini' => 1,
                'target_persen_bulan_ini' => 4.75,
                'realisasi_fisik_bulan_ini' => 1,
                'realisasi_persen_bulan_ini' => 4.75,
                'capaian_fisik' => 100, // Karena Realisasi (1) = Target (1)

                'pagu_anggaran' => 0, // Bisa diupdate nanti via web
                'bulan' => 1,
                'tahun_anggaran' => 2026,
                'status' => 'approved',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        Project::insert($subKegiatans);
    }
}
