<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckApprovedUser
{
    public function handle(Request $request, Closure $next)
    {
        // Cek apakah user sudah login, TAPI is_approved-nya masih false
        if (Auth::check() && !Auth::user()->is_approved) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Tendang balik ke halaman login bawa pesan merah
            return redirect()->route('login')->with('error', 'Akun Anda berhasil diverifikasi, namun masih MENUNGGU PERSETUJUAN Admin. Silakan hubungi Admin Kota.');
        }

        return $next($request);
    }
}
