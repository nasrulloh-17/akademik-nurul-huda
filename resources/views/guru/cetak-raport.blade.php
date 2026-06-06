<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Cetak Raport {{ $siswa->nama_siswa }}</title>
    <style>
        body {
            color: #222;
            font-family: Arial, sans-serif;
            margin: 32px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
            vertical-align: top;
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
        <p>Tahun Ajaran: {{ $tahunAjaran->nama_tahun_ajaran }}</p>
        <p>Wali Kelas: {{ $guru->nama_guru }}</p>
    </div>

    <h3>Nilai Mata Pelajaran</h3>

    <table>
        <tr>
            <th>No</th>
            <th>Mata Pelajaran</th>
            <th>Guru</th>
            <th>Nilai Akhir</th>
            <th>Catatan Guru</th>
        </tr>

        @forelse($nilai as $n)
            @php($nilaiAkhir = isset($n->nilai_tugas, $n->nilai_uts, $n->nilai_uas) ? ($n->nilai_tugas + $n->nilai_uts + $n->nilai_uas) / 3 : null)

            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $n->nama_mata_pelajaran }}</td>
                <td>{{ $n->nama_guru ?? '-' }}</td>
                <td>{{ $nilaiAkhir === null ? '-' : number_format($nilaiAkhir, 2) }}</td>
                <td>{{ $n->catatan_guru ?? '-' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="5">Belum ada mata pelajaran pada kelas ini.</td>
            </tr>
        @endforelse
    </table>

    <h3>Nilai Kegiatan Tambahan</h3>

    @forelse($kegiatanTambahan as $kategori => $kegiatanList)
        <h4>{{ $kategori }}</h4>

        <table>
            <tr>
                <th>Kegiatan</th>
                <th>Nilai</th>
            </tr>

            @foreach($kegiatanList as $kegiatan)
                <tr>
                    <td>{{ $kegiatan->kegiatan }}</td>
                    <td>{{ $kegiatan->nilai ?? '-' }}</td>
                </tr>
            @endforeach
        </table>
    @empty
        <p>Belum ada nilai kegiatan tambahan.</p>
    @endforelse

    <script>
        window.onload = function () {
            window.print();
        };
    </script>
</body>
</html>
