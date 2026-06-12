@extends('layouts.dashboard')

@section('judul_halaman', 'Dashboard Admin')

@section('konten')
<div class="grid">
    <div class="card">
        <h3>Jumlah Guru</h3>
        <h1>{{ $jumlahGuru }}</h1>
    </div>

    <div class="card">
        <h3>Jumlah Siswa</h3>
        <h1>{{ $jumlahSiswa }}</h1>
    </div>

    <div class="card">
        <h3>Jumlah Kelas</h3>
        <h1>{{ $jumlahKelas }}</h1>
    </div>

    <div class="card">
        <h3>Mata Pelajaran</h3>
        <h1>{{ $jumlahMapel }}</h1>
    </div>
</div>

<div class="card">
    <h3>Akses Input Nilai Wali Kelas</h3>
    <p class="muted">
        Jika diaktifkan, setiap wali kelas bisa menginput nilai semua mata pelajaran pada kelas walinya untuk sementara.
    </p>

    <p>
        Status:
        <strong>{{ $aksesNilaiWaliKelasAktif ? 'Aktif' : 'Nonaktif' }}</strong>
    </p>

    <form method="post" action="{{ route('admin.akses-nilai-wali-kelas') }}">
        @csrf
        <button class="btn {{ $aksesNilaiWaliKelasAktif ? 'danger' : '' }}">
            {{ $aksesNilaiWaliKelasAktif ? 'Nonaktifkan Akses Wali Kelas' : 'Aktifkan Akses Wali Kelas' }}
        </button>
    </form>
</div>
@endsection
