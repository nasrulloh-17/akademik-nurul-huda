@extends('layouts.dashboard')

@section('judul_halaman', 'Input Nilai Kegiatan Tambahan')

@section('konten')
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
                <table>
                    <tr>
                        <th>NIS</th>
                        <th>Nama Siswa</th>
                        <th>Cetak Raport</th>
                    </tr>

                    @foreach($siswa as $murid)
                        <tr>
                            <td>{{ $murid->nis }}</td>
                            <td>{{ $murid->nama_siswa }}</td>
                            <td>
                                <a class="btn alt" href="{{ route('guru.raport.cetak', $murid->id) }}" target="_blank">
                                    Cetak Raport
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>

            @foreach($kegiatanTambahan as $kategori => $kegiatanList)
                <section style="margin-bottom:24px">
                    <h4>{{ $kategori }}</h4>

                    <div style="overflow-x:auto">
                        <table style="min-width:760px">
                            <tr>
                                <th style="width:140px">NIS</th>
                                <th style="width:220px">Nama Siswa</th>
                                @foreach($kegiatanList as $kegiatan)
                                    <th>{{ $kegiatan }}</th>
                                @endforeach
                            </tr>

                            @foreach($siswa as $murid)
                                <tr>
                                    <td>{{ $murid->nis }}</td>
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
