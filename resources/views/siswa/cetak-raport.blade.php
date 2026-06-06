<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Cetak Raport</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 32px;
            color: #222;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
        }

        h1,
        h2 {
            text-align: center;
        }

        .meta {
            margin: 24px 0;
        }

        @media print {
            button {
                display: none;
            }
        }
    </style>
</head>
<body>
    <button onclick="window.print()">Cetak / Simpan PDF</button>

    <h1>Yayasan Nurul Huda Munjuk</h1>
    <h2>Raport Siswa</h2>

    <div class="meta">
        <p>Nama Siswa: <strong>{{ $siswa->nama_siswa }}</strong></p>
        <p>NIS: {{ $siswa->nis }}</p>
        <p>Kelas: {{ $siswa->nama_kelas }}</p>
        <p>
            Peringkat Kelas Tahun Ajaran Aktif {{ $tahunAjaranAktif->nama_tahun_ajaran }}:
            {{ $peringkat ? $peringkat.' dari '.$jumlahSiswaKelas.' siswa' : 'Belum tersedia' }}
        </p>
    </div>

    @forelse($tahunRaport as $tahun)
        @php($nilaiTahun = $nilaiPerTahun[$tahun] ?? collect())

        <h3>Tahun Ajaran {{ $tahun }}</h3>

        @if($nilaiTahun->isNotEmpty())
            <table>
                <tr>
                    <th>Mata Pelajaran</th>
                    <th>Guru</th>
                    <th>Tugas</th>
                    <th>UTS</th>
                    <th>UAS</th>
                    <th>Rata-rata</th>
                    <th>Catatan Guru</th>
                </tr>

                @foreach($nilaiTahun as $n)
                    <tr>
                        <td>{{ $n->nama_mata_pelajaran }}</td>
                        <td>{{ $n->nama_guru }}</td>
                        <td>{{ $n->nilai_tugas }}</td>
                        <td>{{ $n->nilai_uts }}</td>
                        <td>{{ $n->nilai_uas }}</td>
                        <td>{{ number_format(($n->nilai_tugas + $n->nilai_uts + $n->nilai_uas) / 3, 2) }}</td>
                        <td>{{ $n->catatan_guru }}</td>
                    </tr>
                @endforeach
            </table>
        @else
            <p>Belum ada nilai mata pelajaran pada tahun ajaran ini.</p>
        @endif

        @if(isset($kegiatanPerTahun[$tahun]))
            <h3>Kegiatan Tambahan Tahun Ajaran {{ $tahun }}</h3>

            @foreach($kegiatanPerTahun[$tahun] as $kategori => $kegiatanList)
                <h4>{{ $kategori }}</h4>

                <table>
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

    <h3>Catatan Walikelas</h3>

    @foreach($catatan as $c)
        <p>{{ $c->catatan }}</p>
    @endforeach

    <script>
        window.onload = function () {
            window.print();
        };
    </script>
</body>
</html>
