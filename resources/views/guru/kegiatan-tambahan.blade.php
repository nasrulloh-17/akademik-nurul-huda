@extends('layouts.dashboard')

@section('judul_halaman', 'Input Nilai Kegiatan Tambahan')

@section('konten')
@php
    $periodeAktif = (bool) ($tahunAjaran->aktif ?? false);
@endphp

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
                        <option value="{{ $kelas->id }}" @selected((int) $kelasAktif === (int) $kelas->id)>
                            {{ $kelas->nama_kelas }}
                        </option>
                    @endforeach
                </select>
            </label>

            <label>
                Tahun Ajaran/Semester
                <select name="tahun_ajaran_id" required>
                    @foreach($daftarTahunAjaran as $periode)
                        <option value="{{ $periode->id }}" @selected((int) $tahunAjaran->id === (int) $periode->id)>
                            {{ $periode->nama_tahun_ajaran }} - {{ ucfirst($periode->semester ?? 'ganjil') }}
                        </option>
                    @endforeach
                </select>
            </label>

            <button class="btn" type="submit">Tampilkan Kelas</button>
        </div>
    </form>
</div>

<div class="card">
    <h3>Nilai Kegiatan Tambahan</h3>
    <p class="muted">
        Periode:
        {{ $tahunAjaran->nama_tahun_ajaran }} - {{ ucfirst($tahunAjaran->semester ?? 'ganjil') }}
    </p>
    @unless($periodeAktif)
        <p class="muted" style="color:#dc3545">
            Nilai kegiatan tambahan periode lama hanya bisa dilihat, tidak bisa diubah.
        </p>
    @endunless

    @if($siswa->isNotEmpty())
        @foreach($kegiatanTambahan as $kategori => $kegiatanList)
            <form method="post" action="{{ route('guru.kegiatan-tambahan.simpan') }}" style="margin-bottom:24px">
                @csrf

                <input type="hidden" name="kelas_id" value="{{ $kelasAktif }}">
                <input type="hidden" name="tahun_ajaran_id" value="{{ $tahunAjaran->id }}">

                <section>
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
                                        @php
                                            $nilaiKey = $murid->id.'|'.$kategori.'|'.$kegiatan;
                                            $nilaiAktif = $nilai[$nilaiKey]->nilai ?? '';
                                            $kategoriInputKey = $kegiatanTambahanKeys[$kategori]['key'];
                                            $kegiatanInputKey = $kegiatanTambahanKeys[$kategori]['kegiatan'][$kegiatan];
                                        @endphp

                                        <td>
                                            @if($kategori === 'Kehadiran')
                                                <input
                                                    type="number"
                                                    min="0"
                                                    name="nilai[{{ $murid->id }}][{{ $kategoriInputKey }}][{{ $kegiatanInputKey }}]"
                                                    value="{{ $nilaiAktif }}"
                                                    placeholder="0"
                                                    style="min-width:86px"
                                                    @disabled(! $periodeAktif)
                                                >
                                            @else
                                                <select
                                                    name="nilai[{{ $murid->id }}][{{ $kategoriInputKey }}][{{ $kegiatanInputKey }}]"
                                                    style="min-width:150px"
                                                    @disabled(! $periodeAktif)
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

                    @if($periodeAktif)
                        <button class="btn" type="submit" style="margin-top:14px">
                            Simpan {{ $kategori }}
                        </button>
                    @else
                        <button class="btn" type="button" disabled style="margin-top:14px">
                            Periode Lama Tidak Bisa Diubah
                        </button>
                    @endif
                </section>
            </form>
        @endforeach
    @else
        <p>Belum ada siswa aktif pada kelas ini.</p>
    @endif
</div>
@endsection
