<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class RekapExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $projects;
    protected $rowNumber = 0; // Variabel untuk nomor urut

    // Menerima data dari Controller
    public function __construct($projects)
    {
        $this->projects = $projects;
    }

    // Mengambil data collection
    public function collection()
    {
        return $this->projects;
    }

    // Membuat Judul Kolom (Header) Excel
    public function headings(): array
    {
        return [
            'No',
            'Sub Kegiatan',
            'Kegiatan Induk',
            'Target Fisik',
            'Realisasi Fisik',
            'Satuan',
            'Pagu Anggaran',
            'Realisasi Anggaran',
            'Capaian Fisik (%)',
            'Penanggung Jawab'
        ];
    }

    // Memetakan isi baris datanya
    public function map($p): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $p->nama_sub_kegiatan,
            $p->activity->nama_kegiatan ?? '-',
            $p->target_fisik_bulan_ini,
            $p->realisasi_fisik_bulan_ini,
            $p->satuan,
            $p->pagu_anggaran,
            $p->realisasi_anggaran,
            number_format((float)$p->capaian_fisik, 1) . '%',
            $p->penanggung_jawab
        ];
    }
}
