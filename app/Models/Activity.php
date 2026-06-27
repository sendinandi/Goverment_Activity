<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $guarded = ['id']; // Mengizinkan semua kolom diisi kecuali ID

    // TAMBAHAN BARU: Relasi Kegiatan dimiliki oleh 1 Program
    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    // Relasi: Satu Kegiatan (Induk) punya Banyak Sub Kegiatan (Project)
    public function projects()
    {
        return $this->hasMany(Project::class, 'activity_id');
    }
}
