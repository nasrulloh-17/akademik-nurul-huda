@extends('layouts.dashboard')

@section('judul_halaman', 'Input Nilai')

@section('konten')
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
            <h3>{{ $aktif->nama_mata_pelajaran }}</h3>
            <p class="muted">Tahun ajaran aktif: {{ $tahunAjaran->nama_tahun_ajaran }}</p>

            @unless($kelasAktif)
                <p class="muted">Pilih kelas terlebih dahulu agar nilai yang dicetak sesuai kelas.</p>
            @endunless

            <table>
                <tr>
                    <th>NIS</th>
                    <th>Nama Siswa</th>
                    <th>Kelas</th>
                    <th>Nilai Tugas</th>
                    <th>Nilai UTS</th>
                    <th>Nilai UAS</th>
                    <th>Nilai Akhir</th>
                    <th>Catatan Guru</th>
                </tr>

                @foreach($siswa as $murid)
                    @php($n = $nilai[$murid->id] ?? null)

                    <tr>
                        <td>{{ $murid->nis }}</td>
                        <td>{{ $murid->nama_siswa }}</td>
                        <td>{{ $murid->nama_kelas }}</td>
                        <td>
                            <input
                                class="nilai-input"
                                name="nilai[{{ $murid->id }}][nilai_tugas]"
                                value="{{ $n->nilai_tugas ?? 0 }}"
                                data-nilai-tugas
                            >
                        </td>
                        <td>
                            <input
                                class="nilai-input"
                                name="nilai[{{ $murid->id }}][nilai_uts]"
                                value="{{ $n->nilai_uts ?? 0 }}"
                                data-nilai-uts
                            >
                        </td>
                        <td>
                            <input
                                class="nilai-input"
                                name="nilai[{{ $murid->id }}][nilai_uas]"
                                value="{{ $n->nilai_uas ?? 0 }}"
                                data-nilai-uas
                            >
                        </td>
                        <td>
                            <input data-nilai-akhir readonly>
                        </td>
                        <td>
                            <input name="nilai[{{ $murid->id }}][catatan_guru]" value="{{ $n->catatan_guru ?? '' }}">
                        </td>
                    </tr>
                @endforeach
            </table>

            <p>
                <button class="btn">Simpan Nilai</button>
            </p>
        </div>
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

            nilaiAkhir.value = ((tugas + uts + uas) / 3).toFixed(2);
        };

        nilaiTugas.addEventListener('input', hitungNilaiAkhir);
        nilaiUts.addEventListener('input', hitungNilaiAkhir);
        nilaiUas.addEventListener('input', hitungNilaiAkhir);

        hitungNilaiAkhir();
    });
</script>
@endsection
