<?php

namespace App\Http\Controllers;

use App\Models\DevelopmentProject;
use App\Models\Program;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = DevelopmentProject::with(['activity'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        $programs = Program::all();
        return view('projects.create', compact('programs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'activity_id' => 'required|exists:activities,id',
            'nama_sub_kegiatan' => 'required|string|max:255',
            'bulan' => 'required|integer|between:1,12',
            'satuan' => 'required|string',
            'sasaran_fisik_desc' => 'required|string',
            'target_fisik_bulan_ini' => 'required|numeric',
            'target_persen_bulan_ini' => 'required|numeric',
            'pagu_anggaran' => 'required|numeric',
        ]);

        $targetPersen = $request->target_persen_bulan_ini;
        $realisasiPersen = $request->realisasi_persen_bulan_ini ?? 0;

        $capaian = 0;
        if ($targetPersen > 0) {
            $capaian = ($realisasiPersen / $targetPersen) * 100;
        } elseif ($realisasiPersen > 0) {
            $capaian = 100;
        }

        DevelopmentProject::create([
            'user_id' => Auth::id(),
            'opd_id' => Auth::user()->opd_id,
            'activity_id' => $request->activity_id,
            'nama_sub_kegiatan' => $request->nama_sub_kegiatan,
            'bulan' => $request->bulan,
            'tahun_anggaran' => 2026,
            'satuan' => $request->satuan,
            'sasaran_fisik_desc' => $request->sasaran_fisik_desc,
            'total_target_fisik_tahunan' => 0,
            'target_fisik_bulan_ini' => $request->target_fisik_bulan_ini,
            'target_persen_bulan_ini' => $targetPersen,
            'realisasi_fisik_bulan_ini' => $request->realisasi_fisik_bulan_ini ?? 0,
            'realisasi_persen_bulan_ini' => $realisasiPersen,
            'capaian_fisik' => $capaian,
            'pagu_anggaran' => $request->pagu_anggaran,
            'realisasi_anggaran' => $request->realisasi_anggaran ?? 0,
            'kendala' => $request->kendala ?? '-',
            'tindak_lanjut' => $request->tindak_lanjut ?? '-',
            'penanggung_jawab' => $request->penanggung_jawab ?? '-',
            'status' => 'draft'
        ]);

        // LOG AKTIVITAS: Input Data
        \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'aksi' => 'Input Data',
            'keterangan' => 'Menambahkan data capaian fisik baru untuk sub kegiatan: ' . $request->nama_sub_kegiatan,
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('projects.index')->with('success', 'Data Sub Kegiatan berhasil disimpan.');
    }

    public function edit($id)
    {
        $project = DevelopmentProject::where('user_id', Auth::id())->findOrFail($id);

        $currentActivity = $project->activity;
        $currentProgramId = $currentActivity->program_id;

        $programs = Program::all();
        $activities = Activity::where('program_id', $currentProgramId)->get();

        return view('projects.edit', compact('project', 'programs', 'activities', 'currentProgramId'));
    }

    public function update(Request $request, $id)
    {
        $project = DevelopmentProject::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'activity_id' => 'required|exists:activities,id',
            'nama_sub_kegiatan' => 'required|string|max:255',
            'bulan' => 'required|integer|between:1,12',
            'satuan' => 'required|string',
            'sasaran_fisik_desc' => 'required|string',
            'target_fisik_bulan_ini' => 'required|numeric',
            'target_persen_bulan_ini' => 'required|numeric',
            'pagu_anggaran' => 'required|numeric',
        ]);

        $targetPersen = $request->target_persen_bulan_ini;
        $realisasiPersen = $request->realisasi_persen_bulan_ini ?? 0;

        $capaian = 0;
        if ($targetPersen > 0) {
            $capaian = ($realisasiPersen / $targetPersen) * 100;
        } elseif ($realisasiPersen > 0) {
            $capaian = 100;
        }

        $project->update([
            'activity_id' => $request->activity_id,
            'nama_sub_kegiatan' => $request->nama_sub_kegiatan,
            'bulan' => $request->bulan,
            'satuan' => $request->satuan,
            'sasaran_fisik_desc' => $request->sasaran_fisik_desc,
            'target_fisik_bulan_ini' => $request->target_fisik_bulan_ini,
            'target_persen_bulan_ini' => $targetPersen,
            'realisasi_fisik_bulan_ini' => $request->realisasi_fisik_bulan_ini ?? 0,
            'realisasi_persen_bulan_ini' => $realisasiPersen,
            'capaian_fisik' => $capaian,
            'pagu_anggaran' => $request->pagu_anggaran,
            'realisasi_anggaran' => $request->realisasi_anggaran ?? 0,
            'kendala' => $request->kendala ?? '-',
            'tindak_lanjut' => $request->tindak_lanjut ?? '-',
            'penanggung_jawab' => $request->penanggung_jawab ?? '-',
            'status' => 'draft',
            'catatan_revisi' => null
        ]);

        // LOG AKTIVITAS: Edit Data
        \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'aksi' => 'Edit Data',
            'keterangan' => 'Memperbarui data realisasi pada sub kegiatan: ' . $project->nama_sub_kegiatan,
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('projects.index')->with('success', 'Perubahan berhasil disimpan.');
    }

    public function getActivitiesByProgram($programId)
    {
        $activities = Activity::where('program_id', $programId)->get();
        return response()->json($activities);
    }

    public function history(Request $request)
    {
        $currentYear = date('Y');

        $query = DevelopmentProject::with(['activity', 'opd'])
            ->where('tahun_anggaran', '<', $currentYear)
            ->latest();

        if ($request->filled('tahun')) {
            $query->where('tahun_anggaran', $request->tahun);
        }

        $projects = $query->paginate(10)->withQueryString();

        $tahuns = DevelopmentProject::where('tahun_anggaran', '<', $currentYear)
            ->select('tahun_anggaran')->distinct()->orderBy('tahun_anggaran', 'desc')->pluck('tahun_anggaran');

        return view('projects.history', compact('projects', 'tahuns', 'currentYear'));
    }
}