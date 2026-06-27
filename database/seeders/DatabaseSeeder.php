<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Opd;
use App\Models\User;
use App\Models\Program;
use App\Models\Activity;
use App\Models\DevelopmentProject;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ==========================================
        // 1. SETUP USER & OPD (KANTOR)
        // ==========================================
        $opd = Opd::firstOrCreate(
            ['kode_opd' => 'DISKOMINFO'],
            ['nama_opd' => 'DISKOMINFOSTANDI KOTA BEKASI']
        );

        // USER ADMIN (Verifikator)
        User::firstOrCreate(
            ['email' => 'admin@diskominfostandi.go.id'],
            [
                'name' => 'Admin Program',
                'password' => Hash::make('password'),
                'role' => 'admin_opd',
                'opd_id' => $opd->id,
                'email_verified_at' => now() // <--- PENTING: Agar bisa langsung login
            ]
        );

        // USER OPERATOR
        User::firstOrCreate(
            ['email' => 'operator@diskominfostandi.go.id'],
            [
                'name' => 'Operator Kegiatan',
                'password' => Hash::make('password'),
                'role' => 'operator',
                'opd_id' => $opd->id,
                'email_verified_at' => now() // <--- PENTING
            ]
        );

        // USER KEPALA DINAS (Pimpinan)
        User::firstOrCreate(
            ['email' => 'kadis@diskominfostandi.go.id'],
            [
                'name' => 'Kepala Dinas',
                'password' => Hash::make('password'),
                'role' => 'pimpinan',
                'opd_id' => $opd->id,
                'email_verified_at' => now() // <--- PENTING
            ]
        );

        // ==========================================
        // 2. DATA KEGIATAN & PROYEK (LENGKAP)
        // ==========================================

        $programs = [
            [
                'nama' => 'PROGRAM PENUNJANG URUSAN PEMERINTAHAN DAERAH KABUPATEN/KOTA',
                'kegiatan' => [
                    [
                        'nama' => 'Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah',
                        'subs' => [
                            ['Penyusunan Dokumen Perencanaan Perangkat Daerah', 'Dokumen', 9, 0, 0, 0, '-'],
                            ['Koordinasi dan Penyusunan Laporan Capaian Kinerja dan Ikhtisar Realisasi Kinerja SKPD', 'Laporan', 21, 1, 4.75, 4.75, '-'],
                        ]
                    ],
                    [
                        'nama' => 'Administrasi Keuangan Perangkat Daerah',
                        'subs' => [
                            ['Penyediaan Gaji dan Tunjangan ASN', 'Orang/Bulan', 2520, 89, 0.07, 3.53, 'Belum terserapnya TPP ASN'],
                            ['Koordinasi dan Penyusunan Laporan Keuangan Akhir Tahun SKPD', 'Laporan', 22, 1, 0.045, 0.045, '-'],
                        ]
                    ],
                    [
                        'nama' => 'Administrasi Kepegawaian Perangkat Daerah',
                        'subs' => [
                            ['Pendidikan dan pelatihan pegawai berdasarkan tugas dan fungsi', 'Orang', 20, 0, 0, 0, 'Pelaksanaan di Triwulan II'],
                            ['Bimbingan Teknis Implementasi Peraturan Perundang-Undangan', 'Orang', 90, 0, 0.1, 0, 'Pelaksanaan di Triwulan II'],
                        ]
                    ],
                    [
                        'nama' => 'Administrasi Umum Perangkat Daerah',
                        'subs' => [
                            ['Penyediaan Komponen Instalasi Listrik/Penerangan Bangunan Kantor', 'Paket', 1, 0, 0.1, 10, '-'],
                            ['Penyediaan Bahan Logistik Kantor', 'Paket', 3, 0, 0.03, 3, '-'],
                            ['Penyediaan Barang Cetakan dan Penggandaan', 'Paket', 2, 0, 0.02, 2, '-'],
                            ['Penyediaan Bahan Bacaan dan Peraturan Perundang-undangan', 'Dokumen', 12, 1, 0.08, 0.08, '-'],
                            ['Fasilitasi Kunjungan Tamu', 'Laporan', 4, 0, 0, 0, 'Belum ada permohonan kunjungan'],
                            ['Penyelenggaraan Rapat Koordinasi dan Konsultasi SKPD', 'Laporan', 12, 1, 0.02, 0.02, '-'],
                            ['Penatausahaan Arsip Dinamis pada SKPD', 'Dokumen', 1, 0, 0, 0, 'Pelaksanaan di Triwulan II'],
                        ]
                    ],
                    [
                        'nama' => 'Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah',
                        'subs' => [
                            ['Pengadaan Mebel', 'Unit', 83, 0, 0, 0, 'Pelaksanaan di Triwulan II'],
                            ['Pengadaan Peralatan dan Mesin Lainnya', 'Unit', 24, 0, 0, 0, 'Pelaksanaan di Triwulan II'],
                        ]
                    ],
                    [
                        'nama' => 'Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah',
                        'subs' => [
                            ['Penyediaan Jasa Komunikasi, Sumber Daya Air dan Listrik', 'Laporan', 1, 1, 8.33, 8.33, '-'],
                            ['Penyediaan Jasa Pelayanan Umum Kantor', 'Laporan', 12, 1, 8.33, 8.33, '-'],
                        ]
                    ],
                    [
                        'nama' => 'Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah',
                        'subs' => [
                            ['Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan dan Pajak Kendaraan Perorangan Dinas', 'Unit', 19, 0, 0, 0, 'Dimulai Februari'],
                            ['Pemeliharaan Peralatan dan Mesin Lainnya', 'Unit', 100, 0, 0, 0, 'Dimulai Februari'],
                            ['Pemeliharaan/Rehabilitasi Gedung Kantor dan Bangunan Lainnya', 'Unit', 1, 0, 0, 0, 'Pelaksanaan di Triwulan II'],
                        ]
                    ]
                ]
            ],
            [
                'nama' => 'PROGRAM PENGELOLAAN INFORMASI DAN KOMUNIKASI PUBLIK',
                'kegiatan' => [
                    [
                        'nama' => 'Pengelolaan Informasi dan Komunikasi Publik Pemerintah Daerah Kabupaten/Kota',
                        'subs' => [
                            ['Relasi Media', 'Laporan', 12, 1, 8.33, 8.33, '-'],
                            ['Pengelolaan Konten dan Perencanaan Media Komunikasi Publik', 'Laporan', 12, 1, 8.33, 8.33, '-'],
                            ['Pengelolaan Media Komunikasi Publik', 'Laporan', 12, 1, 8.33, 8.33, '-'],
                            ['Pelayanan Informasi Publik', 'Laporan', 12, 1, 8.33, 8.33, '-'],
                            ['Kemitraan dengan Pemangku Kepentingan', 'Laporan', 4, 0, 0, 0, 'Pelaksanaan menyesuaikan jadwal'],
                        ]
                    ]
                ]
            ],
            [
                'nama' => 'PROGRAM APLIKASI INFORMATIKA',
                'kegiatan' => [
                    [
                        'nama' => 'Pengelolaan Nama Domain yang Telah Ditetapkan oleh Pemerintah Pusat dan Sub Domain di Lingkup Pemerintah Daerah',
                        'subs' => [
                            ['Penyelenggaraan Sistem Jaringan Intra Pemerintah Daerah', 'Titik', 50, 5, 10, 8, 'Kendala teknis jaringan di beberapa titik'],
                            ['Pengelolaan Nama Domain dan Sub Domain', 'Domain', 10, 1, 10, 10, '-'],
                        ]
                    ],
                    [
                        'nama' => 'Pengelolaan e-Government Di Lingkup Pemerintah Daerah',
                        'subs' => [
                            ['Pengembangan dan Pengelolaan Ekosistem Kota Cerdas', 'Sistem', 5, 0, 0, 0, 'Tahap Lelang'],
                            ['Pengembangan Aplikasi Layanan Publik', 'Aplikasi', 3, 0, 10, 5, 'Keterlambatan tim teknis'],
                        ]
                    ]
                ]
            ],
            [
                'nama' => 'PROGRAM STATISTIK DAN PERSANDIAN',
                'kegiatan' => [
                    [
                        'nama' => 'Penyelenggaraan Statistik Sektoral',
                        'subs' => [
                            ['Penyusunan Metadata Statistik Sektoral', 'Dokumen', 5, 0, 0, 0, '-'],
                            ['Pengumpulan Data Statistik Sektoral', 'Dataset', 100, 10, 10, 10, '-'],
                        ]
                    ],
                    [
                        'nama' => 'Penyelenggaraan Persandian untuk Pengamanan Informasi',
                        'subs' => [
                            ['Pelaksanaan Keamanan Informasi Pemerintahan Daerah', 'Laporan', 12, 1, 8.33, 8.33, '-'],
                        ]
                    ]
                ]
            ]
        ];

        // ==========================================
        // 3. EKSEKUSI LOOPING (PROCESSOR)
        // ==========================================

        foreach ($programs as $progData) {
            $program = Program::create(['nama_program' => $progData['nama']]);

            foreach ($progData['kegiatan'] as $actData) {
                $activity = Activity::create([
                    'program_id' => $program->id,
                    'opd_id' => $opd->id,
                    'nama_kegiatan' => $actData['nama']
                ]);

                foreach ($actData['subs'] as $sub) {
                    $namaSub = $sub[0];
                    $satuan = $sub[1];
                    $totalTarget = $sub[2];
                    $targetQty = $sub[3];
                    $targetPersen = $sub[4];
                    $realisasiPersen = $sub[5];
                    $kendala = $sub[6];

                    // Budget Dummy
                    $pagu = 100000000;
                    if (str_contains(strtolower($namaSub), 'mebel') || str_contains(strtolower($namaSub), 'bangunan') || str_contains(strtolower($namaSub), 'mesin')) {
                        $pagu = rand(500000000, 1500000000);
                    } elseif (str_contains(strtolower($namaSub), 'gaji') || str_contains(strtolower($namaSub), 'listrik')) {
                        $pagu = rand(2000000000, 5000000000);
                    } else {
                        $pagu = rand(50000000, 200000000);
                    }

                    // Rumus Capaian
                    $capaian = 0;
                    if ($targetPersen > 0) {
                        $capaian = ($realisasiPersen / $targetPersen) * 100;
                    } else {
                        $capaian = 100;
                    }

                    $status = ($capaian >= 99) ? 'approved' : 'draft';

                    DevelopmentProject::create([
                        'user_id' => 2, // Operator
                        'opd_id' => $opd->id,
                        'activity_id' => $activity->id,
                        'nama_sub_kegiatan' => $namaSub,
                        'bulan' => 1,
                        'tahun_anggaran' => 2026,
                        'pagu_anggaran' => $pagu,
                        'realisasi_anggaran' => 0,
                        'satuan' => $satuan,
                        'sasaran_fisik_desc' => "Jumlah $namaSub ($totalTarget $satuan)",
                        'total_target_fisik_tahunan' => $totalTarget,
                        'target_fisik_bulan_ini' => $targetQty,
                        'target_persen_bulan_ini' => $targetPersen,
                        'realisasi_fisik_bulan_ini' => ($targetQty > 0 && $realisasiPersen >= $targetPersen) ? $targetQty : 0,
                        'realisasi_persen_bulan_ini' => $realisasiPersen,
                        'capaian_fisik' => $capaian,
                        'penanggung_jawab' => 'PPTK / Kasubbag',
                        'kendala' => $kendala,
                        'tindak_lanjut' => '-',
                        'status' => $status
                    ]);
                }
            }
        }
    }
}
