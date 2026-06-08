@extends('layouts.dashboard')

@section('judul_halaman', 'Biodata Guru')

@section('konten')
<form class="card" method="post" action="{{ route('guru.biodata.simpan') }}">
    @csrf

    <div class="form-grid">
        <label>
            ID Guru
            <input value="{{ $guru->id_guru }}" readonly>
        </label>

        <label>
            Tanggal Lahir
            <input
                type="date"
                value="{{ $guru->tanggal_lahir }}"
                readonly
            >
        </label>

        <label>
            Nama Guru
            <input name="nama_guru" value="{{ old('nama_guru', $guru->nama_guru) }}" required>
        </label>

        <label>
            Jenis Kelamin
            <select name="jenis_kelamin" required>
                <option value="">Pilih jenis kelamin</option>
                <option value="Laki-laki" @selected(old('jenis_kelamin', $guru->jenis_kelamin) === 'Laki-laki')>
                    Laki-laki
                </option>
                <option value="Perempuan" @selected(old('jenis_kelamin', $guru->jenis_kelamin) === 'Perempuan')>
                    Perempuan
                </option>
            </select>
        </label>

        <label>
            Telepon
            <input name="telepon" value="{{ old('telepon', $guru->telepon) }}" placeholder="Nomor telepon">
        </label>
    </div>

    <p>
        <label>
            Alamat
            <textarea name="alamat" placeholder="Alamat">{{ old('alamat', $guru->alamat) }}</textarea>
        </label>
    </p>

    <p class="muted">
        Tanggal lahir tidak dapat diubah dari halaman ini karena digunakan sebagai dasar ID guru.
    </p>

    <button class="btn">Simpan Biodata</button>
</form>
@endsection
