<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $guarded = []; // Izinkan semua kolom diisi

    // Relasi untuk mengambil nama siapa pelakunya
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}