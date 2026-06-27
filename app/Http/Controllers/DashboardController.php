<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DevelopmentProject;
use App\Models\Activity;
use App\Models\Program;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | 1. Query Dasar Dashboard
        |--------------------------------------------------------------------------
        | Dashboard hanya menampilkan data yang sudah divalidasi/verifikasi
        | dengan status approved.
        */

        $tahunAktif = $request->filled('tahun') ? $request->tahun : null;
        $tahunTampil = $tahunAktif ?? 'Semua Tahun';

        $baseQuery = DevelopmentProject::with(['activity.program'])
            ->where('status', 'approved');

        /*
        |--------------------------------------------------------------------------
        | 2. Function Filter
        |--------------------------------------------------------------------------
        | Default pertama kali masuk:
        | - Semua tahun
        | - Semua bulan
        | - Semua bidang/bagian
        | - Semua program
        | - Semua kegiatan
        */

        $applyFilters = function ($query, $includeMonthFilter = true) use ($request, $tahunAktif) {
            // Filter Tahun Anggaran
            if ($tahunAktif) {
                $query->where('tahun_anggaran', $tahunAktif);
            }

            // Filter Rentang Bulan
            if ($includeMonthFilter) {
                if ($request->filled('start_month') && $request->filled('end_month')) {
                    $start = (int) $request->start_month;
                    $end = (int) $request->end_month;

                    if ($start > $end) {
                        [$start, $end] = [$end, $start];
                    }

                    $query->whereBetween('bulan', [$start, $end]);
                } elseif ($request->filled('start_month')) {
                    $query->where('bulan', '>=', (int) $request->start_month);
                } elseif ($request->filled('end_month')) {
                    $query->where('bulan', '<=', (int) $request->end_month);
                }
            }

            // Filter Bidang / Bagian
            if ($request->filled('bagian')) {
                $query->whereHas('activity.program', function ($q) use ($request) {
                    $q->where('nama_bagian', $request->bagian);
                });
            }

            // Filter Program
            if ($request->filled('program_id')) {
                $query->whereHas('activity', function ($q) use ($request) {
                    $q->where('program_id', $request->program_id);
                });
            }

            // Filter Kegiatan Induk
            if ($request->filled('activity_id')) {
                $query->where('activity_id', $request->activity_id);
            }

            // Filter Penanggung Jawab
            if ($request->filled('penanggung_jawab')) {
                $query->where('penanggung_jawab', $request->penanggung_jawab);
            }

            // Search Sub-Kegiatan
            if ($request->filled('q')) {
                $query->where('nama_sub_kegiatan', 'like', '%' . $request->q . '%');
            }

            return $query;
        };

        /*
        |--------------------------------------------------------------------------
        | 3. Data Utama Dashboard
        |--------------------------------------------------------------------------
        */

        $query = $applyFilters(clone $baseQuery, true);

        $projects = (clone $query)
            ->latest()
            ->get()
            ->map(function ($item) {
                return $this->normalizeProjectValue($item);
            });

        $totalPagu = $projects->sum('pagu_anggaran');
        $totalRealisasi = $projects->sum('realisasi_anggaran');
        $sisaAnggaran = max($totalPagu - $totalRealisasi, 0);

        $countData = $projects->count();

        $avgFisik = $countData > 0
            ? $projects->avg('capaian_fisik_normalized')
            : 0;

        $persenKeuangan = $totalPagu > 0
            ? ($totalRealisasi / $totalPagu) * 100
            : 0;

        /*
        |--------------------------------------------------------------------------
        | 4. Grafik Capaian Fisik per Sub-Kegiatan
        |--------------------------------------------------------------------------
        | Batasi 20 data agar grafik tetap terbaca.
        */

        $chartData = $projects
            ->sortByDesc('capaian_fisik_normalized')
            ->take(20)
            ->values();

        /*
        |--------------------------------------------------------------------------
        | 5. Data Tren Bulanan Berdasarkan Filter Bulan
        |--------------------------------------------------------------------------
        | Jika tidak ada filter bulan, grafik menampilkan Januari sampai Desember.
        | Jika pilih Januari s/d Januari, grafik hanya menampilkan Januari.
        */

        $trendQuery = $applyFilters(clone $baseQuery, true);

        $trendSource = $trendQuery
            ->get()
            ->map(function ($item) {
                return $this->normalizeProjectValue($item);
            });

        $bulanNama = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        $bulanSingkat = [
            1 => 'Jan',
            2 => 'Feb',
            3 => 'Mar',
            4 => 'Apr',
            5 => 'Mei',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Agu',
            9 => 'Sep',
            10 => 'Okt',
            11 => 'Nov',
            12 => 'Des',
        ];

        $startBulanGrafik = $request->filled('start_month') ? (int) $request->start_month : 1;
        $endBulanGrafik = $request->filled('end_month') ? (int) $request->end_month : 12;

        if ($startBulanGrafik > $endBulanGrafik) {
            [$startBulanGrafik, $endBulanGrafik] = [$endBulanGrafik, $startBulanGrafik];
        }

        $trendLabels = [];
        $trendTarget = [];
        $trendRealisasi = [];
        $trendHasData = [];

        for ($bulan = $startBulanGrafik; $bulan <= $endBulanGrafik; $bulan++) {
            $dataBulan = $trendSource->where('bulan', $bulan);

            $trendLabels[] = $bulanSingkat[$bulan];

            if ($dataBulan->count() > 0) {
                $trendTarget[] = round((float) $dataBulan->avg('target_persen_normalized'), 2);
                $trendRealisasi[] = round((float) $dataBulan->avg('realisasi_persen_normalized'), 2);
                $trendHasData[] = true;
            } else {
                $trendTarget[] = 0;
                $trendRealisasi[] = 0;
                $trendHasData[] = false;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 6. Ringkasan Kondisi Capaian
        |--------------------------------------------------------------------------
        | Tidak memakai batas 80/90%.
        | Logika berdasarkan perbandingan realisasi dengan target bulan berjalan.
        */

        $jumlahSesuaiTarget = $projects->filter(function ($p) {
            return (float) $p->realisasi_persen_normalized >= (float) $p->target_persen_normalized;
        })->count();

        $jumlahAdaKendala = $projects->filter(function ($p) {
            return (float) $p->realisasi_persen_normalized < (float) $p->target_persen_normalized
                && !empty($p->kendala)
                && $p->kendala !== '-';
        })->count();

        $jumlahDalamPemantauan = $projects->filter(function ($p) {
            return (float) $p->realisasi_persen_normalized < (float) $p->target_persen_normalized
                && (empty($p->kendala) || $p->kendala === '-');
        })->count();

        $persenDataSesuaiTarget = $countData > 0
            ? ($jumlahSesuaiTarget / $countData) * 100
            : 0;

        /*
        |--------------------------------------------------------------------------
        | 7. Data Dropdown Filter
        |--------------------------------------------------------------------------
        */

        $tahuns = DevelopmentProject::select('tahun_anggaran')
            ->whereNotNull('tahun_anggaran')
            ->distinct()
            ->orderBy('tahun_anggaran', 'desc')
            ->pluck('tahun_anggaran');

        $penanggungJawabs = DevelopmentProject::select('penanggung_jawab')
            ->whereNotNull('penanggung_jawab')
            ->distinct()
            ->orderBy('penanggung_jawab')
            ->pluck('penanggung_jawab');

        $activities = Activity::orderBy('nama_kegiatan')->get();

        $programs = Program::orderBy('nama_program')->get();

        $bagianList = Program::select('nama_bagian')
            ->whereNotNull('nama_bagian')
            ->distinct()
            ->orderBy('nama_bagian')
            ->pluck('nama_bagian');

        /*
        |--------------------------------------------------------------------------
        | 8. Periode Monitoring
        |--------------------------------------------------------------------------
        */

        $startMonth = $request->start_month;
        $endMonth = $request->end_month;

        if ($startMonth && $endMonth) {
            $periodeMonitoring = $bulanNama[(int) $startMonth] . ' s/d ' . $bulanNama[(int) $endMonth];
        } elseif ($startMonth) {
            $periodeMonitoring = 'Mulai ' . $bulanNama[(int) $startMonth];
        } elseif ($endMonth) {
            $periodeMonitoring = 'Sampai ' . $bulanNama[(int) $endMonth];
        } else {
            $periodeMonitoring = 'Seluruh Bulan';
        }

        return view('dashboard.index', compact(
            'totalPagu',
            'totalRealisasi',
            'sisaAnggaran',
            'avgFisik',
            'persenKeuangan',
            'projects',
            'chartData',
            'penanggungJawabs',
            'activities',
            'programs',
            'bagianList',
            'tahuns',
            'tahunAktif',
            'tahunTampil',
            'periodeMonitoring',
            'bulanNama',
            'trendLabels',
            'trendTarget',
            'trendRealisasi',
            'trendHasData',
            'jumlahSesuaiTarget',
            'jumlahAdaKendala',
            'jumlahDalamPemantauan',
            'persenDataSesuaiTarget'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | Normalisasi Nilai Persentase
    |--------------------------------------------------------------------------
    | Supaya tampilan dashboard tidak menampilkan 10000%.
    | Nilai grafik dibatasi maksimal 100% agar lebih mudah dibaca.
    */

    private function normalizeProjectValue($item)
    {
        $target = (float) $item->target_persen_bulan_ini;
        $realisasi = (float) $item->realisasi_persen_bulan_ini;
        $capaian = (float) $item->capaian_fisik;

        // Jika data tersimpan dalam desimal, misalnya 0.1 = 10%
        if ($target > 0 && $target <= 1) {
            $target *= 100;
        }

        if ($realisasi > 0 && $realisasi <= 1) {
            $realisasi *= 100;
        }

        $item->target_persen_normalized = min($target, 100);
        $item->realisasi_persen_normalized = min($realisasi, 100);
        $item->capaian_fisik_normalized = min($capaian, 100);

        return $item;
    }

    public function getSubActivities($activity_id)
    {
        $subs = DevelopmentProject::where('activity_id', $activity_id)
            ->where('status', 'approved')
            ->select('nama_sub_kegiatan')
            ->distinct()
            ->get();

        return response()->json($subs);
    }
}