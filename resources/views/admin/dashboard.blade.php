@extends('layouts.dashboard')
@section('judul_halaman','Dashboard Admin')
@section('konten')
<div class="grid">
    <div class="card"><h3>Jumlah Guru</h3><h1>{{ $jumlahGuru }}</h1></div>
    <div class="card"><h3>Jumlah Siswa</h3><h1>{{ $jumlahSiswa }}</h1></div>
    <div class="card"><h3>Jumlah Kelas</h3><h1>{{ $jumlahKelas }}</h1></div>
    <div class="card"><h3>Mata Pelajaran</h3><h1>{{ $jumlahMapel }}</h1></div>
</div>
@endsection
