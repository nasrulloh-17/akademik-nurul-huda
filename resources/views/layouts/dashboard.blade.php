@extends('layouts.app')
@section('body')
<div class="shell">
    <aside class="side">
        <div class="brand">Yayasan Nurul Huda Munjuk</div>
        <nav class="menu">
            @if(session('jenis_pengguna') === 'admin')
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <a href="{{ route('admin.slider') }}">Slider</a>
                <a href="{{ route('admin.berita') }}">Berita</a>
                <a href="{{ route('admin.informasi') }}">Informasi Sekolah</a>
                <a href="{{ route('admin.guru') }}">Guru</a>
                <a href="{{ route('admin.siswa') }}">Siswa</a>
                <a href="{{ route('admin.kelas') }}">Kelas</a>
                <a href="{{ route('admin.mata-pelajaran') }}">Mata Pelajaran</a>
            @elseif(session('jenis_pengguna') === 'guru')
                <a href="{{ route('guru.dashboard') }}">Dashboard</a>
                <a href="{{ route('guru.nilai') }}">Input Nilai</a>
                <a href="{{ route('guru.catatan') }}">Catatan Walikelas</a>
                <a href="{{ route('guru.data-siswa') }}">Data Siswa</a>
            @else
                <a href="{{ route('siswa.dashboard') }}">Dashboard</a>
                <a href="{{ route('siswa.biodata') }}">Biodata</a>
                <a href="{{ route('siswa.raport') }}">Raport</a>
                <a href="{{ route('siswa.tagihan') }}">Tagihan</a>
            @endif
        </nav>
    </aside>
    <main class="content">
        <div class="top">
            <div>
                <h1 style="margin:0">@yield('judul_halaman')</h1>
                <div class="muted">{{ session('nama_pengguna') }}</div>
            </div>
            <form method="post" action="{{ route('keluar') }}">@csrf<button class="btn alt">Keluar</button></form>
        </div>
        @if(session('sukses'))<div class="alert">{{ session('sukses') }}</div>@endif
        @if($errors->any())<div class="alert">{{ $errors->first() }}</div>@endif
        @yield('konten')
    </main>
</div>
@endsection
