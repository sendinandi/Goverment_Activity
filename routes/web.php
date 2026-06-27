<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ValidationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\ManualBookController;

// 1. Redirect halaman utama '/' langsung ke '/dashboard'
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// ====================================================================
// 🔒 PAYUNG BESAR: WAJIB LOGIN, VERIFIKASI EMAIL, & DI-APPROVE ADMIN
// ====================================================================
Route::middleware(['auth', 'verified', \App\Http\Middleware\CheckApprovedUser::class])->group(function () {
    
    // --- GRUP 1: RUTE UMUM (Semua Role Bisa Akses Dashboard & Profil) ---
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/manual-book', [ManualBookController::class, 'index'])->name('manual-book.index');
    Route::get('/manual-book/download', [ManualBookController::class, 'download'])->name('manual-book.download');
    
    // Jalur Data Endpoint (API/AJAX untuk Grafik Dashboard)
    Route::get('/api/map-data', [DashboardController::class, 'getMapData'])->name('api.map-data');
    Route::get('/api/activities/{opd_id}', [DashboardController::class, 'getActivities']);
    Route::get('/api/sub-activities/{activity_id}', [DashboardController::class, 'getSubActivities']);

    // Profile bawaan Breeze
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- GRUP 2: RUTE OPERATOR OPD (Transaksi & Riwayat Proyek) ---
    Route::resource('projects', ProjectController::class);
    Route::get('/history', [ProjectController::class, 'history'])->name('projects.history');
    Route::get('/api/get-activities/{programId}', [ProjectController::class, 'getActivitiesByProgram']);

    // --- GRUP 3: RUTE KHUSUS VERIFIKATOR ---
    Route::middleware(['role:verifikator'])->group(function () {
        Route::get('/validation', [ValidationController::class, 'index'])->name('validation.index');
        Route::patch('/validation/{id}/approve', [ValidationController::class, 'approve'])->name('validation.approve');
        Route::patch('/validation/{id}/revisi', [ValidationController::class, 'revisi'])->name('validation.revisi');
    });

    // --- GRUP 4: RUTE LAPORAN (Pimpinan, Admin OPD, Verifikator) ---
    Route::middleware(['role:pimpinan,admin_opd,verifikator'])->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export-excel', [ReportController::class, 'exportExcel'])->name('reports.excel');
        Route::get('/reports/print', [ReportController::class, 'print'])->name('reports.print');
        Route::get('/reports/{id}', [ReportController::class, 'show'])->name('reports.show');
    });

    // --- GRUP 5: RUTE MASTER DATA & PENGGUNA (Khusus Admin) ---
    Route::middleware(['role:admin_opd'])->group(function () {
        // Manajemen Pengguna (Memanggil fungsi dari UserController)
        Route::resource('users', UserController::class)->except(['create', 'show', 'edit']);
        Route::patch('/users/{id}/approve', [UserController::class, 'approve'])->name('users.approve');

        // Master Program & Master Kegiatan
        Route::resource('programs', ProgramController::class);
        Route::resource('activities', ActivityController::class)->except(['create', 'show', 'edit']);
        
        // Audit Log
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit.index');
    });

});
// ====================================================================

// Memanggil routes auth bawaan Breeze (Login, Register, dll)
require __DIR__ . '/auth.php';