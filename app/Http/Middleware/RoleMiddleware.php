<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    // Tambahkan titik tiga (...) sebelum $roles agar bisa menerima banyak role
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Cek apakah user sudah login dan role-nya ada di dalam daftar yang diizinkan
        if ($request->user() && !in_array($request->user()->role, $roles)) {
            // Jika tidak ada di daftar, tolak!
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk membuka halaman ini.');
        }

        return $next($request);
    }
}
