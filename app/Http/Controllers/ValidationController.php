<?php

namespace App\Http\Controllers;

use App\Models\DevelopmentProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ValidationController extends Controller
{
    public function index(Request $request)
    {
        $query = DevelopmentProject::with(['opd', 'activity', 'user']);

        if ($request->has('q') && $request->q != '') {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('nama_sub_kegiatan', 'like', '%' . $search . '%')
                    ->orWhereHas('opd', function ($subQ) use ($search) {
                        $subQ->where('nama_opd', 'like', '%' . $search . '%');
                    });
            });
        }

        $projects = $query->orderByRaw("FIELD(status, 'draft', 'pending_validation') DESC")
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('validation.index', compact('projects'));
    }

    public function show($id)
    {
        $project = DevelopmentProject::with(['opd', 'activity', 'user'])->findOrFail($id);
        return view('validation.show', compact('project'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,revision',
            'catatan_revisi' => 'nullable|string'
        ]);

        $project = DevelopmentProject::findOrFail($id);
        $project->status = $request->status;

        if ($request->status == 'revision') {
            $project->catatan_revisi = $request->catatan_revisi;
        } else {
            $project->catatan_revisi = null;
        }

        $project->save();

        return redirect()->back()->with('success', 'Status kegiatan berhasil diperbarui.');
    }

    public function approve($id)
    {
        // BUG FIX: Menggunakan DevelopmentProject alih-alih Project
        $project = DevelopmentProject::findOrFail($id);

        $project->update([
            'status' => 'approved'
        ]);

        // LOG AKTIVITAS: Validasi Setuju
        \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'aksi' => 'Validasi Setuju',
            'keterangan' => 'Mempublikasikan (Approve) data realisasi sub kegiatan: ' . $project->nama_sub_kegiatan,
            'ip_address' => request()->ip()
        ]);

        return redirect()->back()->with('success', 'Data realisasi berhasil disetujui!');
    }

    public function revisi(Request $request, $id)
    {
        $request->validate([
            'catatan_revisi' => 'required|string',
        ]);

        // BUG FIX: Menggunakan DevelopmentProject alih-alih Project
        $project = DevelopmentProject::findOrFail($id);

        $project->update([
            'status' => 'revision',
            'catatan_revisi' => $request->catatan_revisi
        ]);

        // LOG AKTIVITAS: Validasi Revisi
        \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'aksi' => 'Validasi Revisi',
            'keterangan' => 'Menolak dan memberikan catatan revisi pada sub kegiatan: ' . $project->nama_sub_kegiatan,
            'ip_address' => request()->ip()
        ]);

        return redirect()->back()->with('success', 'Catatan revisi berhasil dikirim ke Operator!');
    }
}