<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index()
    {
        // Mengambil log terbaru dari database, tampilkan 20 data per halaman
        $logs = AuditLog::with('user')->latest()->paginate(20);
        
        return view('audit.index', compact('logs'));
    }
}