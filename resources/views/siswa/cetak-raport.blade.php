<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Cetak Raport</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 18px;
            color: #222;
            font-size: 11px;
            line-height: 1.35;
        }

        button {
            border: 0;
            border-radius: 6px;
            background: #047869;
            color: #fff;
            cursor: pointer;
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 10px;
            padding: 7px 11px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            page-break-inside: avoid;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 3px 4px;
            text-align: left;
            vertical-align: top;
        }

        h1,
        h2 {
            margin: 0;
            text-align: center;
        }

        h1 {
            font-size: 17px;
        }

        h2 {
            font-size: 14px;
            margin-top: 2px;
        }

        h3 {
            font-size: 12px;
            margin: 12px 0 5px;
        }

        h4 {
            font-size: 11px;
            margin: 8px 0 4px;
        }

        p {
            margin: 3px 0;
        }

        .meta {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2px 18px;
            margin: 12px 0;
        }

        .meta p:last-child {
            grid-column: 1 / -1;
        }

        .score-table th,
        .score-table td {
            font-size: 10.5px;
        }

        .center {
            text-align: center;
        }

        .compact-section {
            page-break-inside: avoid;
        }

        .paired-sections {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            align-items: start;
        }

        .paired-sections table {
            margin-bottom: 6px;
        }

        @media print {
            body {
                margin: 0;
                font-size: 10.5px;
            }

            button {
                display: none;
            }

            @page {
                size: A4;
                margin: 10mm;
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
            Peringkat Kelas Periode Aktif {{ $tahunAjaranAktif->nama_tahun_ajaran }} - {{ ucfirst($tahunAjaranAktif->semester ?? 'ganjil') }}:
            {{ $peringkat ? $peringkat.' dari '.$jumlahSiswaKelas.' siswa' : 'Belum tersedia' }}
        </p>
    </div>

    @if($tahunRaport->isEmpty())
        <p>Belum ada nilai yang tersimpan.</p>
    @else
        @foreach($tahunRaport as $tahun)
            @php
                $nilaiTahun = $nilaiPerTahun[$tahun] ?? collect();
            @endphp

            <h3>Tahun Ajaran {{ $tahun }}</h3>

            @if($nilaiTahun->isNotEmpty())
                <table class="score-table">
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
                            <td class="center">{{ $n->kkm === null ? '-' : number_format($n->kkm, 0) }}</td>
                            <td class="center">{{ number_format($n->nilai_tugas, 0) }}</td>
                            <td class="center">{{ number_format($n->nilai_uts, 0) }}</td>
                            <td class="center">{{ number_format($n->nilai_uas, 0) }}</td>
                            <td class="center" style="{{ $belumTuntas ? 'color:#dc3545;font-weight:700' : '' }}">
                                {{ number_format($nilaiAkhir, 0) }}
                            </td>
                            <td>{{ $n->catatan_guru }}</td>
                        </tr>
                    @endforeach
                </table>
            @else
                <p>Belum ada nilai mata pelajaran pada tahun ajaran ini.</p>
            @endif

            @if(isset($kegiatanPerTahun[$tahun]))
                @php
                    $ekstrakurikuler = $kegiatanPerTahun[$tahun]['Ekstrakurikuler'] ?? collect();
                    $pengembanganDiri = $kegiatanPerTahun[$tahun]['Pengembangan Diri'] ?? collect();
                    $kepribadian = $kegiatanPerTahun[$tahun]['Kepribadian'] ?? collect();
                    $kehadiran = ($kegiatanPerTahun[$tahun]['Kehadiran'] ?? collect())->keyBy('kegiatan');
                @endphp

                <div class="compact-section">
                    <h3>Kegiatan Tambahan Tahun Ajaran {{ $tahun }}</h3>

                    <div class="paired-sections">
                        <section>
                            <h4>Ekstrakurikuler</h4>

                            <table>
                                <tr>
                                    <th>Kegiatan</th>
                                    <th style="width:85px">Nilai</th>
                                </tr>

                                @forelse($ekstrakurikuler as $kegiatan)
                                    <tr>
                                        <td>{{ $kegiatan->kegiatan }}</td>
                                        <td class="center">{{ $kegiatan->nilai }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="center">Belum ada nilai.</td>
                                    </tr>
                                @endforelse
                            </table>
                        </section>

                        <section>
                            <h4>Pengembangan Diri</h4>

                            <table>
                                <tr>
                                    <th>Kegiatan</th>
                                    <th style="width:85px">Nilai</th>
                                </tr>

                                @forelse($pengembanganDiri as $kegiatan)
                                    <tr>
                                        <td>{{ $kegiatan->kegiatan }}</td>
                                        <td class="center">{{ $kegiatan->nilai }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="center">Belum ada nilai.</td>
                                    </tr>
                                @endforelse
                            </table>
                        </section>
                    </div>

                    <div class="paired-sections">
                        <section>
                            <h4>Kepribadian</h4>

                            <table>
                                <tr>
                                    <th>Aspek</th>
                                    <th style="width:85px">Nilai</th>
                                </tr>

                                @forelse($kepribadian as $kegiatan)
                                    <tr>
                                        <td>{{ $kegiatan->kegiatan }}</td>
                                        <td class="center">{{ $kegiatan->nilai }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="center">Belum ada nilai.</td>
                                    </tr>
                                @endforelse
                            </table>
                        </section>

                        <section>
                            <h4>Ketidakhadiran</h4>

                            <table>
                                <tr>
                                    <th>Keterangan</th>
                                    <th style="width:55px">Jumlah</th>
                                    <th style="width:42px">Satuan</th>
                                </tr>
                                <tr>
                                    <td>Sakit</td>
                                    <td class="center">{{ $kehadiran['Sakit']->nilai ?? 0 }}</td>
                                    <td class="center">hari</td>
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
                        </section>
                    </div>
                </div>
            @endif

            @if(isset($catatanPerTahun[$tahun]))
                <h3>Catatan Walikelas Tahun Ajaran {{ $tahun }}</h3>

                @foreach($catatanPerTahun[$tahun] as $c)
                    <p><strong>Catatan walikelas:</strong> {{ $c->catatan }}</p>
                @endforeach
            @endif
        @endforeach
    @endif

    <h3>Riwayat Catatan Walikelas</h3>

    @foreach($catatan as $c)
        <p>
            <strong>{{ $c->nama_tahun_ajaran ?? 'Tanpa Tahun Ajaran' }} - {{ ucfirst($c->semester ?? 'ganjil') }}:</strong>
            {{ $c->catatan }}
        </p>
    @endforeach

    <script>
        window.onload = function () {
            window.print();
        };
    </script>
</body>
</html>
