@extends('layouts.dashboard')
@section('judul_halaman','Input Nilai')
@section('konten')
<div class="card">
    @forelse($mapelGuru as $m)<a class="btn {{ $aktif && $aktif->id === $m->id ? '' : 'alt' }}" href="{{ route('guru.nilai',$m->id) }}">{{ $m->nama_mata_pelajaran }}</a> @empty Belum ada mata pelajaran yang diampu. @endforelse
</div>
@if($aktif)
<form method="post" action="{{ route('guru.nilai.simpan',$aktif->id) }}">@csrf
<div class="card"><h3>{{ $aktif->nama_mata_pelajaran }}</h3>
<table><tr><th>Nama Siswa</th><th>Kelas</th><th>Nilai Tugas</th><th>Nilai UTS</th><th>Nilai UAS</th><th>Catatan Guru</th></tr>
@foreach($siswa as $murid)
@php($n = $nilai[$murid->id] ?? null)
<tr><td>{{ $murid->nama_siswa }}</td><td>{{ $murid->nama_kelas }}</td><td><input name="nilai[{{ $murid->id }}][nilai_tugas]" value="{{ $n->nilai_tugas ?? 0 }}"></td><td><input name="nilai[{{ $murid->id }}][nilai_uts]" value="{{ $n->nilai_uts ?? 0 }}"></td><td><input name="nilai[{{ $murid->id }}][nilai_uas]" value="{{ $n->nilai_uas ?? 0 }}"></td><td><input name="nilai[{{ $murid->id }}][catatan_guru]" value="{{ $n->catatan_guru ?? '' }}"></td></tr>
@endforeach
</table><p><button class="btn">Simpan Nilai</button></p></div></form>
@endif
@endsection
