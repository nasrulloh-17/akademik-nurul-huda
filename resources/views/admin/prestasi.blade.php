@extends('layouts.dashboard')

@section('judul_halaman', 'Prestasi')

@section('konten')
<form class="card" method="post" enctype="multipart/form-data" action="{{ route('admin.prestasi.simpan') }}">
    @csrf

    <div class="form-grid">
        <input name="judul" placeholder="Judul Prestasi" required>
        <input type="file" name="foto" accept="image/*">
    </div>

    <p>
        <textarea name="keterangan" placeholder="Keterangan singkat"></textarea>
    </p>

    <button class="btn">Simpan Prestasi</button>
</form>

<table>
    <tr>
        <th>Foto</th>
        <th>Judul</th>
        <th>Keterangan</th>
        <th>Aksi</th>
    </tr>

    @foreach($prestasi as $item)
        <tr>
            <td>
                @if($item->foto)
                    <img class="thumb" src="{{ asset($item->foto) }}" alt="{{ $item->judul }}">
                @endif
            </td>
            <td>{{ $item->judul }}</td>
            <td>{{ Str::limit($item->keterangan, 90) }}</td>
            <td>
                <form method="post" action="{{ route('admin.prestasi.hapus', $item->id) }}">
                    @csrf
                    <button class="btn danger">Hapus</button>
                </form>
            </td>
        </tr>
    @endforeach
</table>
@endsection
