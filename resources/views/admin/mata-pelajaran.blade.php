@extends('layouts.dashboard')

@section('judul_halaman', 'Mata Pelajaran')

@section('konten')
<form class="card" method="post" action="{{ route('admin.mata-pelajaran.simpan') }}">
    @csrf

    <h3>Tambah Mata Pelajaran</h3>

    <div class="form-grid">
        <label>
            Mata Pelajaran
            <input name="nama_mata_pelajaran" placeholder="Contoh: Matematika" required>
        </label>

        <label>
            Jenis Pelajaran
            <select name="jenis_pelajaran" required>
                <option value="Formal">Formal</option>
                <option value="Non formal">Non formal</option>
            </select>
        </label>

        <label>
            Kelas
            <select name="kelas_id">
                <option value="">Pilih Kelas</option>
                @foreach($kelas as $item)
                    <option value="{{ $item->id }}">{{ $item->nama_kelas }}</option>
                @endforeach
            </select>
        </label>

        <label>
            Guru Pengampu
            <select name="guru_id">
                <option value="">Pilih Guru Pengampu</option>
                @foreach($guru as $g)
                    <option value="{{ $g->id }}">{{ $g->nama_guru }}</option>
                @endforeach
            </select>
        </label>
    </div>

    <button class="btn">Simpan Mata Pelajaran</button>
</form>

<div class="card">
    <h3>Daftar Mata Pelajaran</h3>
    <p class="muted">
        Daftar ini menampilkan mata pelajaran yang sudah diinput beserta kelas dan guru pengampunya.
    </p>

    <div style="overflow-x:auto">
        <table>
            <tr>
                <th>No</th>
                <th>Mata Pelajaran</th>
                <th>Jenis</th>
                <th>Kelas</th>
                <th>Guru Pengampu</th>
                <th>KKM</th>
                <th>Ubah</th>
                <th>Hapus</th>
            </tr>

            @forelse($mapel as $m)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>
                <form id="ubah-mapel-{{ $m->id }}" method="post" action="{{ route('admin.mata-pelajaran.ubah', $m->id) }}">
                    @csrf
                </form>
                <input
                    form="ubah-mapel-{{ $m->id }}"
                    name="nama_mata_pelajaran"
                    value="{{ $m->nama_mata_pelajaran }}"
                    required
                    style="min-width:180px"
                >
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
                        <option value="{{ $item->id }}" @selected((int) $m->kelas_id === (int) $item->id)>
                            {{ $item->nama_kelas }}
                        </option>
                    @endforeach
                </select>
            </td>
            <td>
                <select form="ubah-mapel-{{ $m->id }}" name="guru_id">
                    <option value="">Pilih Guru Pengampu</option>
                    @foreach($guru as $g)
                        <option value="{{ $g->id }}" @selected((int) $m->guru_id === (int) $g->id)>
                            {{ $g->nama_guru }}
                        </option>
                    @endforeach
                </select>
            </td>
            <td>{{ $m->kkm !== null ? rtrim(rtrim(number_format($m->kkm, 2, ',', '.'), '0'), ',') : '-' }}</td>
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
            @empty
                <tr>
                    <td colspan="8">Belum ada mata pelajaran yang diinput.</td>
                </tr>
            @endforelse
        </table>
    </div>
</div>
@endsection
