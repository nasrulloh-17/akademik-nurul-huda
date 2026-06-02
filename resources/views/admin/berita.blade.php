@extends('layouts.dashboard')
@section('judul_halaman','Berita')
@section('konten')
<form class="card" method="post" enctype="multipart/form-data" action="{{ route('admin.berita.simpan') }}">@csrf
    <div class="form-grid"><input name="judul" placeholder="Judul Berita" required><input type="date" name="tanggal_berita"><input type="file" name="foto_kegiatan" accept="image/*"></div>
    <p><textarea name="isi" placeholder="Isi berita" required></textarea></p><button class="btn">Simpan Berita</button>
</form>
<table><tr><th>Foto Kegiatan</th><th>Judul</th><th>Tanggal Berita</th><th>Isi</th><th>Aksi</th></tr>
@foreach($berita as $item)<tr><td>@if($item->foto_kegiatan)<img class="thumb" src="{{ asset($item->foto_kegiatan) }}">@endif</td><td>{{ $item->judul }}</td><td>{{ $item->tanggal_berita }}</td><td>{{ Str::limit($item->isi,90) }}</td><td><form method="post" action="{{ route('admin.berita.hapus',$item->id) }}">@csrf<button class="btn danger">Hapus</button></form></td></tr>@endforeach
</table>
@endsection
