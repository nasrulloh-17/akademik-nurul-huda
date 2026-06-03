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
    </div>

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

        @foreach($nilai as $n)
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
