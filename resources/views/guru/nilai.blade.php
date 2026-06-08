@extends('layouts.dashboard')

@section('judul_halaman', 'Input Nilai')

@section('konten')
<style>
    .nilai-kkm-bar {
        display: grid;
        grid-template-columns: minmax(220px, 1fr) minmax(180px, 260px) auto;
        gap: 12px;
        align-items: end;
        margin-bottom: 12px;
    }

    @media (max-width: 640px) {
        .nilai-hide-mobile {
            display: none;
        }

        .nilai-kkm-bar {
            grid-template-columns: 1fr;
            align-items: stretch;
        }

        .nilai-kkm-bar .btn {
            width: 100%;
        }

        .nilai-input {
            min-width: 64px;
        }
    }
</style>

<div class="card">
    @forelse($mapelGuru as $m)
        <a class="btn {{ $aktif && $aktif->id === $m->id ? '' : 'alt' }}" href="{{ route('guru.nilai', $m->id) }}">
            {{ $m->nama_mata_pelajaran }}
        </a>
    @empty
        Belum ada mata pelajaran yang diampu.
    @endforelse
</div>

@if($aktif)
    <div class="card">
        <form method="get" action="{{ route('guru.nilai', $aktif->id) }}">
            <div class="form-grid">
                <select name="kelas_id" required>
                    <option value="">Pilih kelas</option>
                    @foreach($kelas as $item)
                        <option value="{{ $item->id }}" @selected((int) $kelasAktif === $item->id)>
                            {{ $item->nama_kelas }}
                        </option>
                    @endforeach
                </select>

                <button class="btn" type="submit">Tampilkan Kelas</button>

                @if($kelasAktif)
                    <a class="btn alt" href="{{ route('guru.nilai.cetak', ['mapel' => $aktif->id, 'kelas_id' => $kelasAktif]) }}" target="_blank">
                        Cetak Nilai PDF
                    </a>
                @endif
            </div>
        </form>
    </div>

    <form method="post" action="{{ route('guru.nilai.simpan', $aktif->id) }}">
        @csrf

        <div class="card">
            <div class="nilai-kkm-bar">
                <div>
                    <h3 style="margin:0">{{ $aktif->nama_mata_pelajaran }}</h3>
                    <p class="muted" style="margin-bottom:0">
                        Tahun ajaran aktif:
                        {{ $tahunAjaran->nama_tahun_ajaran }} - {{ ucfirst($tahunAjaran->semester ?? 'ganjil') }}
                    </p>
                </div>

                <label>
                    Nilai KKM
                    <input
                        form="form-kkm"
                        type="number"
                        min="0"
                        max="100"
                        step="0.01"
                        name="kkm"
                        value="{{ $aktif->kkm }}"
                        placeholder="Contoh: 75"
                        required
                    >
                </label>

                <button class="btn" form="form-kkm">Simpan KKM</button>
            </div>

            @if($aktif->kkm === null)
                <div class="alert">Isi nilai KKM terlebih dahulu sebelum menginput nilai siswa.</div>
            @endif

            <p class="muted">
                Nilai akhir dihitung otomatis: 30% Tugas + 30% UTS + 40% UAS.
            </p>

            @unless($kelasAktif)
                <p class="muted">Pilih kelas terlebih dahulu agar nilai yang dicetak sesuai kelas.</p>
            @endunless

            <table>
                <tr>
                    <th class="nilai-hide-mobile">NIS</th>
                    <th>Nama Siswa</th>
                    <th class="nilai-hide-mobile">Kelas</th>
                    <th>Nilai Tugas</th>
                    <th>Nilai UTS</th>
                    <th>Nilai UAS</th>
                    <th>Nilai Akhir</th>
                    <th>Catatan Guru</th>
                </tr>

                @foreach($siswa as $murid)
                    @php($n = $nilai[$murid->id] ?? null)

                    <tr>
                        <td class="nilai-hide-mobile">{{ $murid->nis }}</td>
                        <td>{{ $murid->nama_siswa }}</td>
                        <td class="nilai-hide-mobile">{{ $murid->nama_kelas }}</td>
                        <td>
                            <input
                                class="nilai-input"
                                name="nilai[{{ $murid->id }}][nilai_tugas]"
                                value="{{ $n->nilai_tugas ?? 0 }}"
                                data-nilai-tugas
                                @disabled($aktif->kkm === null)
                            >
                        </td>
                        <td>
                            <input
                                class="nilai-input"
                                name="nilai[{{ $murid->id }}][nilai_uts]"
                                value="{{ $n->nilai_uts ?? 0 }}"
                                data-nilai-uts
                                @disabled($aktif->kkm === null)
                            >
                        </td>
                        <td>
                            <input
                                class="nilai-input"
                                name="nilai[{{ $murid->id }}][nilai_uas]"
                                value="{{ $n->nilai_uas ?? 0 }}"
                                data-nilai-uas
                                @disabled($aktif->kkm === null)
                            >
                        </td>
                        <td>
                            <input data-nilai-akhir data-kkm="{{ $aktif->kkm }}" readonly>
                        </td>
                        <td>
                            <input
                                name="nilai[{{ $murid->id }}][catatan_guru]"
                                value="{{ $n->catatan_guru ?? '' }}"
                                @disabled($aktif->kkm === null)
                            >
                        </td>
                    </tr>
                @endforeach
            </table>

            <p>
                <button class="btn" @disabled($aktif->kkm === null)>Simpan Nilai</button>
            </p>
        </div>
    </form>

    <form id="form-kkm" method="post" action="{{ route('guru.nilai.kkm', $aktif->id) }}">
        @csrf
    </form>
@endif

<script>
    document.querySelectorAll('tr').forEach((row) => {
        const nilaiTugas = row.querySelector('[data-nilai-tugas]');
        const nilaiUts = row.querySelector('[data-nilai-uts]');
        const nilaiUas = row.querySelector('[data-nilai-uas]');
        const nilaiAkhir = row.querySelector('[data-nilai-akhir]');

        if (! nilaiTugas || ! nilaiUts || ! nilaiUas || ! nilaiAkhir) {
            return;
        }

        const hitungNilaiAkhir = () => {
            const tugas = parseFloat(nilaiTugas.value) || 0;
            const uts = parseFloat(nilaiUts.value) || 0;
            const uas = parseFloat(nilaiUas.value) || 0;

            const akhir = (tugas * 0.3) + (uts * 0.3) + (uas * 0.4);
            const kkm = parseFloat(nilaiAkhir.dataset.kkm);

            nilaiAkhir.value = akhir.toFixed(0);
            nilaiAkhir.style.color = ! Number.isNaN(kkm) && akhir < kkm ? '#dc3545' : '';
            nilaiAkhir.style.fontWeight = ! Number.isNaN(kkm) && akhir < kkm ? '700' : '';
        };

        nilaiTugas.addEventListener('input', hitungNilaiAkhir);
        nilaiUts.addEventListener('input', hitungNilaiAkhir);
        nilaiUas.addEventListener('input', hitungNilaiAkhir);

        hitungNilaiAkhir();
    });
</script>
@endsection
