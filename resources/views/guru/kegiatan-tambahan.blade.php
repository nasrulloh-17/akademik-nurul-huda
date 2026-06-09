@extends('layouts.dashboard')

@section('judul_halaman', 'Input Nilai Kegiatan Tambahan')

@section('konten')
<style>
    .kegiatan-table {
        min-width: 760px;
    }

    .kegiatan-nis {
        width: 140px;
    }

    .kegiatan-nama {
        width: 220px;
    }

    @media (max-width: 640px) {
        .kegiatan-hide-mobile {
            display: none;
        }

        .kegiatan-table {
            min-width: 560px;
        }

        .kegiatan-nama {
            width: 150px;
        }

        .kegiatan-table select {
            min-width: 118px !important;
        }

        .kegiatan-table input[type="number"] {
            min-width: 64px !important;
        }

        .raport-list-table .btn {
            padding: 7px 9px;
            font-size: 12px;
        }
    }
</style>

<div class="card">
    <form method="get" action="{{ route('guru.kegiatan-tambahan') }}">
        <div class="form-grid">
            <label>
                Kelas Wali
                <select name="kelas_id" required>
                    @foreach($kelasWali as $kelas)
                        <option value="{{ $kelas->id }}" @selected((int) $kelasAktif === $kelas->id)>
                            {{ $kelas->nama_kelas }}
                        </option>
                    @endforeach
                </select>
            </label>

            <button class="btn" type="submit">Tampilkan Kelas</button>
        </div>
    </form>
</div>

<form method="post" action="{{ route('guru.kegiatan-tambahan.simpan') }}">
    @csrf

    <input type="hidden" name="kelas_id" value="{{ $kelasAktif }}">

    <div class="card">
        <h3>Nilai Kegiatan Tambahan</h3>
        <p class="muted">
            Tahun ajaran aktif:
            {{ $tahunAjaran->nama_tahun_ajaran }} - {{ ucfirst($tahunAjaran->semester ?? 'ganjil') }}
        </p>

        @if($siswa->isNotEmpty())
            <div style="overflow-x:auto;margin-bottom:24px">

            </div>

            @foreach($kegiatanTambahan as $kategori => $kegiatanList)
                <section style="margin-bottom:24px">
                    <h4>{{ $kategori }}</h4>

                    <div style="overflow-x:auto">
                        <table class="kegiatan-table">
                            <tr>
                                <th class="kegiatan-nis kegiatan-hide-mobile">NIS</th>
                                <th class="kegiatan-nama">Nama Siswa</th>
                                @foreach($kegiatanList as $kegiatan)
                                    <th>{{ $kegiatan }}</th>
                                @endforeach
                            </tr>

                            @foreach($siswa as $murid)
                                <tr>
                                    <td class="kegiatan-hide-mobile">{{ $murid->nis }}</td>
                                    <td>
                                        <strong>{{ $murid->nama_siswa }}</strong>
                                    </td>

                                    @foreach($kegiatanList as $kegiatan)
                                        @php($nilaiKey = $murid->id.'|'.$kategori.'|'.$kegiatan)
                                        @php($nilaiAktif = $nilai[$nilaiKey]->nilai ?? '')

                                        <td>
                                            @if($kategori === 'Kehadiran')
                                                <input
                                                    type="number"
                                                    min="0"
                                                    name="nilai[{{ $murid->id }}][{{ $kategori }}][{{ $kegiatan }}]"
                                                    value="{{ $nilaiAktif }}"
                                                    placeholder="0"
                                                    style="min-width:86px"
                                                >
                                            @else
                                                <select
                                                    name="nilai[{{ $murid->id }}][{{ $kategori }}][{{ $kegiatan }}]"
                                                    style="min-width:150px"
                                                >
                                                    <option value="">Pilih</option>
                                                    @foreach($nilaiKegiatanTambahan[$kategori] as $opsi)
                                                        <option value="{{ $opsi }}" @selected($nilaiAktif === $opsi)>
                                                            {{ $opsi }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </section>
            @endforeach

            <button class="btn">Simpan Nilai Kegiatan Tambahan</button>
        @else
            <p>Belum ada siswa aktif pada kelas ini.</p>
        @endif
    </div>
</form>
@endsection
