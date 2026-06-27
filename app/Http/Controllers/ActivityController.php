<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Program;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index()
    {
        $activities = Activity::with('program')->get();
        $programs = Program::all(); 

        return view('activities.index', compact('activities', 'programs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'program_id' => 'required', 
            'nama_kegiatan' => 'required',
        ]);

        Activity::create([
            'program_id' => $request->program_id,
            'nama_kegiatan' => $request->nama_kegiatan,
        ]);

        // LOG AKTIVITAS: Tambah Master Kegiatan
        \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'aksi' => 'Tambah Master Data',
            'keterangan' => 'Menambahkan Master Kegiatan Induk baru: ' . $request->nama_kegiatan,
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('activities.index')->with('success', 'Kegiatan berhasil ditambahkan!');
    }

    public function update(Request $request, Activity $activity)
    {
        $request->validate([
            'program_id' => 'required',
            'nama_kegiatan' => 'required',
        ]);

        $activity->update([
            'program_id' => $request->program_id,
            'nama_kegiatan' => $request->nama_kegiatan,
        ]);

        return redirect()->route('activities.index')->with('success', 'Kegiatan berhasil diperbarui!');
    }

    public function destroy($id)
    {
        Activity::destroy($id);
        return back()->with('success', 'Master Kegiatan berhasil dihapus!');
    }
}