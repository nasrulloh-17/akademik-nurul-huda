@extends('layouts.app')

@section('body')
<div class="shell" data-dashboard-shell>
    <aside class="side">
        <div class="side-head">
            <div class="brand">Yayasan Nurul Huda Munjuk</div>

            <button class="menu-toggle" type="button" data-menu-toggle aria-expanded="true" aria-label="Tutup menu">
                &times;
            </button>
        </div>

        <nav class="menu" data-dashboard-menu>
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
