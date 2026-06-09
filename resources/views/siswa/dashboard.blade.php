@extends('layouts.dashboard')

@section('judul_halaman', 'Dashboard Siswa')

@section('konten')
<div class="grid">
    <div class="card">
        <h3>Nama Siswa</h3>
        <h2>{{ $siswa->nama_siswa }}</h2>
    </div>

    <div class="card">
        <h3>Jumlah Nilai</h3>
        <h2>{{ $totalNilai }}</h2>
    </div>

    <div class="card">
        <h3>Tagihan Belum Lunas</h3>
        <h2>Rp {{ number_format($totalTagihan, 0, ',', '.') }}</h2>
    </div>
</div>

<div class="card">
    <h3>Rincian Tagihan</h3>

    @php
        $sppMakan = $tagihanAdministrasi['SPP dan Makan']->jumlah ?? 0;
        $kelengkapan = $tagihanAdministrasi['Kelengkapan Sekolah']->jumlah ?? 0;
        $lainnya = $tagihanAdministrasi['Lainnya']->jumlah ?? 0;
        $totalAdministrasi = $sppMakan + $kelengkapan + $lainnya;
    @endphp

    <table>
        <tr>
            <th>SPP dan Makan</th>
            <th>Kelengkapan Sekolah</th>
            <th>Lainnya</th>
            <th>Total</th>
        </tr>
        <tr>
            <td>Rp {{ number_format($sppMakan, 0, ',', '.') }}</td>
            <td>Rp {{ number_format($kelengkapan, 0, ',', '.') }}</td>
            <td>Rp {{ number_format($lainnya, 0, ',', '.') }}</td>
            <td><strong>Rp {{ number_format($totalAdministrasi, 0, ',', '.') }}</strong></td>
        </tr>
    </table>
</div>
@endsection
