@extends('layouts.dashboard')

@section('judul_halaman', 'Naik Kelas')

@section('konten')
<div class="card">
    <h3>Tahun Ajaran</h3>
    <p class="muted">Tahun ajaran aktif digunakan untuk memisahkan riwayat kelas dan nilai siswa.</p>

    <form method="post" action="{{ route('admin.tahun-ajaran.simpan') }}">
        @csrf
        <div class="form-grid">
            <input name="nama_tahun_ajaran" placeholder="Contoh: 2026/2027" required>
            <select name="semester" required>
                <option value="">Pilih Semester</option>
                <option value="ganjil">Ganjil</option>
                <option value="genap">Genap</option>
            </select>
            <button class="btn">Tambah Tahun Ajaran</button>
        </div>
    </form>

    <table>
        <tr>
            <th>Tahun Ajaran</th>
            <th>Semester</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>

        @foreach($tahunAjaran as $tahun)
            <tr>
                <td>{{ $tahun->nama_tahun_ajaran }}</td>
                <td>{{ ucfirst($tahun->semester ?? 'ganjil') }}</td>
                <td>{{ $tahun->aktif ? 'Aktif' : '-' }}</td>
                <td>
                    @unless($tahun->aktif)
                        <form method="post" action="{{ route('admin.tahun-ajaran.aktif', $tahun->id) }}">
                            @csrf
                            <button class="btn">Jadikan Aktif</button>
                        </form>
                    @endunless
                </td>
            </tr>
        @endforeach
    </table>
</div>

<div class="card">
    <h3>Naikkan Siswa ke Kelas Berikutnya</h3>
    <p class="muted">Semua siswa aktif dari kelas asal akan dipindahkan ke kelas tujuan dan riwayat kelas tahun ajaran tujuan akan disimpan.</p>

    <form method="post" action="{{ route('admin.naik-kelas.proses') }}">
        @csrf

        <div class="form-grid">
            <select name="kelas_asal_id" required>
                <option value="">Kelas Asal</option>
                @foreach($kelas as $item)
                    <option value="{{ $item->id }}">{{ $item->nama_kelas }}</option>
                @endforeach
            </select>

            <select name="kelas_tujuan_id" required>
                <option value="">Kelas Tujuan</option>
                @foreach($kelas as $item)
                    <option value="{{ $item->id }}">{{ $item->nama_kelas }}</option>
                @endforeach
            </select>

            <select name="tahun_ajaran_id" required>
                <option value="">Tahun Ajaran Tujuan</option>
                @foreach($tahunAjaran as $tahun)
                    <option value="{{ $tahun->id }}" @selected($tahunAktif && $tahunAktif->id === $tahun->id)>
                        {{ $tahun->nama_tahun_ajaran }} - {{ ucfirst($tahun->semester ?? 'ganjil') }}
                    </option>
                @endforeach
            </select>
        </div>

        <p>
            <button class="btn" onclick="return confirm('Naikkan semua siswa dari kelas asal ke kelas tujuan?')">
                Proses Naik Kelas
            </button>
        </p>
    </form>
</div>

<div class="card">
    <h3>Luluskan Kelas Akhir</h3>
    <p class="muted">Siswa kelas akhir akan diberi status lulus. Nilai dan riwayat kelas lama tetap tersimpan.</p>

    <form method="post" action="{{ route('admin.lulus-kelas.proses') }}">
        @csrf

        <div class="form-grid">
            <select name="kelas_id" required>
                <option value="">Pilih Kelas Akhir</option>
                @foreach($kelas as $item)
                    <option value="{{ $item->id }}">{{ $item->nama_kelas }}</option>
                @endforeach
            </select>

            <select name="tahun_ajaran_id" required>
                <option value="">Tahun Ajaran Lulus</option>
                @foreach($tahunAjaran as $tahun)
                    <option value="{{ $tahun->id }}" @selected($tahunAktif && $tahunAktif->id === $tahun->id)>
                        {{ $tahun->nama_tahun_ajaran }} - {{ ucfirst($tahun->semester ?? 'ganjil') }}
                    </option>
                @endforeach
            </select>
        </div>

        <p>
            <button class="btn danger" onclick="return confirm('Luluskan semua siswa aktif pada kelas ini?')">
                Luluskan Kelas
            </button>
        </p>
    </form>
</div>

@foreach($kelas as $item)
    <div class="card">
        <h3>{{ $item->nama_kelas }}</h3>

        <table>
            <tr>
                <th>NIS</th>
                <th>Nama Siswa</th>
                <th>Status</th>
            </tr>

            @forelse(($siswa[$item->id] ?? []) as $murid)
                <tr>
                    <td>{{ $murid->nis }}</td>
                    <td>{{ $murid->nama_siswa }}</td>
                    <td>{{ $murid->status }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">Belum ada siswa aktif.</td>
                </tr>
            @endforelse
        </table>
    </div>
@endforeach

<div class="card">
    <h3>Siswa Lulus</h3>

    <table>
        <tr>
            <th>NIS</th>
            <th>Nama Siswa</th>
            <th>Tanggal Lulus</th>
        </tr>

        @forelse($lulus as $murid)
            <tr>
                <td>{{ $murid->nis }}</td>
                <td>{{ $murid->nama_siswa }}</td>
                <td>{{ $murid->tanggal_lulus }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="3">Belum ada siswa lulus.</td>
            </tr>
        @endforelse
    </table>
</div>
@endsection
