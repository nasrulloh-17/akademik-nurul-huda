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
@endsection
