@extends('layouts.dashboard')
@section('judul_halaman','Data Siswa')
@section('konten')
@foreach($kelas as $k)
<div class="card"><h3>{{ $k->nama_kelas }}</h3><table><tr><th>NIS</th><th>Nama Siswa</th><th>Jenis Kelamin</th><th>Telepon</th><th>Alamat</th></tr>@forelse(($siswa[$k->id] ?? []) as $murid)<tr><td>{{ $murid->nis }}</td><td>{{ $murid->nama_siswa }}</td><td>{{ $murid->jenis_kelamin }}</td><td>{{ $murid->telepon }}</td><td>{{ $murid->alamat }}</td></tr>@empty<tr><td colspan="5">Belum ada siswa.</td></tr>@endforelse</table></div>
@endforeach
@endsection
