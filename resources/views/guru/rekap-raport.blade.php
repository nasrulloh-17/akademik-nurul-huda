@extends('layouts.dashboard')

@section('judul_halaman', 'Rekap Raport')

@section('konten')
<div class="card">
    <form method="get" action="{{ route('guru.rekap-raport') }}">
        <div class="form-grid">
            <select name="kelas_id" required>
                @foreach($kelasWali as $kelas)
                    <option value="{{ $kelas->id }}" @selected((int) $kelasAktif === (int) $kelas->id)>
                        {{ $kelas->nama_kelas }}
                    </option>
                @endforeach
            </select>

            <select name="tahun_ajaran_id" required>
                @foreach($daftarTahunAjaran as $periode)
                    <option value="{{ $periode->id }}" @selected((int) $tahunAjaran->id === (int) $periode->id)>
                        {{ $periode->nama_tahun_ajaran }} - {{ ucfirst($periode->semester ?? 'ganjil') }}
                    </option>
                @endforeach
            </select>

            <button class="btn">Tampilkan</button>
        </div>
    </form>
</div>

<div class="card">
    <h3>Periode: {{ $tahunAjaran->nama_tahun_ajaran }} - {{ ucfirst($tahunAjaran->semester ?? 'ganjil') }}</h3>
    <p class="muted">
        Status lengkap jika nilai semua mata pelajaran sudah ada, kegiatan tambahan sudah ada, dan catatan wali kelas sudah diisi.
    </p>

    <table>
        <tr>
            <th>No</th>
            <th>Nama Siswa</th>
            <th>Nilai Mapel</th>
            <th>Kegiatan Tambahan</th>
            <th>Catatan</th>
            <th>Cetak</th>
        </tr>

        @foreach($siswa as $murid)
            @php
                $jumlahNilai = ($nilai[$murid->id] ?? collect())
                    ->filter(fn ($item) => $item->nilai_tugas !== null && $item->nilai_uts !== null && $item->nilai_uas !== null)
                    ->count();
                $jumlahKegiatan = ($kegiatanTambahan[$murid->id] ?? collect())->filter(fn ($item) => $item->nilai !== null && $item->nilai !== '')->count();
                $jumlahMapel = $mapel->count();
                $jumlahKegiatanMinimal = 13;
            @endphp

            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $murid->nama_siswa }}</td>
                <td>{{ $jumlahNilai }}/{{ $jumlahMapel }}</td>
                <td>{{ $jumlahKegiatan }}/{{ $jumlahKegiatanMinimal }}</td>
                <td>{{ isset($catatan[$murid->id]) ? 'Ada' : 'Belum ada' }}</td>
                <td>
                    <div style="display:flex;gap:6px;flex-wrap:wrap">
                        <a
                            class="btn alt"
                            href="{{ route('guru.raport.cetak', ['siswa' => $murid->id, 'tahun_ajaran_id' => $tahunAjaran->id]) }}"
                            target="_blank"
                        >
                            Raport Formal
                        </a>
                        <a
                            class="btn alt"
                            href="{{ route('guru.raport-diniyah.cetak', ['siswa' => $murid->id, 'tahun_ajaran_id' => $tahunAjaran->id]) }}"
                            target="_blank"
                        >
                            Raport Diniyah
                        </a>
                    </div>
                </td>
            </tr>
        @endforeach
    </table>
</div>
@endsection
