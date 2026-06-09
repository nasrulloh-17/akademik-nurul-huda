@extends('layouts.dashboard')

@section('judul_halaman', 'Download CSV')

@section('konten')
<div class="grid">
    <div class="card">
        <h3>Data Guru</h3>
        <p class="muted">Download data lengkap seluruh guru, diurutkan berdasarkan nama A-Z.</p>
        <a class="btn" href="{{ route('guru.download-csv.unduh', 'guru') }}">Download Data Guru</a>
    </div>

    <div class="card">
        <h3>Data Siswa</h3>
        <p class="muted">Download data lengkap seluruh siswa, diurutkan berdasarkan nama A-Z.</p>
        <a class="btn" href="{{ route('guru.download-csv.unduh', 'siswa') }}">Download Data Siswa</a>
    </div>
</div>

<div class="card">
    <h3>Data Nilai Akhir Siswa</h3>
    <p class="muted">
        Data nilai akhir diambil dari tahun ajaran aktif:
        {{ $tahunAjaran->nama_tahun_ajaran }} - {{ ucfirst($tahunAjaran->semester ?? 'ganjil') }}.
    </p>

    <form method="get" action="{{ route('guru.download-csv.unduh', 'nilai-akhir') }}">
        <div class="form-grid">
            <select name="kelas_id" required>
                <option value="">Pilih kelas</option>
                @foreach($kelas as $item)
                    <option value="{{ $item->id }}">{{ $item->nama_kelas }}</option>
                @endforeach
            </select>

            <button class="btn">Download Nilai Akhir</button>
        </div>
    </form>
</div>

<div class="card">
    <h3>Data Ketidakhadiran Siswa</h3>
    <p class="muted">
        Data ketidakhadiran diambil dari input kegiatan tambahan kategori Kehadiran pada tahun ajaran aktif.
    </p>

    <form method="get" action="{{ route('guru.download-csv.unduh', 'ketidakhadiran') }}">
        <div class="form-grid">
            <select name="kelas_id" required>
                <option value="">Pilih kelas</option>
                @foreach($kelas as $item)
                    <option value="{{ $item->id }}">{{ $item->nama_kelas }}</option>
                @endforeach
            </select>

            <button class="btn">Download Ketidakhadiran</button>
        </div>
    </form>
</div>
@endsection
