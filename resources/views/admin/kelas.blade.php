@extends('layouts.dashboard')

@section('judul_halaman', 'Kelas')

@section('konten')
<form class="card" method="post" action="{{ route('admin.kelas.simpan') }}">
    @csrf

    <div class="form-grid">
        <input name="nama_kelas" placeholder="Nama Kelas" required>
        <input name="tingkat" placeholder="Tingkat">
        <input name="keterangan" placeholder="Keterangan">
    </div>

    <button class="btn">Simpan Kelas</button>
</form>

@foreach($kelas as $k)
    <div class="card">
        <h3>{{ $k->nama_kelas }}</h3>

        <table>
            <tr>
                <th>NIS</th>
                <th>Nama Siswa</th>
                <th>Jenis Kelamin</th>
            </tr>

            @forelse(($siswa[$k->id] ?? []) as $murid)
                <tr>
                    <td>{{ $murid->nis }}</td>
                    <td>{{ $murid->nama_siswa }}</td>
                    <td>{{ $murid->jenis_kelamin }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">Belum ada siswa.</td>
                </tr>
            @endforelse
        </table>
    </div>
@endforeach
@endsection
