@extends('layouts.dashboard')

@section('judul_halaman', 'Siswa')

@section('konten')
<form class="card" method="post" action="{{ route('admin.siswa.simpan') }}">
    @csrf

    <div class="form-grid">
        <input name="nis" placeholder="NIS" required>
        <input name="nama_siswa" placeholder="Nama Siswa" required>
        <input type="password" name="kata_sandi" placeholder="Password" required>

        <select name="kelas_id">
            <option value="">Pilih Kelas</option>
            @foreach($kelas as $k)
                <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
            @endforeach
        </select>

        <select name="jenis_kelamin">
            <option value="">Jenis Kelamin</option>
            <option>Laki-laki</option>
            <option>Perempuan</option>
        </select>

        <input name="telepon" placeholder="Telepon">
    </div>

    <p>
        <textarea name="alamat" placeholder="Alamat"></textarea>
    </p>

    <button class="btn">Simpan Siswa</button>
</form>

<table>
    <tr>
        <th>NIS</th>
        <th>Nama Siswa</th>
        <th>Kelas</th>
        <th>Jenis Kelamin</th>
        <th>Telepon</th>
        <th>Status</th>
        <th>Ubah Password</th>
        <th>Aksi</th>
    </tr>

    @foreach($siswa as $item)
        <tr>
            <td>{{ $item->nis }}</td>
            <td>{{ $item->nama_siswa }}</td>
            <td>{{ $item->nama_kelas }}</td>
            <td>{{ $item->jenis_kelamin }}</td>
            <td>{{ $item->telepon }}</td>
            <td>{{ $item->status ?? 'aktif' }}</td>
            <td>
                <form method="post" action="{{ route('admin.siswa.password', $item->id) }}">
                    @csrf

                    <div style="display:flex;gap:8px">
                        <input type="password" name="kata_sandi" placeholder="Password baru" required>
                        <button class="btn">Ubah</button>
                    </div>
                </form>
            </td>
            <td>
                <form method="post" action="{{ route('admin.siswa.hapus', $item->id) }}">
                    @csrf
                    <button class="btn danger">Hapus</button>
                </form>
            </td>
        </tr>
    @endforeach
</table>
@endsection
