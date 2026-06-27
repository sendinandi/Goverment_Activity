<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // 1. Jalankan autentikasi bawaan (Cek kesesuaian email dan password)
        $request->authenticate();

        // 2. Ambil data user yang baru saja mencoba login
        $user = Auth::user();

        // 3. TAMBAHKAN PENGECEKAN STATUS APPROVAL DI SINI
        // Memastikan status 'is_approved' bernilai true (1)
        if (!$user->is_approved) {
            
            // Jika belum di-approve, paksa logout detik itu juga
            Auth::guard('web')->logout();

            // Batalkan session yang sempat terbentuk
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Tendang kembali ke halaman login dengan membawa pesan error/peringatan
            return redirect()->route('login')->with('error', 'Akun Anda belum disetujui oleh Admin. Silakan hubungi Admin atau tunggu proses verifikasi.');
        }

        // 4. Jika 'is_approved' bernilai true, alur dilanjutkan ke dashboard utama
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
