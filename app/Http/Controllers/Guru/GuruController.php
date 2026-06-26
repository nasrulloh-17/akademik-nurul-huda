<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

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

    private function keyInputKegiatanTambahan(string $teks): string
    {
        return trim(strtolower(preg_replace('/[^A-Za-z0-9]+/', '_', $teks)), '_');
    }

    private function keyKegiatanTambahanUntukView(): array
    {
        $keys = [];

        foreach ($this->kegiatanTambahan as $kategori => $kegiatanList) {
            $keys[$kategori] = [
                'key' => $this->keyInputKegiatanTambahan($kategori),
                'kegiatan' => [],
            ];

            foreach ($kegiatanList as $kegiatan) {
                $keys[$kategori]['kegiatan'][$kegiatan] = $this->keyInputKegiatanTambahan($kegiatan);
            }
        }

        return $keys;
    }

    private function keyKegiatanTambahanUntukSimpan(): array
    {
        $keys = [];

        foreach ($this->kegiatanTambahan as $kategori => $kegiatanList) {
            $kategoriKey = $this->keyInputKegiatanTambahan($kategori);

            $keys[$kategoriKey] = [
                'kategori' => $kategori,
                'kegiatan' => [],
            ];

            foreach ($kegiatanList as $kegiatan) {
                $keys[$kategoriKey]['kegiatan'][$this->keyInputKegiatanTambahan($kegiatan)] = $kegiatan;
            }
        }

        return $keys;
    }

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

    private function daftarTahunAjaran()
    {
        return DB::table('tahun_ajaran')->orderByDesc('id')->get();
    }

    private function tahunAjaranTerpilih(Request $request)
    {
        $tahunAjaranAktif = $this->tahunAjaranAktif();
        $tahunAjaranId = $request->integer('tahun_ajaran_id');

        if (! $tahunAjaranId) {
            return $tahunAjaranAktif;
        }

        return DB::table('tahun_ajaran')->where('id', $tahunAjaranId)->first() ?: $tahunAjaranAktif;
    }

    private function tahunAjaranInputAktif(Request $request)
    {
        $tahunAjaran = $this->tahunAjaranAktif();
        $tahunAjaranId = $request->integer('tahun_ajaran_id');

        if ($tahunAjaranId && $tahunAjaranId !== (int) $tahunAjaran->id) {
            throw ValidationException::withMessages([
                'tahun_ajaran_id' => 'Nilai tahun ajaran lama hanya bisa dilihat, tidak bisa diubah.',
            ]);
        }

        return $tahunAjaran;
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

    private function stafKeuangan(int $guruId): bool
    {
        return DB::table('guru_role')
            ->where('guru_id', $guruId)
            ->where('role', 'staff')
            ->where('staff_jenis', 'staff keuangan')
            ->exists();
    }

    private function jagaStafKeuangan()
    {
        $guru = $this->guru();

        if (! $this->stafKeuangan($guru->id)) {
            throw ValidationException::withMessages($this->pesanTidakBerhak());
        }

        return $guru;
    }

    private function unggahDokumenKeuangan(Request $request, string $nama, string $folder): ?string
    {
        $file = $request->file($nama);

        if (! $file) {
            return null;
        }

        if (! $file->isValid()) {
            throw ValidationException::withMessages([
                $nama => 'Upload file gagal. Pastikan ukuran file tidak melebihi batas hosting.',
            ]);
        }

        $documentRoot = $_SERVER['DOCUMENT_ROOT'] ?? null;
        $publicRoot = $documentRoot && is_dir($documentRoot)
            ? rtrim($documentRoot, DIRECTORY_SEPARATOR)
            : public_path();
        $tujuan = $publicRoot.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.$folder;

        if (! is_dir($tujuan)) {
            mkdir($tujuan, 0755, true);
        }

        if (! is_writable($tujuan)) {
            throw ValidationException::withMessages([
                $nama => 'Folder upload tidak bisa ditulis. Pastikan folder public_html/uploads memiliki permission yang benar.',
            ]);
        }

        $namaFile = Str::uuid().'.'.$file->getClientOriginalExtension();
        $file->move($tujuan, $namaFile);

        return "uploads/$folder/$namaFile";
    }

    private function perbaruiStatusTagihan(int $tagihanId): void
    {
        $tagihan = DB::table('tagihan')->where('id', $tagihanId)->first();

        if (! $tagihan) {
            return;
        }

        $terbayar = (float) DB::table('pembayaran_tagihan')
            ->where('tagihan_id', $tagihanId)
            ->where('status', 'valid')
            ->sum('jumlah_bayar');
        $jumlah = (float) $tagihan->jumlah;
        $status = $terbayar <= 0 ? 'belum lunas' : ($terbayar >= $jumlah ? 'lunas' : 'sebagian');

        DB::table('tagihan')->where('id', $tagihanId)->update([
            'status' => $status,
            'updated_at' => now(),
        ]);
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

    private function aksesNilaiWaliKelasAktif(): bool
    {
        return file_exists(storage_path('app/akses-nilai-wali-kelas.flag'));
    }

    private function bolehInputMapel($guru, $mataPelajaran): bool
    {
        if (! $mataPelajaran) {
            return false;
        }

        if ((int) $mataPelajaran->guru_id === (int) $guru->id) {
            return true;
        }

        if (! $this->aksesNilaiWaliKelasAktif()) {
            return false;
        }

        return DB::table('guru_role')
            ->where('guru_id', $guru->id)
            ->where('role', 'wali kelas')
            ->where('kelas_id', $mataPelajaran->kelas_id)
            ->exists();
    }

    private function streamCsv(string $namaFile, array $header, iterable $baris)
    {
        return response()->streamDownload(function () use ($header, $baris) {
            $output = fopen('php://output', 'w');

            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, $header, ';');

            foreach ($baris as $row) {
                fputcsv($output, $row, ';');
            }

            fclose($output);
        }, $namaFile, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function namaFileCsv(string $nama): string
    {
        return trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $nama), '-');
    }

    private function angkaNilaiAkhir($nilai): ?int
    {
        if ($nilai === null || $nilai->nilai_tugas === null || $nilai->nilai_uts === null || $nilai->nilai_uas === null) {
            return null;
        }

        return (int) round(($nilai->nilai_tugas * 0.3) + ($nilai->nilai_uts * 0.3) + ($nilai->nilai_uas * 0.4));
    }

    private function infoDiniyah($siswa): array
    {
        $tingkat = (int) ($siswa->tingkat ?: preg_replace('/\D+/', '', (string) $siswa->nama_kelas));
        $jenjang = $tingkat >= 10 ? 'WUSTHO' : 'ULA';
        $kelasDiniyah = match ($tingkat) {
            7 => '1 ULA',
            8 => '2 ULA',
            9 => '3 ULA',
            10 => '1 WUSTHO',
            11 => '2 WUSTHO',
            12 => '3 WUSTHO',
            default => $siswa->nama_kelas,
        };

        return compact('jenjang', 'kelasDiniyah');
    }

    private function nilaiKosongAtauValid($nilai): bool
    {
        return $nilai === null || $nilai === '' || (is_numeric($nilai) && $nilai >= 0 && $nilai <= 100);
    }

    private function mapelSetaraIds($mataPelajaran)
    {
        return DB::table('mata_pelajaran')
            ->whereRaw('LOWER(TRIM(nama_mata_pelajaran)) = ?', [strtolower(trim($mataPelajaran->nama_mata_pelajaran))])
            ->where('kelas_id', $mataPelajaran->kelas_id)
            ->where('jenis_pelajaran', $mataPelajaran->jenis_pelajaran ?? 'Formal')
            ->pluck('id')
            ->map(fn ($id) => (int) $id);
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

    public function downloadCsv()
    {
        $this->guru();

        return view('guru.download-csv', [
            'kelas' => DB::table('kelas')->orderBy('nama_kelas')->get(),
            'tahunAjaran' => $this->tahunAjaranAktif(),
            'daftarTahunAjaran' => $this->daftarTahunAjaran(),
        ]);
    }

    public function unduhCsv(Request $request, string $jenis)
    {
        $this->guru();

        return match ($jenis) {
            'guru' => $this->unduhCsvGuru(),
            'siswa' => $this->unduhCsvSiswa(),
            'nilai-akhir' => $this->unduhCsvNilaiAkhir($request),
            'ketidakhadiran' => $this->unduhCsvKetidakhadiran($request),
            default => abort(404),
        };
    }

    private function unduhCsvGuru()
    {
        $guru = DB::table('guru')
            ->leftJoin('guru_role', 'guru_role.guru_id', '=', 'guru.id')
            ->leftJoin('kelas', 'kelas.id', '=', 'guru_role.kelas_id')
            ->select('guru.*', 'guru_role.role', 'guru_role.staff_jenis', 'kelas.nama_kelas')
            ->orderBy('guru.nama_guru')
            ->get()
            ->groupBy('id');
        $baris = [];
        $nomor = 1;

        foreach ($guru as $roleItems) {
            $item = $roleItems->first();
            $roles = $roleItems
                ->filter(fn ($role) => $role->role)
                ->map(function ($role) {
                    if ($role->role === 'wali kelas' && $role->nama_kelas) {
                        return 'Wali Kelas '.$role->nama_kelas;
                    }

                    if ($role->role === 'staff' && $role->staff_jenis) {
                        return ucwords($role->staff_jenis);
                    }

                    return ucwords($role->role);
                })
                ->implode(', ');

            $baris[] = [
                $nomor++,
                $item->id_guru,
                $item->nama_guru,
                $item->tanggal_lahir,
                $item->jenis_kelamin,
                $roles ?: '-',
                $item->telepon,
                $item->alamat,
            ];
        }

        return $this->streamCsv('data-guru.csv', ['No', 'ID Guru', 'Nama Guru', 'Tanggal Lahir', 'Jenis Kelamin', 'Role', 'Telepon', 'Alamat'], $baris);
    }

    private function unduhCsvSiswa()
    {
        $siswa = DB::table('siswa')
            ->leftJoin('kelas', 'kelas.id', '=', 'siswa.kelas_id')
            ->select('siswa.*', 'kelas.nama_kelas')
            ->orderBy('siswa.nama_siswa')
            ->get();
        $baris = [];

        foreach ($siswa as $index => $item) {
            $baris[] = [
                $index + 1,
                $item->nis,
                $item->nisn,
                $item->nama_siswa,
                $item->nama_kelas,
                $item->jenis_kelamin,
                $item->tempat_lahir,
                $item->tanggal_lahir,
                $item->telepon,
                $item->alamat,
                $item->status,
            ];
        }

        return $this->streamCsv('data-siswa.csv', ['No', 'NIS', 'NISN', 'Nama Siswa', 'Kelas', 'Jenis Kelamin', 'Tempat Lahir', 'Tanggal Lahir', 'Telepon', 'Alamat', 'Status'], $baris);
    }

    private function unduhCsvNilaiAkhir(Request $request)
    {
        $tahunAjaran = $this->tahunAjaranTerpilih($request);
        $kelasId = $request->integer('kelas_id');
        $kelas = DB::table('kelas')->where('id', $kelasId)->first();

        abort_unless($kelas, 404);

        $siswa = DB::table('siswa')
            ->where('kelas_id', $kelasId)
            ->where('status', 'aktif')
            ->orderBy('nama_siswa')
            ->get();
        $nilai = DB::table('nilai')
            ->where('tahun_ajaran_id', $tahunAjaran->id)
            ->whereIn('siswa_id', $siswa->pluck('id'))
            ->get()
            ->groupBy('siswa_id');
        $mapelIdsBernilai = $nilai
            ->flatten(1)
            ->pluck('mata_pelajaran_id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();
        $mapel = DB::table('mata_pelajaran')
            ->where(function ($query) use ($kelasId, $mapelIdsBernilai) {
                $query->where('kelas_id', $kelasId)
                    ->orWhereNull('kelas_id');

                if ($mapelIdsBernilai->isNotEmpty()) {
                    $query->orWhereIn('id', $mapelIdsBernilai);
                }
            })
            ->orderBy('nama_mata_pelajaran')
            ->get();
        $kelompokMapel = $mapel
            ->groupBy(fn ($item) => strtolower(trim($item->nama_mata_pelajaran)).'|'.strtolower(trim($item->jenis_pelajaran ?? 'Formal')))
            ->map(function ($items) {
                $utama = $items->sortBy(fn ($item) => $item->kelas_id === null ? 1 : 0)->first();

                return [
                    'id' => $utama->id,
                    'nama' => $utama->nama_mata_pelajaran,
                    'jenis' => $utama->jenis_pelajaran ?? 'Formal',
                    'ids' => $items->pluck('id')->map(fn ($id) => (int) $id)->values(),
                ];
            })
            ->values();
        $rekap = [];

        foreach ($siswa as $murid) {
            $total = 0;
            $jumlahMapelDinilai = 0;
            $nilaiMapel = [];
            $nilaiSiswa = $nilai[$murid->id] ?? collect();

            foreach ($kelompokMapel as $pelajaran) {
                $nilaiTerpilih = $nilaiSiswa
                    ->whereIn('mata_pelajaran_id', $pelajaran['ids'])
                    ->sortByDesc('updated_at')
                    ->first();
                $nilaiAkhir = $this->angkaNilaiAkhir($nilaiTerpilih);
                $nilaiMapel[$pelajaran['id']] = $nilaiAkhir;

                if ($nilaiAkhir !== null) {
                    $total += $nilaiAkhir;
                    $jumlahMapelDinilai++;
                }
            }

            $rekap[$murid->id] = [
                'total' => $total,
                'rata_rata' => $jumlahMapelDinilai ? round($total / $jumlahMapelDinilai, 2) : 0,
                'nilai_mapel' => $nilaiMapel,
            ];
        }

        $peringkat = collect($rekap)
            ->sortBy([
                ['total', 'desc'],
                ['rata_rata', 'desc'],
            ])
            ->keys()
            ->values()
            ->flip()
            ->map(fn ($index) => $index + 1);
        $adaNamaMapelGanda = $kelompokMapel->pluck('nama')->duplicates()->isNotEmpty();
        $headerMapel = $kelompokMapel
            ->map(fn ($pelajaran) => $adaNamaMapelGanda ? $pelajaran['nama'].' ('.$pelajaran['jenis'].')' : $pelajaran['nama'])
            ->toArray();
        $header = array_merge(['No', 'Nama Siswa', 'Kelas'], $headerMapel, ['Jumlah Total Nilai', 'Rata-rata', 'Peringkat']);
        $baris = [];

        foreach ($siswa as $index => $murid) {
            $row = [$index + 1, $murid->nama_siswa, $kelas->nama_kelas];

            foreach ($kelompokMapel as $pelajaran) {
                $row[] = $rekap[$murid->id]['nilai_mapel'][$pelajaran['id']] ?? '';
            }

            $row[] = $rekap[$murid->id]['total'];
            $row[] = $rekap[$murid->id]['rata_rata'];
            $row[] = $peringkat[$murid->id] ?? '';
            $baris[] = $row;
        }

        return $this->streamCsv('nilai-akhir-'.$this->namaFileCsv($kelas->nama_kelas).'.csv', $header, $baris);
    }

    private function unduhCsvKetidakhadiran(Request $request)
    {
        $tahunAjaran = $this->tahunAjaranTerpilih($request);
        $kelasId = $request->integer('kelas_id');
        $kelas = DB::table('kelas')->where('id', $kelasId)->first();

        abort_unless($kelas, 404);

        $siswa = DB::table('siswa')
            ->where('kelas_id', $kelasId)
            ->where('status', 'aktif')
            ->orderBy('nama_siswa')
            ->get();
        $kehadiran = DB::table('nilai_kegiatan_tambahan')
            ->where('tahun_ajaran_id', $tahunAjaran->id)
            ->where('kategori', 'Kehadiran')
            ->whereIn('siswa_id', $siswa->pluck('id'))
            ->get()
            ->keyBy(fn ($item) => $item->siswa_id.'|'.$item->kegiatan);
        $baris = [];

        foreach ($siswa as $index => $murid) {
            $sakit = (int) ($kehadiran[$murid->id.'|Sakit']->nilai ?? 0);
            $izin = (int) ($kehadiran[$murid->id.'|Izin']->nilai ?? 0);
            $tanpaKeterangan = (int) ($kehadiran[$murid->id.'|Tanpa Keterangan']->nilai ?? 0);

            $baris[] = [
                $index + 1,
                $murid->nama_siswa,
                $kelas->nama_kelas,
                $sakit,
                $izin,
                $tanpaKeterangan,
                $sakit + $izin + $tanpaKeterangan,
            ];
        }

        return $this->streamCsv('ketidakhadiran-'.$this->namaFileCsv($kelas->nama_kelas).'.csv', ['No', 'Nama Siswa', 'Kelas', 'Sakit', 'Izin', 'Tanpa Keterangan', 'Total'], $baris);
    }

    public function nilai(Request $request, ?int $mapel = null)
    {
        $guru = $this->guru();
        $tahunAjaran = $this->tahunAjaranTerpilih($request);
        $daftarTahunAjaran = $this->daftarTahunAjaran();
        $aksesNilaiWaliKelasAktif = $this->aksesNilaiWaliKelasAktif();
        $kelasWaliIds = $aksesNilaiWaliKelasAktif
            ? DB::table('guru_role')
                ->where('guru_id', $guru->id)
                ->where('role', 'wali kelas')
                ->whereNotNull('kelas_id')
                ->pluck('kelas_id')
            : collect();
        $mapelGuru = DB::table('mata_pelajaran')
            ->leftJoin('kelas', 'kelas.id', '=', 'mata_pelajaran.kelas_id')
            ->where(function ($query) use ($guru, $kelasWaliIds) {
                $query->where('mata_pelajaran.guru_id', $guru->id);

                if ($kelasWaliIds->isNotEmpty()) {
                    $query->orWhereIn('mata_pelajaran.kelas_id', $kelasWaliIds);
                }
            })
            ->select('mata_pelajaran.*', 'kelas.nama_kelas', 'kelas.tingkat')
            ->orderBy('kelas.tingkat')
            ->orderBy('kelas.nama_kelas')
            ->orderBy('mata_pelajaran.nama_mata_pelajaran')
            ->get();
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

        return view('guru.nilai', compact('mapelGuru', 'aktif', 'kelas', 'kelasAktif', 'siswa', 'nilai', 'tahunAjaran', 'daftarTahunAjaran', 'aksesNilaiWaliKelasAktif'));
    }

    public function simpanNilai(Request $request, int $mapel)
    {
        $guru = $this->guru();
        $tahunAjaran = $this->tahunAjaranInputAktif($request);
        $mataPelajaran = DB::table('mata_pelajaran')->where('id', $mapel)->first();

        if (! $this->bolehInputMapel($guru, $mataPelajaran)) {
            return back()->withErrors($this->pesanTidakBerhak());
        }

        if ($mataPelajaran->kkm === null) {
            return back()->withErrors(['kkm' => 'Isi nilai KKM terlebih dahulu sebelum menginput nilai siswa.']);
        }

        $mapelSetaraIds = $this->mapelSetaraIds($mataPelajaran);

        foreach ($request->input('nilai', []) as $siswaId => $isi) {
            $siswaId = (int) $siswaId;
            $nilaiTugas = $isi['nilai_tugas'] ?? null;
            $nilaiUts = $isi['nilai_uts'] ?? null;
            $nilaiUas = $isi['nilai_uas'] ?? null;

            if (! $this->nilaiKosongAtauValid($nilaiTugas) || ! $this->nilaiKosongAtauValid($nilaiUts) || ! $this->nilaiKosongAtauValid($nilaiUas)) {
                return back()->withErrors(['nilai' => 'Nilai harus berupa angka 0 sampai 100.'])->withInput();
            }

            DB::transaction(function () use ($siswaId, $mapel, $tahunAjaran, $mapelSetaraIds, $nilaiTugas, $nilaiUts, $nilaiUas, $isi) {
                $nilaiLama = DB::table('nilai')
                    ->where('siswa_id', $siswaId)
                    ->where('tahun_ajaran_id', $tahunAjaran->id)
                    ->whereIn('mata_pelajaran_id', $mapelSetaraIds)
                    ->orderByRaw('mata_pelajaran_id = ? desc', [$mapel])
                    ->latest('updated_at')
                    ->first();
                $dataNilai = [
                    'siswa_id' => $siswaId,
                    'mata_pelajaran_id' => $mapel,
                    'tahun_ajaran_id' => $tahunAjaran->id,
                    'nilai_tugas' => $nilaiTugas === '' ? null : $nilaiTugas,
                    'nilai_uts' => $nilaiUts === '' ? null : $nilaiUts,
                    'nilai_uas' => $nilaiUas === '' ? null : $nilaiUas,
                    'catatan_guru' => $isi['catatan_guru'] ?? null,
                    'updated_at' => now(),
                    'created_at' => now(),
                ];

                if ($nilaiLama) {
                    DB::table('nilai')
                        ->where('siswa_id', $siswaId)
                        ->where('tahun_ajaran_id', $tahunAjaran->id)
                        ->whereIn('mata_pelajaran_id', $mapelSetaraIds)
                        ->where('id', '!=', $nilaiLama->id)
                        ->delete();

                    unset($dataNilai['created_at']);

                    DB::table('nilai')->where('id', $nilaiLama->id)->update($dataNilai);

                    return;
                }

                DB::table('nilai')->insert($dataNilai);
            });
        }

        return back()->with('sukses', 'Nilai berhasil diperbarui.');
    }

    public function simpanKkm(Request $request, int $mapel)
    {
        $guru = $this->guru();
        $mataPelajaran = DB::table('mata_pelajaran')->where('id', $mapel)->first();

        if (! $this->bolehInputMapel($guru, $mataPelajaran)) {
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
        $tahunAjaran = $this->tahunAjaranTerpilih($request);
        $aktif = DB::table('mata_pelajaran')->where('id', $mapel)->first();

        if (! $this->bolehInputMapel($guru, $aktif)) {
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
        $tahunAjaran = $this->tahunAjaranAktif();

        if (! DB::table('guru_role')->where('guru_id', $guru->id)->where('role', 'wali kelas')->exists()) {
            return redirect()->route('guru.dashboard')->withErrors($this->pesanTidakBerhak());
        }

        return view('guru.catatan', [
            'siswa' => DB::table('siswa')->leftJoin('kelas', 'kelas.id', '=', 'siswa.kelas_id')->select('siswa.*', 'kelas.nama_kelas')->orderBy('nama_siswa')->get(),
            'catatan' => DB::table('catatan_walikelas')
                ->where('guru_id', $guru->id)
                ->where('tahun_ajaran_id', $tahunAjaran->id)
                ->latest()
                ->get()
                ->groupBy('siswa_id'),
            'tahunAjaran' => $tahunAjaran,
        ]);
    }

    public function simpanCatatan(Request $request)
    {
        $guru = $this->guru();
        $tahunAjaran = $this->tahunAjaranAktif();

        if (! DB::table('guru_role')->where('guru_id', $guru->id)->where('role', 'wali kelas')->exists()) {
            return back()->withErrors($this->pesanTidakBerhak());
        }

        $data = $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'catatan' => 'nullable',
        ]);

        if (! empty($data['catatan'])) {
            DB::table('catatan_walikelas')->updateOrInsert(
                ['siswa_id' => $data['siswa_id'], 'tahun_ajaran_id' => $tahunAjaran->id],
                [
                    'guru_id' => $guru->id,
                    'catatan' => $data['catatan'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        return back()->with('sukses', 'Catatan berhasil disimpan.');
    }

    public function administrasi()
    {
        $guru = $this->guru();

        if (! $this->stafKeuangan($guru->id)) {
            return redirect()->route('guru.dashboard')->withErrors($this->pesanTidakBerhak());
        }

        return redirect()->route('guru.keuangan.tagihan');
    }

    public function simpanAdministrasi(Request $request)
    {
        $guru = $this->guru();

        if (! $this->stafKeuangan($guru->id)) {
            return back()->withErrors($this->pesanTidakBerhak());
        }

        $data = $request->validate([
            'tagihan' => 'array',
            'tagihan.*.spp_makan' => 'nullable|numeric|min:0',
            'tagihan.*.kelengkapan' => 'nullable|numeric|min:0',
            'tagihan.*.lainnya' => 'nullable|numeric|min:0',
        ]);

        $namaTagihan = [
            'spp_makan' => 'SPP dan Makan',
            'kelengkapan' => 'Kelengkapan Sekolah',
            'lainnya' => 'Lainnya',
        ];
        $siswaAktif = DB::table('siswa')->where('status', 'aktif')->pluck('id')->map(fn ($id) => (int) $id)->toArray();

        foreach ($data['tagihan'] ?? [] as $siswaId => $isi) {
            if (! in_array((int) $siswaId, $siswaAktif, true)) {
                continue;
            }

            foreach ($namaTagihan as $kolom => $nama) {
                $jumlah = max(0, (float) ($isi[$kolom] ?? 0));

                DB::table('tagihan')->updateOrInsert(
                    ['siswa_id' => $siswaId, 'nama_tagihan' => $nama],
                    [
                        'jumlah' => $jumlah,
                        'status' => 'belum lunas',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }

        return back()->with('sukses', 'Tagihan siswa berhasil disimpan.');
    }

    public function jenisTagihan()
    {
        $this->jagaStafKeuangan();

        return view('admin.keuangan-jenis-tagihan', [
            'jenisTagihan' => DB::table('jenis_tagihan')->orderByDesc('aktif')->orderBy('nama_tagihan')->get(),
        ]);
    }

    public function simpanJenisTagihan(Request $request)
    {
        $this->jagaStafKeuangan();
        $data = $request->validate([
            'nama_tagihan' => 'required|string|max:255|unique:jenis_tagihan,nama_tagihan',
            'keterangan' => 'nullable|string',
            'aktif' => 'nullable',
        ]);

        DB::table('jenis_tagihan')->insert([
            'nama_tagihan' => $data['nama_tagihan'],
            'keterangan' => $data['keterangan'] ?? null,
            'aktif' => $request->boolean('aktif', true),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('sukses', 'Jenis tagihan berhasil ditambahkan.');
    }

    public function ubahJenisTagihan(Request $request, int $id)
    {
        $this->jagaStafKeuangan();
        $data = $request->validate([
            'nama_tagihan' => ['required', 'string', 'max:255', Rule::unique('jenis_tagihan', 'nama_tagihan')->ignore($id)],
            'keterangan' => 'nullable|string',
            'aktif' => 'nullable',
        ]);

        DB::table('jenis_tagihan')->where('id', $id)->update([
            'nama_tagihan' => $data['nama_tagihan'],
            'keterangan' => $data['keterangan'] ?? null,
            'aktif' => $request->boolean('aktif'),
            'updated_at' => now(),
        ]);

        return back()->with('sukses', 'Jenis tagihan berhasil diubah.');
    }

    public function tagihanKeuangan(Request $request)
    {
        $this->jagaStafKeuangan();
        $cari = trim((string) $request->query('cari', ''));
        $kelasId = $request->query('kelas_id');

        $tagihan = DB::table('tagihan')
            ->join('siswa', 'siswa.id', '=', 'tagihan.siswa_id')
            ->leftJoin('kelas', 'kelas.id', '=', 'siswa.kelas_id')
            ->leftJoin('jenis_tagihan', 'jenis_tagihan.id', '=', 'tagihan.jenis_tagihan_id')
            ->select(
                'tagihan.*',
                'siswa.nis',
                'siswa.nisn',
                'siswa.nama_siswa',
                'kelas.nama_kelas',
                'jenis_tagihan.nama_tagihan as nama_jenis_tagihan',
                DB::raw("(select coalesce(sum(jumlah_bayar), 0) from pembayaran_tagihan where pembayaran_tagihan.tagihan_id = tagihan.id and pembayaran_tagihan.status = 'valid') as total_bayar")
            )
            ->when($kelasId, fn ($query) => $query->where('siswa.kelas_id', $kelasId))
            ->when($cari !== '', function ($query) use ($cari) {
                $query->where(function ($query) use ($cari) {
                    $query->where('siswa.nama_siswa', 'like', "%$cari%")
                        ->orWhere('siswa.nis', 'like', "%$cari%")
                        ->orWhere('siswa.nisn', 'like', "%$cari%")
                        ->orWhere('tagihan.nama_tagihan', 'like', "%$cari%");
                });
            })
            ->latest('tagihan.id')
            ->get();

        return view('admin.keuangan-tagihan', [
            'tagihan' => $tagihan,
            'jenisTagihan' => DB::table('jenis_tagihan')->where('aktif', true)->orderBy('nama_tagihan')->get(),
            'kelas' => DB::table('kelas')->orderBy('nama_kelas')->get(),
            'siswa' => DB::table('siswa')
                ->leftJoin('kelas', 'kelas.id', '=', 'siswa.kelas_id')
                ->select('siswa.*', 'kelas.nama_kelas')
                ->where('siswa.status', 'aktif')
                ->orderBy('siswa.nama_siswa')
                ->get(),
            'filterCari' => $cari,
            'filterKelasId' => $kelasId,
        ]);
    }

    public function simpanTagihanKeuangan(Request $request)
    {
        $this->jagaStafKeuangan();
        $data = $request->validate([
            'jenis_tagihan_id' => 'required|exists:jenis_tagihan,id',
            'siswa_id' => 'nullable|exists:siswa,id',
            'kelas_id' => 'nullable|exists:kelas,id',
            'periode' => 'nullable|string|max:100',
            'jumlah' => 'required|numeric|min:0',
            'jatuh_tempo' => 'nullable|date',
            'keterangan' => 'nullable|string',
        ]);

        if (empty($data['siswa_id']) && empty($data['kelas_id'])) {
            return back()->withInput()->withErrors(['siswa_id' => 'Pilih siswa atau kelas tujuan tagihan.']);
        }

        $jenis = DB::table('jenis_tagihan')->where('id', $data['jenis_tagihan_id'])->first();
        $tahunAjaran = $this->tahunAjaranAktif();
        $siswaIds = ! empty($data['siswa_id'])
            ? collect([(int) $data['siswa_id']])
            : DB::table('siswa')
                ->where('kelas_id', $data['kelas_id'])
                ->where('status', 'aktif')
                ->pluck('id')
                ->map(fn ($id) => (int) $id);

        foreach ($siswaIds as $siswaId) {
            DB::table('tagihan')->updateOrInsert(
                [
                    'siswa_id' => $siswaId,
                    'jenis_tagihan_id' => $jenis->id,
                    'tahun_ajaran_id' => $tahunAjaran->id,
                    'periode' => $data['periode'] ?? null,
                ],
                [
                    'nama_tagihan' => $jenis->nama_tagihan,
                    'jumlah' => $data['jumlah'],
                    'jatuh_tempo' => $data['jatuh_tempo'] ?? null,
                    'keterangan' => $data['keterangan'] ?? null,
                    'status' => 'belum lunas',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        return back()->with('sukses', 'Tagihan berhasil dibuat atau diperbarui.');
    }

    public function pembayaranKeuangan(Request $request)
    {
        $this->jagaStafKeuangan();
        $siswaId = $request->integer('siswa_id');

        return view('admin.keuangan-pembayaran', [
            'siswa' => DB::table('siswa')->where('status', 'aktif')->orderBy('nama_siswa')->get(),
            'siswaTerpilih' => $siswaId,
            'tagihan' => $siswaId
                ? DB::table('tagihan')
                    ->where('siswa_id', $siswaId)
                    ->where('status', '!=', 'lunas')
                    ->latest()
                    ->get()
                    ->map(function ($tagihan) {
                        $tagihan->total_bayar = DB::table('pembayaran_tagihan')
                            ->where('tagihan_id', $tagihan->id)
                            ->where('status', 'valid')
                            ->sum('jumlah_bayar');

                        return $tagihan;
                    })
                : collect(),
        ]);
    }

    public function simpanPembayaranKeuangan(Request $request)
    {
        $this->jagaStafKeuangan();
        $data = $request->validate([
            'tagihan_id' => 'required|exists:tagihan,id',
            'tanggal_bayar' => 'required|date',
            'jumlah_bayar' => 'required|numeric|min:1',
            'metode_bayar' => 'required|in:tunai,transfer,QRIS,lainnya',
            'keterangan' => 'nullable|string',
            'bukti_pembayaran' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
        ]);
        $tagihan = DB::table('tagihan')->where('id', $data['tagihan_id'])->first();
        $bukti = $request->hasFile('bukti_pembayaran')
            ? $this->unggahDokumenKeuangan($request, 'bukti_pembayaran', 'pembayaran')
            : null;

        DB::table('pembayaran_tagihan')->insert([
            'tagihan_id' => $tagihan->id,
            'siswa_id' => $tagihan->siswa_id,
            'petugas_id' => session('pengguna_id'),
            'tanggal_bayar' => $data['tanggal_bayar'],
            'jumlah_bayar' => $data['jumlah_bayar'],
            'metode_bayar' => $data['metode_bayar'],
            'bukti_pembayaran' => $bukti,
            'keterangan' => $data['keterangan'] ?? null,
            'status' => 'valid',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->perbaruiStatusTagihan($tagihan->id);

        return back()->with('sukses', 'Pembayaran berhasil dicatat.');
    }

    public function riwayatPembayaranKeuangan(Request $request)
    {
        $this->jagaStafKeuangan();
        $cari = trim((string) $request->query('cari', ''));

        return view('admin.keuangan-riwayat', [
            'pembayaran' => DB::table('pembayaran_tagihan')
                ->join('siswa', 'siswa.id', '=', 'pembayaran_tagihan.siswa_id')
                ->join('tagihan', 'tagihan.id', '=', 'pembayaran_tagihan.tagihan_id')
                ->leftJoin('pengguna', 'pengguna.id', '=', 'pembayaran_tagihan.petugas_id')
                ->select('pembayaran_tagihan.*', 'siswa.nama_siswa', 'siswa.nis', 'tagihan.nama_tagihan', 'tagihan.periode', 'pengguna.nama as nama_petugas')
                ->when($cari !== '', function ($query) use ($cari) {
                    $query->where(function ($query) use ($cari) {
                        $query->where('siswa.nama_siswa', 'like', "%$cari%")
                            ->orWhere('siswa.nis', 'like', "%$cari%")
                            ->orWhere('tagihan.nama_tagihan', 'like', "%$cari%");
                    });
                })
                ->latest('pembayaran_tagihan.tanggal_bayar')
                ->latest('pembayaran_tagihan.id')
                ->get(),
            'filterCari' => $cari,
        ]);
    }

    public function batalkanPembayaranKeuangan(Request $request, int $id)
    {
        $this->jagaStafKeuangan();
        $data = $request->validate(['alasan_pembatalan' => 'required|string|max:255']);
        $pembayaran = DB::table('pembayaran_tagihan')->where('id', $id)->first();

        abort_unless($pembayaran, 404);

        DB::table('pembayaran_tagihan')->where('id', $id)->update([
            'status' => 'dibatalkan',
            'alasan_pembatalan' => $data['alasan_pembatalan'],
            'updated_at' => now(),
        ]);

        $this->perbaruiStatusTagihan($pembayaran->tagihan_id);

        return back()->with('sukses', 'Pembayaran berhasil dibatalkan.');
    }

    public function rekapKeuangan()
    {
        $this->jagaStafKeuangan();
        $totalTagihan = DB::table('tagihan')
            ->select('siswa_id', DB::raw('sum(jumlah) as total_tagihan'))
            ->groupBy('siswa_id');
        $totalBayar = DB::table('pembayaran_tagihan')
            ->where('status', 'valid')
            ->select('siswa_id', DB::raw('sum(jumlah_bayar) as total_bayar'))
            ->groupBy('siswa_id');

        return view('admin.keuangan-rekap', [
            'rekap' => DB::table('siswa')
                ->leftJoin('kelas', 'kelas.id', '=', 'siswa.kelas_id')
                ->leftJoinSub($totalTagihan, 'total_tagihan', 'total_tagihan.siswa_id', '=', 'siswa.id')
                ->leftJoinSub($totalBayar, 'total_bayar', 'total_bayar.siswa_id', '=', 'siswa.id')
                ->select(
                    'siswa.id',
                    'siswa.nis',
                    'siswa.nama_siswa',
                    'kelas.nama_kelas',
                    DB::raw('coalesce(total_tagihan.total_tagihan, 0) as total_tagihan'),
                    DB::raw('coalesce(total_bayar.total_bayar, 0) as total_bayar')
                )
                ->orderBy('siswa.nama_siswa')
                ->get(),
        ]);
    }

    public function kegiatanTambahan(Request $request)
    {
        $guru = $this->guru();
        $kelasWali = $this->kelasWali($guru->id);

        if ($kelasWali->isEmpty()) {
            return redirect()->route('guru.dashboard')->withErrors($this->pesanTidakBerhak());
        }

        $tahunAjaran = $this->tahunAjaranTerpilih($request);
        $daftarTahunAjaran = $this->daftarTahunAjaran();
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
            'daftarTahunAjaran' => $daftarTahunAjaran,
            'siswa' => $siswa,
            'nilai' => $nilai,
            'kegiatanTambahan' => $this->kegiatanTambahan,
            'kegiatanTambahanKeys' => $this->keyKegiatanTambahanUntukView(),
            'nilaiKegiatanTambahan' => $this->nilaiKegiatanTambahan,
        ]);
    }

    public function cetakRaportSiswa(Request $request, int $siswaId)
    {
        $guru = $this->guru();
        $kelasWali = $this->kelasWali($guru->id);
        $tahunAjaran = $this->tahunAjaranTerpilih($request);
        $siswa = DB::table('siswa')
            ->leftJoin('kelas', 'kelas.id', '=', 'siswa.kelas_id')
            ->select('siswa.*', 'kelas.nama_kelas', 'kelas.tingkat')
            ->where('siswa.id', $siswaId)
            ->first();

        abort_unless($siswa, 404);

        $kelasRaportId = DB::table('riwayat_kelas')
            ->where('siswa_id', $siswa->id)
            ->where('tahun_ajaran_id', $tahunAjaran->id)
            ->value('kelas_id') ?: $siswa->kelas_id;
        $kelasRaport = DB::table('kelas')->where('id', $kelasRaportId)->first();

        if ($kelasRaport) {
            $siswa->kelas_id = $kelasRaport->id;
            $siswa->nama_kelas = $kelasRaport->nama_kelas;
            $siswa->tingkat = $kelasRaport->tingkat;
        }

        if (! $kelasWali->contains('id', $siswa->kelas_id)) {
            return redirect()->route('guru.dashboard')->withErrors($this->pesanTidakBerhak());
        }

        $dataSekolah = DB::table('data_sekolah')->first();
        $tingkat = (int) ($siswa->tingkat ?: preg_replace('/\D+/', '', (string) $siswa->nama_kelas));
        $pakaiMts = $tingkat >= 7 && $tingkat <= 9;
        $nilai = DB::table('nilai')
            ->join('mata_pelajaran', 'mata_pelajaran.id', '=', 'nilai.mata_pelajaran_id')
            ->leftJoin('guru', 'guru.id', '=', 'mata_pelajaran.guru_id')
            ->where('nilai.siswa_id', $siswa->id)
            ->where('nilai.tahun_ajaran_id', $tahunAjaran->id)
            ->where(function ($query) {
                $query->where('mata_pelajaran.jenis_pelajaran', 'Formal')
                    ->orWhereNull('mata_pelajaran.jenis_pelajaran');
            })
            ->select(
                'mata_pelajaran.id',
                'mata_pelajaran.kelas_id',
                'mata_pelajaran.nama_mata_pelajaran',
                'mata_pelajaran.kkm',
                'guru.nama_guru',
                'nilai.id as nilai_id',
                'nilai.nilai_tugas',
                'nilai.nilai_uts',
                'nilai.nilai_uas',
                'nilai.catatan_guru'
            )
            ->orderBy('mata_pelajaran.nama_mata_pelajaran')
            ->get()
            ->groupBy(fn ($item) => strtolower(trim(preg_replace('/\s+/', ' ', $item->nama_mata_pelajaran))))
            ->map(function ($items) use ($siswa) {
                return $items
                    ->sortByDesc(function ($item) use ($siswa) {
                        $kelasCocok = (int) $item->kelas_id === (int) $siswa->kelas_id;
                        $nilaiLengkap = $item->nilai_tugas !== null && $item->nilai_uts !== null && $item->nilai_uas !== null;

                        return ($kelasCocok ? 1000000 : 0)
                            + ($nilaiLengkap ? 100000 : 0)
                            + (int) $item->nilai_id;
                    })
                    ->first();
            })
            ->sortBy('nama_mata_pelajaran')
            ->values();
        $kegiatanTambahan = DB::table('nilai_kegiatan_tambahan')
            ->where('nilai_kegiatan_tambahan.siswa_id', $siswa->id)
            ->where('nilai_kegiatan_tambahan.tahun_ajaran_id', $tahunAjaran->id)
            ->select('nilai_kegiatan_tambahan.*')
            ->orderBy('kategori')
            ->orderBy('kegiatan')
            ->get()
            ->groupBy('kategori');
        $siswaKelasIds = DB::table('riwayat_kelas')
            ->where('kelas_id', $siswa->kelas_id)
            ->where('tahun_ajaran_id', $tahunAjaran->id)
            ->pluck('siswa_id');

        if ($siswaKelasIds->isEmpty()) {
            $siswaKelasIds = DB::table('siswa')
                ->where('kelas_id', $siswa->kelas_id)
                ->where('status', 'aktif')
                ->pluck('id');
        }

        $peringkatKelas = DB::table('siswa')
            ->leftJoin('nilai', function ($join) use ($tahunAjaran) {
                $join->on('nilai.siswa_id', '=', 'siswa.id')
                    ->where('nilai.tahun_ajaran_id', $tahunAjaran->id);
            })
            ->leftJoin('mata_pelajaran', 'mata_pelajaran.id', '=', 'nilai.mata_pelajaran_id')
            ->whereIn('siswa.id', $siswaKelasIds)
            ->where(function ($query) {
                $query->where('mata_pelajaran.jenis_pelajaran', 'Formal')
                    ->orWhereNull('mata_pelajaran.id');
            })
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
        $catatanWaliKelas = DB::table('catatan_walikelas')
            ->where('siswa_id', $siswa->id)
            ->where('tahun_ajaran_id', $tahunAjaran->id)
            ->latest()
            ->first();

        $viewRaport = $pakaiMts ? 'guru.cetak-raport-smp' : 'guru.cetak-raport-sma';

        return view($viewRaport, [
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
            'catatanWaliKelas' => $catatanWaliKelas,
        ]);
    }

    public function cetakRaportDiniyah(Request $request, int $siswaId)
    {
        $guru = $this->guru();
        $kelasWali = $this->kelasWali($guru->id);
        $tahunAjaran = $this->tahunAjaranTerpilih($request);
        $siswa = DB::table('siswa')
            ->leftJoin('kelas', 'kelas.id', '=', 'siswa.kelas_id')
            ->select('siswa.*', 'kelas.nama_kelas', 'kelas.tingkat')
            ->where('siswa.id', $siswaId)
            ->first();

        abort_unless($siswa, 404);

        $kelasRaportId = DB::table('riwayat_kelas')
            ->where('siswa_id', $siswa->id)
            ->where('tahun_ajaran_id', $tahunAjaran->id)
            ->value('kelas_id') ?: $siswa->kelas_id;
        $kelasRaport = DB::table('kelas')->where('id', $kelasRaportId)->first();

        if ($kelasRaport) {
            $siswa->kelas_id = $kelasRaport->id;
            $siswa->nama_kelas = $kelasRaport->nama_kelas;
            $siswa->tingkat = $kelasRaport->tingkat;
        }

        if (! $kelasWali->contains('id', $siswa->kelas_id)) {
            return redirect()->route('guru.dashboard')->withErrors($this->pesanTidakBerhak());
        }

        $nilai = DB::table('nilai')
            ->join('mata_pelajaran', 'mata_pelajaran.id', '=', 'nilai.mata_pelajaran_id')
            ->where('nilai.siswa_id', $siswa->id)
            ->where('nilai.tahun_ajaran_id', $tahunAjaran->id)
            ->where('mata_pelajaran.jenis_pelajaran', 'Non formal')
            ->select(
                'mata_pelajaran.id',
                'mata_pelajaran.kelas_id',
                'mata_pelajaran.nama_mata_pelajaran',
                'nilai.id as nilai_id',
                'nilai.nilai_tugas',
                'nilai.nilai_uts',
                'nilai.nilai_uas',
                'nilai.catatan_guru'
            )
            ->orderBy('mata_pelajaran.nama_mata_pelajaran')
            ->get()
            ->groupBy(fn ($item) => strtolower(trim(preg_replace('/\s+/', ' ', $item->nama_mata_pelajaran))))
            ->map(function ($items) use ($siswa) {
                return $items
                    ->sortByDesc(function ($item) use ($siswa) {
                        $kelasCocok = (int) $item->kelas_id === (int) $siswa->kelas_id;
                        $nilaiLengkap = $item->nilai_tugas !== null && $item->nilai_uts !== null && $item->nilai_uas !== null;

                        return ($kelasCocok ? 1000000 : 0)
                            + ($nilaiLengkap ? 100000 : 0)
                            + (int) $item->nilai_id;
                    })
                    ->first();
            })
            ->sortBy('nama_mata_pelajaran')
            ->values();

        return view('guru.cetak-raport-diniyah', array_merge([
            'guru' => $guru,
            'waliKelas' => $guru,
            'siswa' => $siswa,
            'tahunAjaran' => $tahunAjaran,
            'nilai' => $nilai,
        ], $this->infoDiniyah($siswa)));
    }

    public function rekapRaport(Request $request)
    {
        $guru = $this->guru();
        $kelasWali = $this->kelasWali($guru->id);

        if ($kelasWali->isEmpty()) {
            return redirect()->route('guru.dashboard')->withErrors($this->pesanTidakBerhak());
        }

        $tahunAjaran = $this->tahunAjaranTerpilih($request);
        $daftarTahunAjaran = $this->daftarTahunAjaran();
        $kelasAktif = $request->integer('kelas_id') ?: $kelasWali->first()->id;

        if (! $kelasWali->contains('id', $kelasAktif)) {
            return redirect()->route('guru.dashboard')->withErrors($this->pesanTidakBerhak());
        }

        $siswa = DB::table('siswa')
            ->where('kelas_id', $kelasAktif)
            ->where('status', 'aktif')
            ->orderBy('nama_siswa')
            ->get();
        $mapel = DB::table('mata_pelajaran')
            ->where(fn ($query) => $query->where('kelas_id', $kelasAktif)->orWhereNull('kelas_id'))
            ->where('jenis_pelajaran', 'Formal')
            ->orderBy('nama_mata_pelajaran')
            ->get();
        $nilai = DB::table('nilai')
            ->where('tahun_ajaran_id', $tahunAjaran->id)
            ->whereIn('siswa_id', $siswa->pluck('id'))
            ->get()
            ->groupBy('siswa_id');
        $kegiatanTambahan = DB::table('nilai_kegiatan_tambahan')
            ->where('tahun_ajaran_id', $tahunAjaran->id)
            ->whereIn('siswa_id', $siswa->pluck('id'))
            ->get()
            ->groupBy('siswa_id');
        $catatan = DB::table('catatan_walikelas')
            ->where('tahun_ajaran_id', $tahunAjaran->id)
            ->whereIn('siswa_id', $siswa->pluck('id'))
            ->get()
            ->keyBy('siswa_id');

        return view('guru.rekap-raport', compact('kelasWali', 'kelasAktif', 'tahunAjaran', 'daftarTahunAjaran', 'siswa', 'mapel', 'nilai', 'kegiatanTambahan', 'catatan'));
    }

    public function simpanKegiatanTambahan(Request $request)
    {
        $guru = $this->guru();
        $kelasWali = $this->kelasWali($guru->id);
        $tahunAjaran = $this->tahunAjaranInputAktif($request);
        $kelasId = $request->integer('kelas_id');
        $kegiatanTambahanKeys = $this->keyKegiatanTambahanUntukSimpan();

        if (! $kelasWali->contains('id', $kelasId)) {
            return back()->withErrors($this->pesanTidakBerhak());
        }

        $siswaKelas = DB::table('siswa')
            ->where('kelas_id', $kelasId)
            ->where('status', 'aktif')
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->toArray();
        $jumlahTersimpan = 0;

        foreach ($request->input('nilai', []) as $siswaId => $kategoriList) {
            $siswaId = (int) $siswaId;

            if (! in_array($siswaId, $siswaKelas, true)) {
                continue;
            }

            foreach ($kategoriList as $kategoriKey => $kegiatanList) {
                $kategoriInfo = $kegiatanTambahanKeys[$kategoriKey] ?? null;

                if (! $kategoriInfo) {
                    continue;
                }

                $kategori = $kategoriInfo['kategori'];

                foreach ($kegiatanList as $kegiatanKey => $isi) {
                    $kegiatan = $kategoriInfo['kegiatan'][$kegiatanKey] ?? null;

                    if (! $kegiatan) {
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

                    $jumlahTersimpan++;
                }
            }
        }

        if ($jumlahTersimpan === 0) {
            return back()->withErrors([
                'nilai' => 'Belum ada nilai kegiatan tambahan yang berhasil disimpan. Pastikan kelas yang dipilih sesuai dengan kelas wali.',
            ])->withInput();
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
