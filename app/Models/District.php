<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    protected $guarded = [];

    // Relasi: Satu kecamatan punya banyak proyek
    public function projects()
    {
        return $this->hasMany(DevelopmentProject::class);
    }
}
