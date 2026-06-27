<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DevelopmentProject extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'opd_id',
        'activity_id', // Pengganti Sector
        'nama_sub_kegiatan',
        'bulan',
        'tahun_anggaran',
        'pagu_anggaran',
        'realisasi_anggaran',
        'satuan',

        // Data Fisik Baru
        'sasaran_fisik_desc',
        'total_target_fisik_tahunan',
        'target_fisik_bulan_ini',
        'target_persen_bulan_ini',
        'realisasi_fisik_bulan_ini',
        'realisasi_persen_bulan_ini',
        'capaian_fisik',

        'status',
        'catatan_revisi',
        'kendala',
        'tindak_lanjut',
        'penanggung_jawab',

        // HAPUS sector_id dan district_id DARI SINI
    ];

    // Relasi ke Kecamatan
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke OPD
    public function opd()
    {
        return $this->belongsTo(Opd::class);
    }

    // Relasi ke Activity (INI YANG DICARI LARAVEL & BIKIN ERROR)
    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }
}
