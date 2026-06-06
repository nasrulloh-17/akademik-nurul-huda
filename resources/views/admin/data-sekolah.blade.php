@extends('layouts.dashboard')

@section('judul_halaman', 'Data Sekolah')

@section('konten')
<form class="card" method="post" action="{{ route('admin.data-sekolah.simpan') }}">
    @csrf

    <h3 style="margin-top:0">Data MTS</h3>

    <div class="form-grid">
        <label>
            Kepala MTs Ma'arif 20
            <input
                name="kepala_mts"
                value="{{ old('kepala_mts', $dataSekolah->kepala_mts ?? '') }}"
                placeholder="Nama kepala MTs Ma'arif 20"
            >
        </label>
    </div>

    <h3>Data SMA</h3>

    <div class="form-grid">
        <label>
            Kepala SMA Nurul Huda
            <input
                name="kepala_sma"
                value="{{ old('kepala_sma', $dataSekolah->kepala_sma ?? '') }}"
                placeholder="Nama kepala SMA Nurul Huda"
            >
        </label>
    </div>

    <p>
        <label>
            Alamat Sekolah
            <textarea name="alamat" placeholder="Alamat sekolah">{{ old('alamat', $dataSekolah->alamat ?? '') }}</textarea>
        </label>
    </p>

    <button class="btn">Simpan Data Sekolah</button>
</form>

<div class="card">
    <h3 style="margin-top:0">Ringkasan Data</h3>

    <table>
        <tr>
            <th>Jenjang</th>
            <th>Nama Sekolah</th>
            <th>Kepala Sekolah</th>
        </tr>
        <tr>
            <td>MTS</td>
            <td>MTs Ma'arif 20</td>
            <td>{{ $dataSekolah->kepala_mts ?? '-' }}</td>
        </tr>
        <tr>
            <td>SMA</td>
            <td>SMA Nurul Huda</td>
            <td>{{ $dataSekolah->kepala_sma ?? '-' }}</td>
        </tr>
    </table>

    <p>
        <strong>Alamat:</strong><br>
        {{ $dataSekolah->alamat ?? '-' }}
    </p>
</div>
@endsection
