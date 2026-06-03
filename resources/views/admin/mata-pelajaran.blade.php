@extends('layouts.dashboard')

@section('judul_halaman', 'Mata Pelajaran')

@section('konten')
<form class="card" method="post" action="{{ route('admin.mata-pelajaran.simpan') }}">
    @csrf

    <div class="form-grid">
        <input name="nama_mata_pelajaran" placeholder="Nama Mata Pelajaran" required>

        <select name="guru_id">
            <option value="">Pilih Guru Pengampu</option>
            @foreach($guru as $g)
                <option value="{{ $g->id }}">{{ $g->nama_guru }}</option>
            @endforeach
        </select>

        <input name="keterangan" placeholder="Keterangan">
    </div>

    <button class="btn">Simpan Mata Pelajaran</button>
</form>

<table>
    <tr>
        <th>Nama Mata Pelajaran</th>
        <th>Guru Pengampu</th>
        <th>Keterangan</th>
    </tr>

    @foreach($mapel as $m)
        <tr>
            <td>{{ $m->nama_mata_pelajaran }}</td>
            <td>{{ $m->nama_guru }}</td>
            <td>{{ $m->keterangan }}</td>
        </tr>
    @endforeach
</table>
@endsection
