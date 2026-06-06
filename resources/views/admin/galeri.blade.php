@extends('layouts.dashboard')

@section('judul_halaman', 'Galeri Foto')

@section('konten')
<form class="card" method="post" enctype="multipart/form-data" action="{{ route('admin.galeri.simpan') }}">
    @csrf

    <div class="form-grid">
        <input name="judul" placeholder="Judul Foto">
        <input type="file" name="foto" accept="image/*" required>
    </div>

    <p class="muted">Foto akan tampil di halaman utama dengan susunan seperti galeri Pinterest.</p>

    <button class="btn">Simpan Foto</button>
</form>

<table>
    <tr>
        <th>Foto</th>
        <th>Judul</th>
        <th>Aksi</th>
    </tr>

    @foreach($galeri as $item)
        <tr>
            <td>
                <img class="thumb" src="{{ asset($item->foto) }}" alt="{{ $item->judul ?: 'Foto galeri' }}">
            </td>
            <td>{{ $item->judul ?: '-' }}</td>
            <td>
                <form method="post" action="{{ route('admin.galeri.hapus', $item->id) }}">
                    @csrf
                    <button class="btn danger">Hapus</button>
                </form>
            </td>
        </tr>
    @endforeach
</table>
@endsection
