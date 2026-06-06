<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cetak Nilai {{ $aktif->nama_mata_pelajaran }} - {{ $kelas->nama_kelas }}</title>
    <style>
        body {
            margin: 0;
            background: #eef4f3;
            color: #111827;
            font-family: Arial, sans-serif;
        }

        .toolbar {
            display: flex;
            justify-content: center;
            gap: 10px;
            padding: 18px;
        }

        .btn {
            border: 0;
            border-radius: 8px;
            background: #047869;
            color: white;
            padding: 10px 14px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto 24px;
            padding: 18mm;
            background: white;
            box-shadow: 0 16px 40px rgba(0, 0, 0, .12);
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #111827;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }

        .header h1 {
            margin: 0 0 6px;
            font-size: 20px;
            text-transform: uppercase;
        }

        .meta {
            display: grid;
            grid-template-columns: 130px 1fr;
            gap: 7px 12px;
            margin-bottom: 18px;
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        th,
        td {
            border: 1px solid #111827;
            padding: 7px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background: #d9fff6;
            text-align: center;
        }

        .number,
        .score {
            text-align: center;
        }

        .signature {
            display: grid;
            grid-template-columns: 1fr 220px;
            margin-top: 34px;
            font-size: 14px;
        }

        .signature-space {
            height: 72px;
        }

        @media print {
            body {
                background: white;
            }

            .toolbar {
                display: none;
            }

            .page {
                width: auto;
                min-height: auto;
                margin: 0;
                padding: 0;
                box-shadow: none;
            }

            @page {
                size: A4 portrait;
                margin: 14mm;
            }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <button class="btn" type="button" onclick="window.print()">Cetak / Simpan PDF</button>
        <a class="btn" href="{{ route('guru.nilai', ['mapel' => $aktif->id, 'kelas_id' => $kelas->id]) }}">Kembali</a>
    </div>

    <main class="page">
        <div class="header">
            <h1>Daftar Nilai Siswa</h1>
            <div>Yayasan Nurul Huda Munjuk</div>
        </div>

        <div class="meta">
            <div>Mata Pelajaran</div>
            <div>: {{ $aktif->nama_mata_pelajaran }}</div>
            <div>Kelas</div>
            <div>: {{ $kelas->nama_kelas }}</div>
            <div>Guru</div>
            <div>: {{ $guru->nama_guru }}</div>
            <div>Tanggal Cetak</div>
            <div>: {{ now()->format('d/m/Y') }}</div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width:38px">No</th>
                    <th>Nama Siswa</th>
                    <th style="width:70px">Tugas</th>
                    <th style="width:70px">UTS</th>
                    <th style="width:70px">UAS</th>
                    <th style="width:80px">Nilai Akhir</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($siswa as $murid)
                    @php
                        $n = $nilai[$murid->id] ?? null;
                        $tugas = (float) ($n->nilai_tugas ?? 0);
                        $uts = (float) ($n->nilai_uts ?? 0);
                        $uas = (float) ($n->nilai_uas ?? 0);
                        $akhir = ($tugas + $uts + $uas) / 3;
                    @endphp

                    <tr>
                        <td class="number">{{ $loop->iteration }}</td>
                        <td>{{ $murid->nama_siswa }}</td>
                        <td class="score">{{ number_format($tugas, 2) }}</td>
                        <td class="score">{{ number_format($uts, 2) }}</td>
                        <td class="score">{{ number_format($uas, 2) }}</td>
                        <td class="score">{{ number_format($akhir, 2) }}</td>
                        <td>{{ $n->catatan_guru ?? '' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="number">Belum ada siswa pada kelas ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="signature">
            <div></div>
            <div>
                <div>Guru Mata Pelajaran,</div>
                <div class="signature-space"></div>
                <strong>{{ $guru->nama_guru }}</strong>
            </div>
        </div>
    </main>
</body>
</html>
