@extends('layouts.dashboard')

@section('judul_halaman', 'Raport')

@section('konten')
<div class="card">
    <form method="get" action="{{ route('siswa.raport') }}">
        <div class="form-grid">
            <select name="tahun_ajaran_id">
                <option value="">Semua Tahun Ajaran/Semester</option>
                @foreach($daftarTahunAjaran as $periode)
                    <option value="{{ $periode->id }}" @selected(optional($tahunAjaranFilter)->id === $periode->id)>
                        {{ $periode->nama_tahun_ajaran }} - {{ ucfirst($periode->semester ?? 'ganjil') }}
                    </option>
                @endforeach
            </select>

            <button class="btn">Tampilkan</button>

            <a class="btn alt" href="{{ route('siswa.raport.cetak', request()->only('tahun_ajaran_id')) }}" target="_blank">
                Raport Formal
            </a>

            <a class="btn alt" href="{{ route('siswa.raport-diniyah.cetak', request()->only('tahun_ajaran_id')) }}" target="_blank">
                Raport Diniyah
            </a>
        </div>
    </form>
</div>

<div class="card">
    <h3>{{ $siswa->nama_siswa }} - {{ $siswa->nama_kelas }}</h3>
    <p class="muted">
        Peringkat kelas periode {{ ($tahunAjaranFilter ?: $tahunAjaranAktif)->nama_tahun_ajaran }} - {{ ucfirst(($tahunAjaranFilter ?: $tahunAjaranAktif)->semester ?? 'ganjil') }}:
        {{ $peringkat ? $peringkat.' dari '.$jumlahSiswaKelas.' siswa' : 'Belum tersedia' }}
    </p>

    @forelse($tahunRaport as $tahun)
        @php
            $nilaiTahun = $nilaiPerTahun[$tahun] ?? collect();
        @endphp

        <h4>Tahun Ajaran {{ $tahun }}</h4>

        @if($nilaiTahun->isNotEmpty())
            <table style="margin-bottom:18px">
                <tr>
                    <th>Mata Pelajaran</th>
                    <th>Guru</th>
                    <th>KKM</th>
                    <th>Tugas</th>
                    <th>UTS</th>
                    <th>UAS</th>
                    <th>Rata-rata</th>
                    <th>Catatan Guru</th>
                </tr>

                @foreach($nilaiTahun as $n)
                    @php
                        $nilaiAkhir = ($n->nilai_tugas * 0.3) + ($n->nilai_uts * 0.3) + ($n->nilai_uas * 0.4);
                        $belumTuntas = $n->kkm !== null && $nilaiAkhir < $n->kkm;
                    @endphp

                    <tr>
                        <td>{{ $n->nama_mata_pelajaran }}</td>
                        <td>{{ $n->nama_guru }}</td>
                        <td>{{ $n->kkm === null ? '-' : number_format($n->kkm, 0) }}</td>
                        <td>{{ number_format($n->nilai_tugas, 0) }}</td>
                        <td>{{ number_format($n->nilai_uts, 0) }}</td>
                        <td>{{ number_format($n->nilai_uas, 0) }}</td>
                        <td style="{{ $belumTuntas ? 'color:#dc3545;font-weight:700' : '' }}">
                            {{ number_format($nilaiAkhir, 0) }}
                        </td>
                        <td>{{ $n->catatan_guru }}</td>
                    </tr>
                @endforeach
            </table>
        @else
            <p class="muted">Belum ada nilai mata pelajaran pada tahun ajaran ini.</p>
        @endif

        @if(isset($kegiatanPerTahun[$tahun]))
            <h4>Kegiatan Tambahan Tahun Ajaran {{ $tahun }}</h4>

            @foreach($kegiatanPerTahun[$tahun] as $kategori => $kegiatanList)
                <h5>{{ $kategori }}</h5>

                <table style="margin-bottom:16px">
                    <tr>
                        <th>Kegiatan</th>
                        <th>Nilai</th>
                    </tr>

                    @foreach($kegiatanList as $kegiatan)
                        <tr>
                            <td>{{ $kegiatan->kegiatan }}</td>
                            <td>{{ $kegiatan->nilai }}</td>
                        </tr>
                    @endforeach
                </table>
            @endforeach
        @endif

        @if(isset($catatanPerTahun[$tahun]))
            <h4>Catatan Walikelas Tahun Ajaran {{ $tahun }}</h4>

            @foreach($catatanPerTahun[$tahun] as $c)
                <p><strong>Catatan walikelas:</strong> {{ $c->catatan }}</p>
            @endforeach
        @endif
    @empty
        <p>Belum ada nilai yang tersimpan.</p>
    @endforelse
</div>

<div class="card">
    <h3>Riwayat Catatan Walikelas</h3>

    @forelse($catatan as $c)
        <p>
            <strong>{{ $c->nama_tahun_ajaran ?? 'Tanpa Tahun Ajaran' }} - {{ ucfirst($c->semester ?? 'ganjil') }}:</strong>
            {{ $c->catatan }}
        </p>
    @empty
        <p>Belum ada catatan.</p>
    @endforelse
</div>
@endsection
