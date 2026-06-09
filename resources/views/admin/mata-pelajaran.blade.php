@extends('layouts.dashboard')

@section('judul_halaman', 'Mata Pelajaran')

@section('konten')
<form class="card" method="post" action="{{ route('admin.mata-pelajaran.simpan') }}">
    @csrf

    <div class="form-grid">
        <input name="nama_mata_pelajaran" placeholder="Nama Mata Pelajaran" required>

        <select name="jenis_pelajaran" required>
            <option value="Formal">Formal</option>
            <option value="Non formal">Non formal</option>
        </select>

        <select name="kelas_id">
            <option value="">Pilih Kelas</option>
            @foreach($kelas as $item)
                <option value="{{ $item->id }}">{{ $item->nama_kelas }}</option>
            @endforeach
        </select>

        <select name="guru_id">
            <option value="">Pilih Guru Pengampu</option>
            @foreach($guru as $g)
                <option value="{{ $g->id }}">{{ $g->nama_guru }}</option>
            @endforeach
        </select>
    </div>

    <button class="btn">Simpan Mata Pelajaran</button>
</form>

<table>
    <tr>
        <th>Nama Mata Pelajaran</th>
        <th>Jenis Pelajaran</th>
        <th>Kelas</th>
        <th>Guru Pengampu</th>
        <th>Ubah</th>
        <th>Hapus</th>
    </tr>

    @foreach($mapel as $m)
        <tr>
            <td>
                <form id="ubah-mapel-{{ $m->id }}" method="post" action="{{ route('admin.mata-pelajaran.ubah', $m->id) }}">
                    @csrf
                </form>
                <input form="ubah-mapel-{{ $m->id }}" name="nama_mata_pelajaran" value="{{ $m->nama_mata_pelajaran }}" required>
            </td>
            <td>
                <select form="ubah-mapel-{{ $m->id }}" name="jenis_pelajaran" required>
                    <option value="Formal" @selected(($m->jenis_pelajaran ?? 'Formal') === 'Formal')>Formal</option>
                    <option value="Non formal" @selected(($m->jenis_pelajaran ?? 'Formal') === 'Non formal')>Non formal</option>
                </select>
            </td>
            <td>
                <select form="ubah-mapel-{{ $m->id }}" name="kelas_id">
                    <option value="">Pilih Kelas</option>
                    @foreach($kelas as $item)
                        <option value="{{ $item->id }}" @selected((int) $m->kelas_id === $item->id)>
                            {{ $item->nama_kelas }}
                        </option>
                    @endforeach
                </select>
            </td>
            <td>
                <select form="ubah-mapel-{{ $m->id }}" name="guru_id">
                    <option value="">Pilih Guru Pengampu</option>
                    @foreach($guru as $g)
                        <option value="{{ $g->id }}" @selected((int) $m->guru_id === $g->id)>
                            {{ $g->nama_guru }}
                        </option>
                    @endforeach
                </select>
            </td>
            <td>
                <button class="btn" form="ubah-mapel-{{ $m->id }}">Ubah</button>
            </td>

            <td>
                <form method="post" action="{{ route('admin.mata-pelajaran.hapus', $m->id) }}">
                    @csrf
                    <button class="btn danger" onclick="return confirm('Hapus mata pelajaran ini?')">Hapus</button>
                </form>
            </td>
        </tr>
    @endforeach
</table>
@endsection
