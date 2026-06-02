@extends('layouts.dashboard')
@section('judul_halaman','Informasi Sekolah')
@section('konten')
<form class="card" method="post" action="{{ route('admin.informasi.simpan') }}">@csrf
    <div class="form-grid"><input name="judul" placeholder="Judul Informasi" required><input name="kontak" placeholder="Kontak"></div>
    <p><textarea name="isi" placeholder="Isi informasi" required></textarea></p><button class="btn">Simpan Informasi</button>
</form>
<table><tr><th>Judul</th><th>Isi</th><th>Kontak</th><th>Aksi</th></tr>
@foreach($informasi as $item)<tr><td>{{ $item->judul }}</td><td>{{ $item->isi }}</td><td>{{ $item->kontak }}</td><td><form method="post" action="{{ route('admin.informasi.hapus',$item->id) }}">@csrf<button class="btn danger">Hapus</button></form></td></tr>@endforeach
</table>
@endsection
