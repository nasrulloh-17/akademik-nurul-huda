@extends('layouts.dashboard')

@section('judul_halaman', 'Catatan Walikelas')

@section('konten')
<form class="card" method="post" action="{{ route('guru.catatan.simpan') }}">
    @csrf

    <div class="form-grid">
        <select name="siswa_id" required>
            <option value="">Pilih Siswa</option>
            @foreach($siswa as $murid)
                <option value="{{ $murid->id }}">{{ $murid->nama_siswa }} - {{ $murid->nama_kelas }}</option>
            @endforeach
        </select>
    </div>

    <p>
        <textarea name="catatan" placeholder="Catatan untuk siswa"></textarea>
    </p>

    <button class="btn">Simpan Catatan</button>
</form>

<table>
    <tr>
        <th>Siswa</th>
        <th>Kelas</th>
        <th>Catatan Terakhir</th>
    </tr>

    @foreach($siswa as $murid)
        <tr>
            <td>{{ $murid->nama_siswa }}</td>
            <td>{{ $murid->nama_kelas }}</td>
            <td>{{ optional(($catatan[$murid->id] ?? collect())->first())->catatan }}</td>
        </tr>
    @endforeach
</table>
@endsection
