<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Opd;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('opd')->latest()->get();
        $opds = Opd::orderBy('nama_opd')->get();

        return view('users.index', compact('users', 'opds'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string',
            'opd_id' => 'nullable|exists:opds,id'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'opd_id' => $request->role === 'admin' ? null : $request->opd_id,
        ]);

        return back()->with('success', 'Pengguna baru berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => 'required|string',
            'opd_id' => 'nullable|exists:opds,id'
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'opd_id' => $request->role === 'admin' ? null : $request->opd_id,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return back()->with('success', 'Data pengguna berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if (auth()->id() == $user->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri!');
        }

        // LOG AKTIVITAS: Hapus Akun (Harus sebelum data dihapus dari DB)
        \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'aksi' => 'Hapus Akun',
            'keterangan' => 'Menghapus permanen akun pengguna: ' . $user->name . ' dari sistem',
            'ip_address' => request()->ip()
        ]);

        $user->delete();
        return back()->with('success', 'Pengguna berhasil dihapus!');
    }

    public function approve($id)
    {
        $user = \App\Models\User::findOrFail($id);
        $user->update(['is_approved' => true]);

        // LOG AKTIVITAS: Aktivasi Akun
        \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'aksi' => 'Aktivasi Akun',
            'keterangan' => 'Mengaktifkan dan menyetujui akun pengguna: ' . $user->name . ' (' . $user->email . ')',
            'ip_address' => request()->ip()
        ]);

        return redirect()->back()->with('success', 'Akun OPD berhasil disetujui dan diaktifkan!');
    }
}