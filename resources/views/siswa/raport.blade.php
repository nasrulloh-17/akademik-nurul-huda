@extends('layouts.dashboard')

@section('judul_halaman', 'Raport')

@section('konten')
<div class="card">
    <a class="btn" href="{{ route('siswa.raport.cetak') }}" target="_blank">Cetak Nilai ke PDF</a>
</div>

<div class="card">
    <h3>{{ $siswa->nama_siswa }} - {{ $siswa->nama_kelas }}</h3>
    <p class="muted">
        Peringkat kelas periode aktif {{ $tahunAjaranAktif->nama_tahun_ajaran }} - {{ ucfirst($tahunAjaranAktif->semester ?? 'ganjil') }}:
        {{ $peringkat ? $peringkat.' dari '.$jumlahSiswaKelas.' siswa' : 'Belum tersedia' }}
    </p>

    @forelse($tahunRaport as $tahun)
        @php($nilaiTahun = $nilaiPerTahun[$tahun] ?? collect())

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
                    @php($nilaiAkhir = ($n->nilai_tugas * 0.3) + ($n->nilai_uts * 0.3) + ($n->nilai_uas * 0.4))
                    @php($belumTuntas = $n->kkm !== null && $nilaiAkhir < $n->kkm)

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
    @empty
        <p>Belum ada nilai yang tersimpan.</p>
    @endforelse
</div>

<div class="card">
    <h3>Catatan Walikelas</h3>

    @forelse($catatan as $c)
        <p>{{ $c->catatan }}</p>
    @empty
        <p>Belum ada catatan.</p>
    @endforelse
</div>
@endsection
