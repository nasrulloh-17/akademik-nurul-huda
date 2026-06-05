@extends('layouts.dashboard')

@section('judul_halaman', 'Slider')

@section('konten')
<form class="card" method="post" enctype="multipart/form-data" action="{{ route('admin.slider.simpan') }}">
    @csrf

    <div class="form-grid">
        <div>
            <label>Gambar Slider</label>
            <input type="file" name="gambar" accept="image/png,image/jpeg,image/webp" required>
        </div>

        <div>
            <label>Link Tujuan</label>
            <input name="link" placeholder="https://contoh.com/pendaftaran atau #kontak">
        </div>

        <label>
            <input type="checkbox" name="aktif" value="1" checked style="width:auto">
            Aktif
        </label>
    </div>

    <p class="muted">Gunakan gambar PNG ukuran 1000 x 1000 px agar tampil optimal di halaman utama.</p>

    <button class="btn">Simpan Slider</button>
</form>

<table>
    <tr>
        <th>Gambar</th>
        <th>Link</th>
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
            <td>
                @if($item->link)
                    <a href="{{ $item->link }}" target="_blank" rel="noopener">{{ $item->link }}</a>
                @else
                    <span class="muted">Tidak ada link</span>
                @endif
            </td>
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
