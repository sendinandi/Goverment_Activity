<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\DevelopmentProject;

class DevelopmentProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Bersihkan tabel proyek terlebih dahulu agar tidak duplikat saat dijalankan ulang
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DevelopmentProject::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Array rumpun sub-kegiatan riil Diskominfostandi berdasarkan berkas Excel kamu
        $proyekList = [
            [
                'nama' => 'Penyediaan Jasa Pelayanan Umum Kantor',
                'pagu' => 450000000,
                'satuan' => 'Laporan',
                'desc' => 'Tersedianya jasa pelayanan administrasi penunjang urusan kantor bulanan.',
                'pj' => 'Kasubag Umum & Kepegawaian',
                'activity_id' => 1,
                'tipe' => 'rutin' // Berjalan konstan tiap bulan
            ],
            [
                'nama' => 'Pemeliharaan Peralatan Studio dan Komunikasi',
                'pagu' => 750000000,
                'satuan' => 'Unit',
                'desc' => 'Terpeliharanya infrastruktur perangkat penyiaran radio/TV publik milik daerah.',
                'pj' => 'Kabid Disseminasi Informasi Publik',
                'activity_id' => 2,
                'tipe' => 'kendala_maret' // Mengalami penurunan performa fisik sejak bulan Maret
            ],
            [
                'nama' => 'Pengadaan Mebel dan Perlengkapan Kantor',
                'pagu' => 280000000,
                'satuan' => 'Paket',
                'desc' => 'Pemenuhan sarana mebeler fungsional ruang pelayanan publik Diskominfostandi.',
                'pj' => 'Pejabat Pembuat Komitmen (PPK)',
                'activity_id' => 3,
                'tipe' => 'mulai_april' // Proyek baru aktif di input pada triwulan kedua (April)
            ],
            [
                'nama' => 'Penyediaan Layanan Keamanan Informasi Pemerintah Daerah',
                'pagu' => 620000000,
                'satuan' => 'Sertifikat',
                'desc' => 'Pelaksanaan audit eksternal pemenuhan standar keamanan informasi ISO 27001.',
                'pj' => 'Kabid Persandian dan Keamanan Informasi',
                'activity_id' => 4,
                'tipe' => 'lambat_awal' // Progres fisik kecil di awal tahun karena pengurusan administrasi KAK
            ],
            [
                'nama' => 'Pengembangan Aplikasi Layanan Publik Terintegrasi',
                'pagu' => 950000000,
                'satuan' => 'Aplikasi',
                'desc' => 'Sistem integrasi portal layanan masyarakat Kota Bekasi dalam satu basis data.',
                'pj' => 'Kabid Aplikasi dan Elektronik Pemerintahan (e-Gov)',
                'activity_id' => 5,
                'tipe' => 'loncat_mei' // Progres melonjak tajam saat memasuki tahapan coding di bulan Mei
            ]
        ];

        // 2. Looping masif untuk mengisikan sebaran tren bulanan (Januari s/d Mei 2026)
        for ($bulan = 1; $bulan <= 5; $bulan++) {
            foreach ($proyekList as $proyek) {
                
                // Variabel default pengukur performa fisik
                $targetPersen = 0;
                $realisasiPersen = 0;
                $kendala = '-';
                $tindakLanjut = '-';
                $anggaranDiserap = 0;

                // LOGIKA ATURAN TREN PER BULAN SESUAI KARAKTERISTIK DINAS:
                switch ($proyek['tipe']) {
                    case 'rutin':
                        $targetPersen = 20 * $bulan;
                        $realisasiPersen = 20 * $bulan;
                        $anggaranDiserap = ($proyek['pagu'] / 12) * $bulan;
                        break;

                    case 'kendala_maret':
                        $targetPersen = 15 * $bulan;
                        if ($bulan >= 3) {
                            // Simulasi deviasi minus akibat keterlambatan vendor logistik
                            $realisasiPersen = (15 * $bulan) - (5 * ($bulan - 2));
                            $kendala = 'Keterlambatan pengiriman modul penguat sinyal audio mixer dari distributor utama luar daerah.';
                            $tindakLanjut = 'Melayangkan surat teguran resmi dan mendesak penyedia untuk mempercepat timeline logistik.';
                        } else {
                            $realisasiPersen = 15 * $bulan;
                        }
                        $anggaranDiserap = 80000000 * $bulan;
                        break;

                    case 'mulai_april':
                        if ($bulan < 4) {
                            continue 2; // Lewati looping (proyek belum ada di bulan Jan - Mar)
                        }
                        $targetPersen = 50 * ($bulan - 3);
                        if ($bulan == 4) {
                            $realisasiPersen = 10; // Gagal lelang di bulan pertama
                            $kendala = 'Proses gagal lelang berkas pada sistem LPSE akibat ketidaksesuaian dokumen administrasi rekanan.';
                            $tindakLanjut = 'Melakukan kaji ulang dokumen Kerangka Acuan Kerja (KAK) bersama tim teknis untuk lelang cepat ulang.';
                            $anggaranDiserap = 0;
                        } else {
                            $realisasiPersen = 50 * ($bulan - 3);
                            $anggaranDiserap = 180000000;
                        }
                        break;

                    case 'lambat_awal':
                        $targetPersen = 5 * $bulan;
                        if ($bulan <= 3) {
                            $realisasiPersen = 2 * $bulan; // Lambat di triwulan 1
                            $kendala = 'Proses penyesuaian regulasi review Tingkat Komponen Dalam Negeri (TKDN) di Kementerian Perindustrian.';
                            $tindakLanjut = 'Mengirimkan tim fasilitator dinas untuk melakukan asistensi percepatan dokumen sertifikasi.';
                        } else {
                            $realisasiPersen = 5 * $bulan;
                        }
                        $anggaranDiserap = 25000000 * $bulan;
                        break;

                    case 'loncat_mei':
                        $targetPersen = 10 * $bulan;
                        if ($bulan == 5) {
                            $realisasiPersen = 60; // Melonjak di bulan Mei karena modul selesai serentak
                            $anggaranDiserap = 450000000;
                        } else {
                            $realisasiPersen = 8 * $bulan;
                            $anggaranDiserap = 30000000 * $bulan;
                        }
                        break;
                }

                // Hitung formula capaian fisik otomatis (Sama dengan rumus di ProjectController kamu)
                $capaian = 0;
                if ($targetPersen > 0) {
                    $capaian = ($realisasiPersen / $targetPersen) * 100;
                }

                // Eksekusi insert data massal ke database MySQL
                DevelopmentProject::create([
                    'user_id' => 2, // Terikat ke Operator OPD
                    'opd_id' => 1,  // Terikat ke Diskominfostandi
                    'activity_id' => $proyek['activity_id'],
                    'nama_sub_kegiatan' => $proyek['nama'],
                    'bulan' => $bulan,
                    'tahun_anggaran' => 2026,
                    'pagu_anggaran' => $proyek['pagu'],
                    'realisasi_anggaran' => $anggaranDiserap,
                    'satuan' => $proyek['satuan'],
                    'sasaran_fisik_desc' => $proyek['desc'],
                    'total_target_fisik_tahunan' => 100,
                    'target_fisik_bulan_ini' => ceil($targetPersen / 10),
                    'target_persen_bulan_ini' => $targetPersen,
                    'realisasi_fisik_bulan_ini' => ceil($realisasiPersen / 10),
                    'realisasi_persen_bulan_ini' => $realisasiPersen,
                    'capaian_fisik' => round($capaian, 2),
                    'status' => 'approved', // Langsung berstatus disetujui agar langsung merender grafik dashboard
                    'kendala' => $kendala,
                    'tindak_lanjut' => $tindakLanjut,
                    'penanggung_jawab' => $proyek['pj'],
                    'catatan_revisi' => null
                ]);
            }
        }
    }
}