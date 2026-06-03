@extends('layouts.dashboard')

@section('judul_halaman', 'Slider')

@section('konten')
<form class="card" method="post" enctype="multipart/form-data" action="{{ route('admin.slider.simpan') }}">
    @csrf

    <div class="form-grid">
        <input name="judul" placeholder="Judul Slider" required>
        <input type="file" name="gambar" accept="image/*">

        <label>
            <input type="checkbox" name="aktif" value="1" checked style="width:auto">
            Aktif
        </label>
    </div>

    <p>
        <textarea name="deskripsi" placeholder="Deskripsi"></textarea>
    </p>

    <button class="btn">Simpan Slider</button>
</form>

<table>
    <tr>
        <th>Gambar</th>
        <th>Judul</th>
        <th>Deskripsi</th>
        <th>Status</th>
        <th>Aksi</th>
    </tr>

    @foreach($slider as $item)
        <tr>
            <td>
                @if($item->gambar)
                    <img class="thumb" src="{{ asset($item->gambar) }}" alt="{{ $item->judul }}">
                @endif
            </td>
            <td>{{ $item->judul }}</td>
            <td>{{ $item->deskripsi }}</td>
            <td>{{ $item->aktif ? 'Aktif' : 'Nonaktif' }}</td>
            <td>
                <form method="post" action="{{ route('admin.slider.hapus', $item->id) }}">
                    @csrf
                    <button class="btn danger">Hapus</button>
                </form>
            </td>
        </tr>
    @endforeach
</table>
@endsection
