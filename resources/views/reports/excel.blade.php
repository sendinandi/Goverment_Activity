<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Program / Kegiatan / Sub Kegiatan</th>
            <th>Target Fisik</th>
            <th>Realisasi Fisik</th>
            <th>Satuan</th>
            <th>Pagu Anggaran</th>
            <th>Realisasi Anggaran</th>
            <th>Capaian Fisik (%)</th>
            <th>Penanggung Jawab</th>
        </tr>
    </thead>
    <tbody>
        @foreach($projects as $index => $p)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>
                {{ $p->nama_sub_kegiatan }}
                (Induk: {{ $p->activity->nama_kegiatan ?? '-' }})
            </td>
            <td>{{ $p->target_fisik_bulan_ini }}</td>
            <td>{{ $p->realisasi_fisik_bulan_ini }}</td>
            <td>{{ $p->satuan }}</td>
            <td>{{ $p->pagu_anggaran }}</td>
            <td>{{ $p->realisasi_anggaran }}</td>
            <td>{{ number_format($p->capaian_fisik, 1) }}%</td>
            <td>{{ $p->penanggung_jawab }}</td>
        </tr>
        @endforeach
    </tbody>
</table>