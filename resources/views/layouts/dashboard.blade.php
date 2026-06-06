@extends('layouts.app')

@section('body')
@php
    $jenisPengguna = session('jenis_pengguna');
    $labelPeran = [
        'admin' => 'Administrator',
        'guru' => 'Guru',
        'siswa' => 'Siswa',
    ][$jenisPengguna] ?? 'Pengguna';
@endphp

<div class="shell" data-dashboard-shell>
    <aside class="side">
        <div class="side-head">
            <div class="brand">SIAKAD</div>

            <button class="menu-toggle" type="button" data-menu-toggle aria-expanded="true" aria-label="Tutup menu">
                &times;
            </button>
        </div>

        <div class="role-badge">{{ $labelPeran }}</div>

        <nav class="menu" data-dashboard-menu>
            @if($jenisPengguna === 'admin')
                <a class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">Dashboard</a>
                <a class="{{ request()->routeIs('admin.slider') ? 'active' : '' }}" href="{{ route('admin.slider') }}">Slider</a>
                <a class="{{ request()->routeIs('admin.berita') ? 'active' : '' }}" href="{{ route('admin.berita') }}">Berita</a>
                <a class="{{ request()->routeIs('admin.prestasi') ? 'active' : '' }}" href="{{ route('admin.prestasi') }}">Prestasi</a>
                <a class="{{ request()->routeIs('admin.galeri') ? 'active' : '' }}" href="{{ route('admin.galeri') }}">Galeri</a>
                <a class="{{ request()->routeIs('admin.informasi') ? 'active' : '' }}" href="{{ route('admin.informasi') }}">Informasi Sekolah</a>
                <a class="{{ request()->routeIs('admin.data-sekolah') ? 'active' : '' }}" href="{{ route('admin.data-sekolah') }}">Data Sekolah</a>
                <a class="{{ request()->routeIs('admin.guru') ? 'active' : '' }}" href="{{ route('admin.guru') }}">Guru</a>
                <a class="{{ request()->routeIs('admin.siswa') ? 'active' : '' }}" href="{{ route('admin.siswa') }}">Siswa</a>
                <a class="{{ request()->routeIs('admin.kelas') ? 'active' : '' }}" href="{{ route('admin.kelas') }}">Kelas</a>
                <a class="{{ request()->routeIs('admin.naik-kelas') ? 'active' : '' }}" href="{{ route('admin.naik-kelas') }}">Naik Kelas</a>
                <a class="{{ request()->routeIs('admin.mata-pelajaran') ? 'active' : '' }}" href="{{ route('admin.mata-pelajaran') }}">Mata Pelajaran</a>
            @elseif($jenisPengguna === 'guru')
                <a class="{{ request()->routeIs('guru.dashboard') ? 'active' : '' }}" href="{{ route('guru.dashboard') }}">Dashboard</a>
                <a class="{{ request()->routeIs('guru.nilai') ? 'active' : '' }}" href="{{ route('guru.nilai') }}">Input Nilai</a>
                <a class="{{ request()->routeIs('guru.kegiatan-tambahan') ? 'active' : '' }}" href="{{ route('guru.kegiatan-tambahan') }}">Kegiatan Tambahan</a>
                <a class="{{ request()->routeIs('guru.catatan') ? 'active' : '' }}" href="{{ route('guru.catatan') }}">Catatan Walikelas</a>
                <a class="{{ request()->routeIs('guru.data-siswa') ? 'active' : '' }}" href="{{ route('guru.data-siswa') }}">Data Siswa</a>
            @else
                <a class="{{ request()->routeIs('siswa.dashboard') ? 'active' : '' }}" href="{{ route('siswa.dashboard') }}">Dashboard</a>
                <a class="{{ request()->routeIs('siswa.biodata') ? 'active' : '' }}" href="{{ route('siswa.biodata') }}">Biodata</a>
                <a class="{{ request()->routeIs('siswa.raport') ? 'active' : '' }}" href="{{ route('siswa.raport') }}">Raport</a>
                <a class="{{ request()->routeIs('siswa.tagihan') ? 'active' : '' }}" href="{{ route('siswa.tagihan') }}">Tagihan</a>
            @endif

            <a class="{{ request()->routeIs('password.form') ? 'active' : '' }}" href="{{ route('password.form') }}">Ubah Password</a>
        </nav>
    </aside>

    <main class="content">
        <div class="top">
            <div>
                <h2 style="margin:0">@yield('judul_halaman')</h2>
                <div class="muted">{{ session('nama_pengguna') }}</div>
            </div>

            <form method="post" action="{{ route('keluar') }}">
                @csrf
                <button class="btn alt">Keluar</button>
            </form>
        </div>

        @if(session('sukses'))
            <div class="alert">{{ session('sukses') }}</div>
        @endif

        @if($errors->any())
            <div class="alert">{{ $errors->first() }}</div>
        @endif

        @yield('konten')
    </main>
</div>

<script>
    document.querySelectorAll('[data-dashboard-shell]').forEach((shell) => {
        const toggle = shell.querySelector('[data-menu-toggle]');
        const mobileQuery = window.matchMedia('(max-width: 800px)');

        const setMenuState = (collapsed) => {
            shell.classList.toggle('menu-collapsed', collapsed);
            toggle.setAttribute('aria-expanded', String(! collapsed));
            toggle.setAttribute('aria-label', collapsed ? 'Buka menu' : 'Tutup menu');
            toggle.innerHTML = collapsed ? '&#9776;' : '&times;';
        };

        toggle.addEventListener('click', () => {
            setMenuState(! shell.classList.contains('menu-collapsed'));
        });

        setMenuState(mobileQuery.matches);

        mobileQuery.addEventListener('change', (event) => {
            setMenuState(event.matches);
        });
    });
</script>
@endsection
