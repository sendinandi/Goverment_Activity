<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Relasi: Satu Program punya banyak Kegiatan
    public function activities()
    {
        return $this->hasMany(Activity::class, 'program_id');
    }
}
