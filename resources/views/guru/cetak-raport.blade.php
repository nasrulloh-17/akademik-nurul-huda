<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Cetak Raport {{ $siswa->nama_siswa }}</title>
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
            width: 215mm;
            min-height: 330mm;
            margin: 0 auto 24px;
            padding: 12mm 14mm;
            background: #fff;
            box-shadow: 0 16px 40px rgba(0, 0, 0, .12);
        }

        .title {
            margin-bottom: 10px;
            text-align: center;
        }

        .title h1 {
            margin: 0 0 3px;
            font-size: 13pt;
            text-transform: uppercase;
        }

        .title h2 {
            margin: 0;
            font-size: 12pt;
            text-transform: uppercase;
        }

        .identity {
            display: grid;
            grid-template-columns: 120px 10px 1fr 118px 10px 1fr;
            gap: 2px 7px;
            margin: 10px 0 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 0 10px;
        }

        th,
        td {
            border: 1px solid #111;
            padding: 4px 5px;
            vertical-align: middle;
        }

        th {
            font-weight: 700;
            text-align: center;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .section-title {
            font-weight: 700;
            margin: 8px 0 4px;
        }

        .below-kkm {
            color: #c1121f;
            font-weight: 700;
        }

        .signature {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 18px;
            margin-top: 14px;
        }

        .signature-box {
            text-align: center;
        }

        .signature-space {
            height: 54px;
        }

        .note {
            font-size: 10pt;
            margin-top: 4px;
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
                size: 215mm 330mm;
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

        $predikat = function ($nilai, $kkm) {
            if ($nilai === null || $kkm === null) {
                return '-';
            }

            if ($nilai >= 90) {
                return 'Cumlaude';
            }

            if ($nilai >= $kkm + 6) {
                return 'Baik';
            }

            if ($nilai >= $kkm) {
                return 'Cukup';
            }

            return 'Kurang';
        };

        $ekstrakurikuler = $kegiatanTambahan['Ekstrakurikuler'] ?? collect();
        $pengembanganDiri = $kegiatanTambahan['Pengembangan Diri'] ?? collect();
        $kepribadian = $kegiatanTambahan['Kepribadian'] ?? collect();
        $kehadiran = ($kegiatanTambahan['Kehadiran'] ?? collect())->keyBy('kegiatan');
    @endphp

    <div class="toolbar">
        <button onclick="window.print()">Cetak / Simpan PDF</button>
    </div>

    <main class="page">
        <header class="title">
            <h1>Laporan Hasil Belajar Peserta Didik</h1>
            <h2>{{ $namaSekolah }}</h2>
        </header>

        <section class="identity">
            <div>Nama Sekolah</div>
            <div>:</div>
            <div>{{ $namaSekolah }}</div>
            <div>Kelas</div>
            <div>:</div>
            <div>{{ $siswa->nama_kelas }}</div>

            <div>Alamat</div>
            <div>:</div>
            <div>{{ $alamatSekolah ?? '-' }}</div>
            <div>Semester</div>
            <div>:</div>
            <div>{{ ucfirst($tahunAjaran->semester ?? 'ganjil') }}</div>

            <div>Nama Peserta Didik</div>
            <div>:</div>
            <div><strong>{{ $siswa->nama_siswa }}</strong></div>
            <div>Tahun Pelajaran</div>
            <div>:</div>
            <div>{{ $tahunAjaran->nama_tahun_ajaran }}</div>

            <div>Nomor Induk/NIS</div>
            <div>:</div>
            <div>{{ $siswa->nis }}</div>
            <div>Peringkat</div>
            <div>:</div>
            <div>{{ $peringkat ? $peringkat.' dari '.$jumlahSiswaKelas.' siswa' : '-' }}</div>
        </section>

        <div class="section-title">A. Nilai Akademik</div>

        <table>
            <tr>
                <th rowspan="2" style="width:32px">No</th>
                <th rowspan="2">Mata Pelajaran</th>
                <th rowspan="2" style="width:48px">KKM</th>
                <th colspan="3">Pengetahuan</th>
            </tr>
            <tr>
                <th style="width:54px">Nilai</th>
                <th style="width:145px">Huruf</th>
                <th style="width:88px">Predikat</th>
            </tr>

            @forelse($nilai as $n)
                @php
                    $nilaiAkhir = isset($n->nilai_tugas, $n->nilai_uts, $n->nilai_uas)
                        ? round(($n->nilai_tugas * 0.3) + ($n->nilai_uts * 0.3) + ($n->nilai_uas * 0.4))
                        : null;
                    $belumTuntas = $nilaiAkhir !== null && $n->kkm !== null && $nilaiAkhir < $n->kkm;
                @endphp

                <tr>
                    <td class="center">{{ $loop->iteration }}</td>
                    <td>{{ $n->nama_mata_pelajaran }}</td>
                    <td class="center">{{ $n->kkm === null ? '-' : number_format($n->kkm, 0) }}</td>
                    <td class="center {{ $belumTuntas ? 'below-kkm' : '' }}">
                        {{ $nilaiAkhir === null ? '-' : $nilaiAkhir }}
                    </td>
                    <td>{{ $nilaiAkhir === null ? '-' : $terbilang($nilaiAkhir) }}</td>
                    <td class="center">{{ $predikat($nilaiAkhir, $n->kkm) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="center">Belum ada mata pelajaran pada kelas ini.</td>
                </tr>
            @endforelse
        </table>

        <div class="section-title">B. Pengembangan Diri</div>

        <table>
            <tr>
                <th style="width:32px">No</th>
                <th>Kegiatan</th>
                <th style="width:170px">Nilai</th>
            </tr>

            @forelse($pengembanganDiri as $kegiatan)
                <tr>
                    <td class="center">{{ $loop->iteration }}</td>
                    <td>{{ $kegiatan->kegiatan }}</td>
                    <td class="center">{{ $kegiatan->nilai ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="center">Belum ada nilai pengembangan diri.</td>
                </tr>
            @endforelse
        </table>

        <div class="section-title">C. Ekstrakurikuler</div>

        <table>
            <tr>
                <th style="width:32px">No</th>
                <th>Kegiatan</th>
                <th style="width:170px">Nilai</th>
            </tr>

            @forelse($ekstrakurikuler as $kegiatan)
                <tr>
                    <td class="center">{{ $loop->iteration }}</td>
                    <td>{{ $kegiatan->kegiatan }}</td>
                    <td class="center">{{ $kegiatan->nilai ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="center">Belum ada nilai ekstrakurikuler.</td>
                </tr>
            @endforelse
        </table>

        <div class="section-title">D. Kepribadian</div>

        <table>
            <tr>
                <th style="width:32px">No</th>
                <th>Aspek</th>
                <th style="width:170px">Nilai</th>
            </tr>

            @forelse($kepribadian as $kegiatan)
                <tr>
                    <td class="center">{{ $loop->iteration }}</td>
                    <td>{{ $kegiatan->kegiatan }}</td>
                    <td class="center">{{ $kegiatan->nilai ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="center">Belum ada nilai kepribadian.</td>
                </tr>
            @endforelse
        </table>

        <div class="section-title">E. Ketidakhadiran</div>

        <table style="width:48%">
            <tr>
                <td>Sakit</td>
                <td class="center" style="width:70px">{{ $kehadiran['Sakit']->nilai ?? 0 }}</td>
                <td class="center" style="width:55px">hari</td>
            </tr>
            <tr>
                <td>Izin</td>
                <td class="center">{{ $kehadiran['Izin']->nilai ?? 0 }}</td>
                <td class="center">hari</td>
            </tr>
            <tr>
                <td>Tanpa Keterangan</td>
                <td class="center">{{ $kehadiran['Tanpa Keterangan']->nilai ?? 0 }}</td>
                <td class="center">hari</td>
            </tr>
        </table>

        <div class="signature">
            <div class="signature-box">
                <div>Orang Tua/Wali,</div>
                <div class="signature-space"></div>
                <strong>............................</strong>
            </div>

            <div class="signature-box">
                <div>Wali Kelas,</div>
                <div class="signature-space"></div>
                <strong>{{ $guru->nama_guru }}</strong>
            </div>

            <div class="signature-box">
                <div>Mengetahui,</div>
                <div>Kepala Sekolah</div>
                <div class="signature-space"></div>
                <strong>{{ $kepalaSekolah ?? '-' }}</strong>
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
