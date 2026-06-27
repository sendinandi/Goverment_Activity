<?php

namespace App\Http\Controllers;

use App\Models\Program;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    public function index()
    {
        $programs = Program::orderBy('nama_bagian')->get();
        return view('programs.index', compact('programs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_bagian' => 'required|string|max:255',
            'nama_program' => 'required|string|max:255',
        ]);

        Program::create([
            'nama_bagian' => $request->nama_bagian,
            'nama_program' => $request->nama_program,
        ]);

        // LOG AKTIVITAS: Tambah Master Program
        \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'aksi' => 'Tambah Master Data',
            'keterangan' => 'Menambahkan Master Program Utama baru: ' . $request->nama_program . ' pada bagian ' . $request->nama_bagian,
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('programs.index')->with('success', 'Program dan Bagian berhasil ditambahkan!');
    }

    public function update(Request $request, Program $program)
    {
        $request->validate([
            'nama_bagian' => 'required|string|max:255',
            'nama_program' => 'required|string|max:255',
        ]);

        $program->update([
            'nama_bagian' => $request->nama_bagian,
            'nama_program' => $request->nama_program,
        ]);

        return redirect()->route('programs.index')->with('success', 'Program dan Bagian berhasil diperbarui!');
    }

    public function destroy(Program $program)
    {
        $program->delete();
        return redirect()->route('programs.index')->with('success', 'Program dan Bagian berhasil dihapus!');
    }
}