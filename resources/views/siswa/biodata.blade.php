@extends('layouts.dashboard')

@section('judul_halaman', 'Biodata Siswa')

@section('konten')
<form class="card" method="post" enctype="multipart/form-data" action="{{ route('siswa.biodata.simpan') }}">
    @csrf

    <div class="form-grid">
        <input value="{{ $siswa->nis }}" disabled>
        <input value="{{ $siswa->nama_siswa }}" disabled>
        <input value="{{ $kelas->nama_kelas ?? '-' }}" disabled>

        <select name="jenis_kelamin">
            <option>{{ $siswa->jenis_kelamin }}</option>
            <option>Laki-laki</option>
            <option>Perempuan</option>
        </select>

        <input name="tempat_lahir" value="{{ $siswa->tempat_lahir }}" placeholder="Tempat Lahir">
        <input type="date" name="tanggal_lahir" value="{{ $siswa->tanggal_lahir }}">
        <input name="telepon" value="{{ $siswa->telepon }}" placeholder="Telepon">
        <input type="file" name="foto_profil" accept="image/*">
    </div>

    <p>
        <textarea name="alamat" placeholder="Alamat">{{ $siswa->alamat }}</textarea>
    </p>

    @if($siswa->foto_profil)
        <img class="thumb" src="{{ asset($siswa->foto_profil) }}" alt="{{ $siswa->nama_siswa }}">
    @endif

    <p>
        <button class="btn">Simpan Biodata</button>
    </p>
</form>
@endsection
