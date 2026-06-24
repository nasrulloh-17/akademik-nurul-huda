@extends('layouts.dashboard')

@section('judul_halaman', 'Siswa')

@section('konten')
<style>
    .siswa-filter-row {
        display: grid;
        grid-template-columns: minmax(260px, 1fr) minmax(180px, 240px) auto auto;
        gap: 10px;
        align-items: center;
    }

    .siswa-filter-row .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 42px;
        white-space: nowrap;
    }

    @media (max-width: 800px) {
        .siswa-filter-row {
            grid-template-columns: 1fr;
        }
    }
</style>

<form class="card" method="post" action="{{ route('admin.siswa.simpan') }}">
    @csrf

    <div class="form-grid">
        <input name="nis" placeholder="NIS" required>
        <input name="nisn" placeholder="NISN untuk login" required>
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

<form class="card" method="get" action="{{ route('admin.siswa') }}">
    <div class="siswa-filter-row">
        <input name="cari" value="{{ $filterCari }}" placeholder="Cari nama, NIS, NISN, atau telepon">

        <select name="kelas_id">
            <option value="">Semua Kelas</option>
            @foreach($kelas as $k)
                <option value="{{ $k->id }}" @selected((string) $filterKelasId === (string) $k->id)>
                    {{ $k->nama_kelas }}
                </option>
            @endforeach
        </select>

        <button class="btn" type="submit">Terapkan Filter</button>
        <a class="btn alt" href="{{ route('admin.siswa') }}">Reset</a>
    </div>
</form>

<table>
    <tr>
        <th>NIS</th>
        <th>NISN</th>
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
            <td>{{ $item->nisn }}</td>
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
                <details style="margin-bottom:5px">
                    <summary class="btn alt" style="display:inline-block">Ubah</summary>

                    <form method="post" action="{{ route('admin.siswa.ubah', $item->id) }}" style="margin-top:12px;min-width:320px">
                        @csrf

                        <div class="form-grid">
                            <input name="nis" value="{{ $item->nis }}" placeholder="NIS" required>
                            <input name="nisn" value="{{ $item->nisn }}" placeholder="NISN untuk login" required>
                            <input name="nama_siswa" value="{{ $item->nama_siswa }}" placeholder="Nama Siswa" required>

                            <select name="kelas_id">
                                <option value="">Pilih Kelas</option>
                                @foreach($kelas as $k)
                                    <option value="{{ $k->id }}" @selected($item->kelas_id === $k->id)>
                                        {{ $k->nama_kelas }}
                                    </option>
                                @endforeach
                            </select>

                            <select name="jenis_kelamin">
                                <option value="">Jenis Kelamin</option>
                                <option value="Laki-laki" @selected($item->jenis_kelamin === 'Laki-laki')>
                                    Laki-laki
                                </option>
                                <option value="Perempuan" @selected($item->jenis_kelamin === 'Perempuan')>
                                    Perempuan
                                </option>
                            </select>

                            <input name="telepon" value="{{ $item->telepon }}" placeholder="Telepon">

                            <select name="status" required>
                                <option value="aktif" @selected(($item->status ?? 'aktif') === 'aktif')>Aktif</option>
                                <option value="lulus" @selected(($item->status ?? 'aktif') === 'lulus')>Lulus</option>
                            </select>
                        </div>

                        <p>
                            <textarea name="alamat" placeholder="Alamat">{{ $item->alamat }}</textarea>
                        </p>

                        <button class="btn">Simpan Perubahan</button>
                    </form>
                </details>

                <form method="post" action="{{ route('admin.siswa.hapus', $item->id) }}">
                    @csrf
                    <button class="btn danger">Hapus</button>
                </form>
            </td>
        </tr>
    @endforeach
</table>
@endsection
