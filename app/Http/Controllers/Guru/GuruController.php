<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GuruController extends Controller
{
    private array $kegiatanTambahan = [
        'Ekstrakurikuler' => ['Pramuka', 'Pidato', 'Khot', 'Robotika'],
        'Pengembangan Diri' => ['Sholat Dluha', 'Sholat Rawatib', 'Murajaah'],
        'Kepribadian' => ['Kedisiplinan', 'Kebersihan', 'Kerapian'],
        'Kehadiran' => ['Sakit', 'Izin', 'Tanpa Keterangan'],
    ];

    private array $nilaiKegiatanTambahan = [
        'Ekstrakurikuler' => ['Aktif', 'Mengikuti', 'Tidak Mengikuti'],
        'Pengembangan Diri' => ['Baik', 'Cukup', 'Kurang'],
        'Kepribadian' => ['Baik', 'Cukup', 'Kurang'],
    ];

    private function tahunAjaranAktif()
    {
        $tahunAjaran = DB::table('tahun_ajaran')->where('aktif', true)->first();

        if ($tahunAjaran) {
            return $tahunAjaran;
        }

        $id = DB::table('tahun_ajaran')->insertGetId([
            'nama_tahun_ajaran' => now()->month >= 7 ? now()->year.'/'.now()->addYear()->year : now()->subYear()->year.'/'.now()->year,
            'semester' => now()->month >= 7 ? 'ganjil' : 'genap',
            'aktif' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return DB::table('tahun_ajaran')->where('id', $id)->first();
    }

    private function guru()
    {
        abort_unless(session('jenis_pengguna') === 'guru', 403);
        return DB::table('guru')->where('pengguna_id', session('pengguna_id'))->first();
    }

    private function pesanTidakBerhak(): array
    {
        return ['akses' => 'Anda tidak memiliki hak akses menu ini.'];
    }

    private function kelasWali(int $guruId)
    {
        return DB::table('guru_role')
            ->join('kelas', 'kelas.id', '=', 'guru_role.kelas_id')
            ->where('guru_role.guru_id', $guruId)
            ->where('guru_role.role', 'wali kelas')
            ->select('kelas.*')
            ->orderBy('kelas.nama_kelas')
            ->get();
    }

    public function dashboard()
    {
        $guru = $this->guru();
        return view('guru.dashboard', [
            'guru' => $guru,
            'mapel' => DB::table('mata_pelajaran')->where('guru_id', $guru->id)->get(),
            'roles' => DB::table('guru_role')->where('guru_id', $guru->id)->pluck('role')->toArray(),
        ]);
    }

    public function biodata()
    {
        return view('guru.biodata', [
            'guru' => $this->guru(),
        ]);
    }

    public function simpanBiodata(Request $request)
    {
        $guru = $this->guru();
        $data = $request->validate([
            'nama_guru' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'telepon' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
        ]);

        DB::transaction(function () use ($guru, $data) {
            DB::table('guru')->where('id', $guru->id)->update([
                'nama_guru' => $data['nama_guru'],
                'jenis_kelamin' => $data['jenis_kelamin'],
                'telepon' => $data['telepon'] ?? null,
                'alamat' => $data['alamat'] ?? null,
                'updated_at' => now(),
            ]);

            DB::table('pengguna')->where('id', $guru->pengguna_id)->update([
                'nama' => $data['nama_guru'],
                'updated_at' => now(),
            ]);
        });

        session(['nama_pengguna' => $data['nama_guru']]);

        return back()->with('sukses', 'Biodata berhasil diperbarui.');
    }

    public function nilai(Request $request, ?int $mapel = null)
    {
        $guru = $this->guru();
        $tahunAjaran = $this->tahunAjaranAktif();
        $mapelGuru = DB::table('mata_pelajaran')->where('guru_id', $guru->id)->get();
        $aktif = $mapel ? $mapelGuru->firstWhere('id', $mapel) : $mapelGuru->first();
        $kelas = DB::table('kelas')->orderBy('nama_kelas')->get();
        $kelasAktif = $request->integer('kelas_id') ?: ($aktif->kelas_id ?? null);
        $siswa = DB::table('siswa')
            ->leftJoin('kelas', 'kelas.id', '=', 'siswa.kelas_id')
            ->select('siswa.*', 'kelas.nama_kelas')
            ->when($kelasAktif, fn ($query) => $query->where('siswa.kelas_id', $kelasAktif))
            ->where('siswa.status', 'aktif')
            ->orderBy('nama_siswa')
            ->get();
        $nilai = $aktif
            ? DB::table('nilai')
                ->where('mata_pelajaran_id', $aktif->id)
                ->where('tahun_ajaran_id', $tahunAjaran->id)
                ->get()
                ->keyBy('siswa_id')
            : collect();

        return view('guru.nilai', compact('mapelGuru', 'aktif', 'kelas', 'kelasAktif', 'siswa', 'nilai', 'tahunAjaran'));
    }

    public function simpanNilai(Request $request, int $mapel)
    {
        $guru = $this->guru();
        $tahunAjaran = $this->tahunAjaranAktif();
        $mataPelajaran = DB::table('mata_pelajaran')->where('id', $mapel)->where('guru_id', $guru->id)->first();

        if (! $mataPelajaran) {
            return back()->withErrors($this->pesanTidakBerhak());
        }

        if ($mataPelajaran->kkm === null) {
            return back()->withErrors(['kkm' => 'Isi nilai KKM terlebih dahulu sebelum menginput nilai siswa.']);
        }

        foreach ($request->input('nilai', []) as $siswaId => $isi) {
            DB::table('nilai')->updateOrInsert(
                ['siswa_id' => $siswaId, 'mata_pelajaran_id' => $mapel, 'tahun_ajaran_id' => $tahunAjaran->id],
                [
                    'nilai_tugas' => $isi['nilai_tugas'] ?? 0,
                    'nilai_uts' => $isi['nilai_uts'] ?? 0,
                    'nilai_uas' => $isi['nilai_uas'] ?? 0,
                    'catatan_guru' => $isi['catatan_guru'] ?? null,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        return back()->with('sukses', 'Nilai berhasil diperbarui.');
    }

    public function simpanKkm(Request $request, int $mapel)
    {
        $guru = $this->guru();

        if (! DB::table('mata_pelajaran')->where('id', $mapel)->where('guru_id', $guru->id)->exists()) {
            return back()->withErrors($this->pesanTidakBerhak());
        }

        $data = $request->validate([
            'kkm' => 'required|numeric|min:0|max:100',
        ]);

        DB::table('mata_pelajaran')->where('id', $mapel)->update([
            'kkm' => $data['kkm'],
            'updated_at' => now(),
        ]);

        return back()->with('sukses', 'Nilai KKM berhasil disimpan.');
    }

    public function cetakNilai(Request $request, int $mapel)
    {
        $guru = $this->guru();
        $tahunAjaran = $this->tahunAjaranAktif();
        $aktif = DB::table('mata_pelajaran')->where('id', $mapel)->where('guru_id', $guru->id)->first();

        if (! $aktif) {
            return redirect()->route('guru.dashboard')->withErrors($this->pesanTidakBerhak());
        }

        $kelasId = $request->integer('kelas_id');
        $kelasId = $kelasId ?: $aktif->kelas_id;
        $kelas = DB::table('kelas')->where('id', $kelasId)->first();

        abort_unless($kelas, 404);

        $siswa = DB::table('siswa')
            ->leftJoin('kelas', 'kelas.id', '=', 'siswa.kelas_id')
            ->select('siswa.*', 'kelas.nama_kelas')
            ->where('siswa.kelas_id', $kelasId)
            ->where('siswa.status', 'aktif')
            ->orderBy('nama_siswa')
            ->get();
        $nilai = DB::table('nilai')
            ->where('mata_pelajaran_id', $aktif->id)
            ->where('tahun_ajaran_id', $tahunAjaran->id)
            ->get()
            ->keyBy('siswa_id');

        return view('guru.cetak-nilai', compact('guru', 'aktif', 'kelas', 'siswa', 'nilai', 'tahunAjaran'));
    }

    public function catatan()
    {
        $guru = $this->guru();

        if (! DB::table('guru_role')->where('guru_id', $guru->id)->where('role', 'wali kelas')->exists()) {
            return redirect()->route('guru.dashboard')->withErrors($this->pesanTidakBerhak());
        }

        return view('guru.catatan', [
            'siswa' => DB::table('siswa')->leftJoin('kelas', 'kelas.id', '=', 'siswa.kelas_id')->select('siswa.*', 'kelas.nama_kelas')->orderBy('nama_siswa')->get(),
            'catatan' => DB::table('catatan_walikelas')->where('guru_id', $guru->id)->latest()->get()->groupBy('siswa_id'),
            'tagihan' => DB::table('tagihan')->latest()->get()->groupBy('siswa_id'),
        ]);
    }

    public function simpanCatatan(Request $request)
    {
        $guru = $this->guru();

        if (! DB::table('guru_role')->where('guru_id', $guru->id)->where('role', 'wali kelas')->exists()) {
            return back()->withErrors($this->pesanTidakBerhak());
        }

        $data = $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'catatan' => 'nullable',
            'nama_tagihan' => 'nullable',
            'jumlah' => 'nullable|numeric',
            'jatuh_tempo' => 'nullable|date',
        ]);

        if (! empty($data['catatan'])) {
            DB::table('catatan_walikelas')->insert([
                'siswa_id' => $data['siswa_id'],
                'guru_id' => $guru->id,
                'catatan' => $data['catatan'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if (! empty($data['nama_tagihan']) && isset($data['jumlah'])) {
            DB::table('tagihan')->insert([
                'siswa_id' => $data['siswa_id'],
                'nama_tagihan' => $data['nama_tagihan'],
                'jumlah' => $data['jumlah'],
                'jatuh_tempo' => $data['jatuh_tempo'] ?? null,
                'status' => 'belum lunas',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return back()->with('sukses', 'Catatan atau tagihan berhasil disimpan.');
    }

    public function kegiatanTambahan(Request $request)
    {
        $guru = $this->guru();
        $kelasWali = $this->kelasWali($guru->id);

        if ($kelasWali->isEmpty()) {
            return redirect()->route('guru.dashboard')->withErrors($this->pesanTidakBerhak());
        }

        $tahunAjaran = $this->tahunAjaranAktif();
        $kelasAktif = $request->integer('kelas_id') ?: $kelasWali->first()->id;

        if (! $kelasWali->contains('id', $kelasAktif)) {
            return redirect()->route('guru.dashboard')->withErrors($this->pesanTidakBerhak());
        }

        $siswa = DB::table('siswa')
            ->where('kelas_id', $kelasAktif)
            ->where('status', 'aktif')
            ->orderBy('nama_siswa')
            ->get();

        $nilai = DB::table('nilai_kegiatan_tambahan')
            ->where('tahun_ajaran_id', $tahunAjaran->id)
            ->where('kelas_id', $kelasAktif)
            ->get()
            ->keyBy(fn ($item) => $item->siswa_id.'|'.$item->kategori.'|'.$item->kegiatan);

        return view('guru.kegiatan-tambahan', [
            'kelasWali' => $kelasWali,
            'kelasAktif' => $kelasAktif,
            'tahunAjaran' => $tahunAjaran,
            'siswa' => $siswa,
            'nilai' => $nilai,
            'kegiatanTambahan' => $this->kegiatanTambahan,
            'nilaiKegiatanTambahan' => $this->nilaiKegiatanTambahan,
        ]);
    }

    public function cetakRaportSiswa(int $siswaId)
    {
        $guru = $this->guru();
        $kelasWali = $this->kelasWali($guru->id);
        $tahunAjaran = $this->tahunAjaranAktif();
        $siswa = DB::table('siswa')
            ->leftJoin('kelas', 'kelas.id', '=', 'siswa.kelas_id')
            ->select('siswa.*', 'kelas.nama_kelas', 'kelas.tingkat')
            ->where('siswa.id', $siswaId)
            ->first();

        abort_unless($siswa, 404);

        if (! $kelasWali->contains('id', $siswa->kelas_id)) {
            return redirect()->route('guru.dashboard')->withErrors($this->pesanTidakBerhak());
        }

        $dataSekolah = DB::table('data_sekolah')->first();
        $tingkat = (int) ($siswa->tingkat ?: preg_replace('/\D+/', '', (string) $siswa->nama_kelas));
        $pakaiMts = $tingkat >= 7 && $tingkat <= 9;

        $nilai = DB::table('mata_pelajaran')
            ->leftJoin('guru', 'guru.id', '=', 'mata_pelajaran.guru_id')
            ->leftJoin('nilai', function ($join) use ($siswa, $tahunAjaran) {
                $join->on('nilai.mata_pelajaran_id', '=', 'mata_pelajaran.id')
                    ->where('nilai.siswa_id', $siswa->id)
                    ->where('nilai.tahun_ajaran_id', $tahunAjaran->id);
            })
            ->where(function ($query) use ($siswa) {
                $query->where('mata_pelajaran.kelas_id', $siswa->kelas_id)
                    ->orWhereNull('mata_pelajaran.kelas_id');
            })
            ->select(
                'mata_pelajaran.id',
                'mata_pelajaran.nama_mata_pelajaran',
                'mata_pelajaran.kkm',
                'guru.nama_guru',
                'nilai.nilai_tugas',
                'nilai.nilai_uts',
                'nilai.nilai_uas',
                'nilai.catatan_guru'
            )
            ->orderBy('mata_pelajaran.nama_mata_pelajaran')
            ->get();
        $kegiatanTambahan = DB::table('nilai_kegiatan_tambahan')
            ->where('nilai_kegiatan_tambahan.siswa_id', $siswa->id)
            ->where('nilai_kegiatan_tambahan.tahun_ajaran_id', $tahunAjaran->id)
            ->select('nilai_kegiatan_tambahan.*')
            ->orderBy('kategori')
            ->orderBy('kegiatan')
            ->get()
            ->groupBy('kategori');
        $peringkatKelas = DB::table('siswa')
            ->leftJoin('nilai', function ($join) use ($tahunAjaran) {
                $join->on('nilai.siswa_id', '=', 'siswa.id')
                    ->where('nilai.tahun_ajaran_id', $tahunAjaran->id);
            })
            ->where('siswa.kelas_id', $siswa->kelas_id)
            ->where('siswa.status', 'aktif')
            ->select(
                'siswa.id',
                DB::raw('AVG((nilai.nilai_tugas * 0.3) + (nilai.nilai_uts * 0.3) + (nilai.nilai_uas * 0.4)) as rata_rata_raport')
            )
            ->groupBy('siswa.id')
            ->get()
            ->sortBy([
                ['rata_rata_raport', 'desc'],
                ['id', 'asc'],
            ])
            ->values();
        $peringkat = $peringkatKelas->search(fn ($item) => (int) $item->id === (int) $siswa->id);
        $dataPeringkat = $peringkat === false ? null : $peringkatKelas[$peringkat];

        return view('guru.cetak-raport', [
            'guru' => $guru,
            'siswa' => $siswa,
            'tahunAjaran' => $tahunAjaran,
            'nilai' => $nilai,
            'kegiatanTambahan' => $kegiatanTambahan,
            'peringkat' => $dataPeringkat && $dataPeringkat->rata_rata_raport !== null ? $peringkat + 1 : null,
            'jumlahSiswaKelas' => $peringkatKelas->count(),
            'namaSekolah' => $pakaiMts ? "MTs Ma'arif 20" : 'SMA Nurul Huda',
            'kepalaSekolah' => $pakaiMts ? ($dataSekolah->kepala_mts ?? null) : ($dataSekolah->kepala_sma ?? null),
            'alamatSekolah' => $dataSekolah->alamat ?? null,
        ]);
    }

    public function simpanKegiatanTambahan(Request $request)
    {
        $guru = $this->guru();
        $kelasWali = $this->kelasWali($guru->id);
        $tahunAjaran = $this->tahunAjaranAktif();
        $kelasId = $request->integer('kelas_id');

        if (! $kelasWali->contains('id', $kelasId)) {
            return back()->withErrors($this->pesanTidakBerhak());
        }

        $siswaKelas = DB::table('siswa')
            ->where('kelas_id', $kelasId)
            ->where('status', 'aktif')
            ->pluck('id')
            ->toArray();

        foreach ($request->input('nilai', []) as $siswaId => $kategoriList) {
            if (! in_array((int) $siswaId, $siswaKelas, true)) {
                continue;
            }

            foreach ($kategoriList as $kategori => $kegiatanList) {
                if (! isset($this->kegiatanTambahan[$kategori])) {
                    continue;
                }

                foreach ($kegiatanList as $kegiatan => $isi) {
                    if (! in_array($kegiatan, $this->kegiatanTambahan[$kategori], true)) {
                        continue;
                    }

                    $isi = trim((string) $isi);

                    if ($kategori === 'Kehadiran') {
                        $isi = $isi === '' ? '0' : (string) max(0, (int) $isi);
                    } elseif (! in_array($isi, $this->nilaiKegiatanTambahan[$kategori] ?? [], true)) {
                        $isi = null;
                    }

                    DB::table('nilai_kegiatan_tambahan')->updateOrInsert(
                        [
                            'siswa_id' => $siswaId,
                            'tahun_ajaran_id' => $tahunAjaran->id,
                            'kategori' => $kategori,
                            'kegiatan' => $kegiatan,
                        ],
                        [
                            'guru_id' => $guru->id,
                            'kelas_id' => $kelasId,
                            'nilai' => $isi,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                }
            }
        }

        return back()->with('sukses', 'Nilai kegiatan tambahan berhasil disimpan.');
    }

    public function dataSiswa()
    {
        $this->guru();
        return view('guru.data-siswa', [
            'kelas' => DB::table('kelas')->orderBy('nama_kelas')->get(),
            'siswa' => DB::table('siswa')->where('status', 'aktif')->orderBy('nama_siswa')->get()->groupBy('kelas_id'),
        ]);
    }
}
