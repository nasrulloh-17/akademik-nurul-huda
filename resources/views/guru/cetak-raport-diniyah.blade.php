<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Raport Diniyah {{ $siswa->nama_siswa }}</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #f1f5f4;
            color: #111;
            font-family: Calibri, Arial, sans-serif;
            font-size: 11pt;
        }

        .toolbar {
            display: flex;
            justify-content: center;
            padding: 14px;
        }

        button {
            border: 0;
            border-radius: 6px;
            background: #047869;
            color: #fff;
            cursor: pointer;
            font-weight: 700;
            padding: 9px 13px;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto 24px;
            padding: 12mm 14mm;
            background: #fff;
            box-shadow: 0 16px 40px rgba(0, 0, 0, .12);
        }

        .title {
            margin-bottom: 14px;
            padding-bottom: 10px;
            border-bottom: 3px double #111;
            font-family: "Times New Roman", Times, serif;
            text-align: center;
            text-transform: uppercase;
        }

        .title h1 {
            margin: 0 0 6px;
            font-size: 18pt;
            font-weight: 700;
        }

        .title h2,
        .title h3 {
            margin: 0;
            font-size: 15pt;
            font-weight: 700;
        }

        .identity {
            display: grid;
            grid-template-columns: 128px 10px 1fr 120px 10px 1fr;
            gap: 3px 7px;
            margin: 10px 0 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 0 12px;
        }

        th,
        td {
            border: 1px solid #111;
            padding: 5px 6px;
            vertical-align: middle;
        }

        th {
            text-align: center;
        }

        .center {
            text-align: center;
        }

        .print-date {
            margin-top: 18px;
            text-align: right;
        }

        .signature {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 18px;
            margin-top: 8px;
        }

        .signature-box {
            text-align: center;
        }

        .signature-space {
            height: 56px;
        }

        @media print {
            body {
                background: #fff;
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
                size: A4;
                margin: 10mm 12mm;
            }
        }
    </style>
</head>
<body>
    @php
        $terbilang = function ($angka) use (&$terbilang) {
            $angka = (int) $angka;
            $huruf = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];

            if ($angka < 12) {
                return $huruf[$angka] ?: 'Nol';
            }

            if ($angka < 20) {
                return $terbilang($angka - 10).' Belas';
            }

            if ($angka < 100) {
                $sisa = $angka % 10;

                return trim($terbilang(intdiv($angka, 10)).' Puluh'.($sisa ? ' '.$terbilang($sisa) : ''));
            }

            if ($angka === 100) {
                return 'Seratus';
            }

            return (string) $angka;
        };

        $tanggalCetak = now()->locale('id')->translatedFormat('d F Y');
    @endphp

    <div class="toolbar">
        <button onclick="window.print()">Cetak / Simpan PDF</button>
    </div>

    <main class="page">
        <header class="title">
            <h1>Laporan Hasil Belajar Santri</h1>
            <h2>Madrasah Diniyah {{ $jenjang }}</h2>
            <h3>Madin Nurul Huda</h3>
        </header>

        <section class="identity">
            <div>Nama Santri</div>
            <div>:</div>
            <div><strong>{{ $siswa->nama_siswa }}</strong></div>
            <div>Kelas</div>
            <div>:</div>
            <div>{{ $kelasDiniyah }}</div>

            <div>Nama Sekolah</div>
            <div>:</div>
            <div>Madin Nurul Huda</div>
            <div>Semester</div>
            <div>:</div>
            <div>{{ ucfirst($tahunAjaran->semester ?? 'ganjil') }}</div>

            <div>Tahun Pelajaran</div>
            <div>:</div>
            <div>{{ $tahunAjaran->nama_tahun_ajaran }}</div>
            <div>Jenjang</div>
            <div>:</div>
            <div>{{ $jenjang }}</div>
        </section>

        <table>
            <tr>
                <th style="width:34px">No</th>
                <th>Mata Pelajaran</th>
                <th style="width:60px">Nilai</th>
                <th style="width:150px">Huruf</th>
                <th>Catatan</th>
            </tr>

            @forelse($nilai as $n)
                @php
                    $nilaiAkhir = isset($n->nilai_tugas, $n->nilai_uts, $n->nilai_uas)
                        ? round(($n->nilai_tugas * 0.3) + ($n->nilai_uts * 0.3) + ($n->nilai_uas * 0.4))
                        : null;
                @endphp

                <tr>
                    <td class="center">{{ $loop->iteration }}</td>
                    <td>{{ $n->nama_mata_pelajaran }}</td>
                    <td class="center">{{ $nilaiAkhir === null ? '-' : $nilaiAkhir }}</td>
                    <td>{{ $nilaiAkhir === null ? '-' : $terbilang($nilaiAkhir) }}</td>
                    <td>{{ $n->catatan_guru ?? '' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="center">Belum ada mata pelajaran non formal pada kelas ini.</td>
                </tr>
            @endforelse
        </table>

        <div class="print-date">Labuhan Maringgai, {{ $tanggalCetak }}</div>

        <div class="signature">
            <div class="signature-box">
                <div>Orang Tua/Wali,</div>
                <div class="signature-space"></div>
                <strong>............................</strong>
            </div>

            <div class="signature-box">
                <div>Wali Kelas,</div>
                <div class="signature-space"></div>
                <strong>{{ $waliKelas->nama_guru ?? '-' }}</strong>
            </div>

            <div class="signature-box">
                <div>Kepala Madin,</div>
                <div class="signature-space"></div>
                <strong>............................</strong>
            </div>
        </div>
    </main>

    <script>
        window.onload = function () {
            window.print();
        };
    </script>
</body>
</html>
