@extends('layouts.dashboard')
@section('judul_halaman','Raport')
@section('konten')
<div class="card"><a class="btn" href="{{ route('siswa.raport.cetak') }}" target="_blank">Cetak Nilai ke PDF</a></div>
<div class="card"><h3>{{ $siswa->nama_siswa }} - {{ $siswa->nama_kelas }}</h3><table><tr><th>Mata Pelajaran</th><th>Guru</th><th>Tugas</th><th>UTS</th><th>UAS</th><th>Rata-rata</th><th>Catatan Guru</th></tr>
@foreach($nilai as $n)<tr><td>{{ $n->nama_mata_pelajaran }}</td><td>{{ $n->nama_guru }}</td><td>{{ $n->nilai_tugas }}</td><td>{{ $n->nilai_uts }}</td><td>{{ $n->nilai_uas }}</td><td>{{ number_format(($n->nilai_tugas+$n->nilai_uts+$n->nilai_uas)/3,2) }}</td><td>{{ $n->catatan_guru }}</td></tr>@endforeach
</table></div>
<div class="card"><h3>Catatan Walikelas</h3>@forelse($catatan as $c)<p>{{ $c->catatan }}</p>@empty <p>Belum ada catatan.</p>@endforelse</div>
@endsection
