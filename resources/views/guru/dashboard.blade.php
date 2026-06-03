@extends('layouts.dashboard')

@section('judul_halaman', 'Dashboard Guru')

@section('konten')
<div class="grid">
    <div class="card">
        <h3>Nama Guru</h3>
        <h2>{{ $guru->nama_guru }}</h2>
    </div>

    <div class="card">
        <h3>Role Guru</h3>
        <p>{{ implode(', ', $roles) ?: '-' }}</p>
    </div>

    <div class="card">
        <h3>Mata Pelajaran Diampu</h3>
        <h2>{{ $mapel->count() }}</h2>
    </div>
</div>
@endsection
