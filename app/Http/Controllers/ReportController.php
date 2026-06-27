<?php

namespace App\Http\Controllers;

use App\Models\DevelopmentProject;
use App\Models\Opd;
use App\Models\Activity;
use Illuminate\Http\Request;
use App\Exports\RekapExport;
use Maatwebsite\Excel\Facades\Excel;
// Catatan: Baris 'use Illuminate\Support\Facades\Response;' Dihapus untuk mencegah error 'Class Response Not Found'

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // 1. QUERY DATA
        $query = DevelopmentProject::with(['opd', 'activity', 'user'])
            ->where('status', 'approved');

        // --- TANGKAP FILTER BIDANG / BAGIAN ---
        if ($request->filled('bagian')) {
            $query->whereHas('activity.program', function ($q) use ($request) {
                $q->where('nama_bagian', $request->bagian);
            });
        }

        // --- TANGKAP FILTER PROGRAM UTAMA ---
        if ($request->filled('program_id')) {
            $query->whereHas('activity', function ($q) use ($request) {
                $q->where('program_id', $request->program_id);
            });
        }

        // 2. FILTER (Jika ada)
        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }

        if ($request->filled('opd_id')) {
            $query->where('opd_id', $request->opd_id);
        }

        if ($request->filled('activity_id')) {
            $query->where('activity_id', $request->activity_id);
        }

        // 3. AMBIL DATA DENGAN PAGINATION (Mencegah error pada $projects->links() di View)
        $projects = $query->latest()->paginate(10);

        // 4. DATA PENDUKUNG UNTUK DROPDOWN
        $opds = Opd::all();
        // Menggunakan pluck langsung dari Model untuk menghemat RAM
        $activities = Activity::whereIn('id', DevelopmentProject::pluck('activity_id')->unique())->get();

        return view('reports.index', compact('projects', 'opds', 'activities'));
    }

    public function print(Request $request)
    {
        // PERBAIKAN: Tambah limit waktu menjadi 5 menit agar tidak time out saat export banyak data
        set_time_limit(300);

        $query = DevelopmentProject::with(['opd', 'activity', 'user'])
            ->where('status', 'approved');

        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }
        if ($request->filled('opd_id')) {
            $query->where('opd_id', $request->opd_id);
        }

        $projects = $query->get();

        return Excel::download(new RekapExport($projects), 'Rekap_Kegiatan_SIPDA.xlsx');
    }

    public function show($id)
    {
        $project = DevelopmentProject::with(['opd', 'activity', 'user'])->findOrFail($id);
        return view('reports.show', compact('project'));
    }

    public function exportExcel(Request $request)
    {
        // 1. Tambah limit waktu agar tidak timeout
        set_time_limit(300);

        // 2. Query Data
        $query = \App\Models\DevelopmentProject::with(['opd', 'activity'])
            ->where('status', 'approved');

        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }
        if ($request->filled('opd_id')) {
            $query->where('opd_id', $request->opd_id);
        }

        $projects = $query->get();

        // 3. Download langsung menggunakan library Excel
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\RekapExport($projects), 'Rekap_Kegiatan_SIPDA.xlsx');
    }
}
