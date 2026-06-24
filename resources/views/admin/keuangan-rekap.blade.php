@extends('layouts.dashboard')

@section('judul_halaman', 'Rekap Keuangan')

@section('konten')
<div class="grid">
    <div class="card">
        <h3>Total Tagihan</h3>
        <p style="font-size:28px;font-weight:800;margin:0">
            Rp {{ number_format($rekap->sum('total_tagihan'), 0, ',', '.') }}
        </p>
    </div>
    <div class="card">
        <h3>Total Terbayar</h3>
        <p style="font-size:28px;font-weight:800;margin:0">
            Rp {{ number_format($rekap->sum('total_bayar'), 0, ',', '.') }}
        </p>
    </div>
    <div class="card">
        <h3>Total Sisa</h3>
        <p style="font-size:28px;font-weight:800;margin:0">
            Rp {{ number_format($rekap->sum(fn ($item) => max(0, $item->total_tagihan - $item->total_bayar)), 0, ',', '.') }}
        </p>
    </div>
</div>

<div class="card" style="overflow-x:auto">
    <table>
        <tr>
            <th>NIS</th>
            <th>Nama Siswa</th>
            <th>Kelas</th>
            <th>Total Tagihan</th>
            <th>Terbayar</th>
            <th>Sisa</th>
            <th>Status</th>
        </tr>

        @foreach($rekap as $item)
            @php
                $sisa = max(0, (float) $item->total_tagihan - (float) $item->total_bayar);
            @endphp
            <tr>
                <td>{{ $item->nis }}</td>
                <td>{{ $item->nama_siswa }}</td>
                <td>{{ $item->nama_kelas ?? '-' }}</td>
                <td>Rp {{ number_format($item->total_tagihan, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($item->total_bayar, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($sisa, 0, ',', '.') }}</td>
                <td>{{ $sisa <= 0 ? 'Lunas' : ($item->total_bayar > 0 ? 'Sebagian' : 'Belum Lunas') }}</td>
            </tr>
        @endforeach
    </table>
</div>
@endsection
