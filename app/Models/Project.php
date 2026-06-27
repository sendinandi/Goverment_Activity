<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    // KASIH TAHU LARAVEL PAKAI TABEL INI:
    protected $table = 'development_projects';

    protected $guarded = ['id'];

    public function activity()
    {
        return $this->belongsTo(Activity::class, 'activity_id');
    }

    public function opd()
    {
        return $this->belongsTo(Opd::class, 'opd_id');
    }
}
