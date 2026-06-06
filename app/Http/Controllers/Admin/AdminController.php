<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    private function jaga()
    {
        abort_unless(session('jenis_pengguna') === 'admin', 403);
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

    private function unggah(Request $request, string $nama, string $folder): ?string
    {
        if (! $request->hasFile($nama)) {
            return null;
        }

        $file = $request->file($nama);
        $namaFile = Str::uuid().'.'.$file->getClientOriginalExtension();
        $file->move(public_path("uploads/$folder"), $namaFile);

        return "uploads/$folder/$namaFile";
    }

    public function dashboard()
    {
        $this->jaga();

        return view('admin.dashboard', [
            'jumlahGuru' => DB::table('guru')->count(),
            'jumlahSiswa' => DB::table('siswa')->count(),
            'jumlahKelas' => DB::table('kelas')->count(),
            'jumlahMapel' => DB::table('mata_pelajaran')->count(),
        ]);
    }

    public function slider()
    {
        $this->jaga();
        return view('admin.slider', ['slider' => DB::table('slider')->latest()->get()]);
    }

    public function simpanSlider(Request $request)
    {
        $this->jaga();
        $data = $request->validate([
            'gambar' => 'required|image',
            'link' => 'nullable|string|max:255',
            'aktif' => 'nullable',
        ]);

        $data['judul'] = 'Slider';
        $data['deskripsi'] = null;
        $data['gambar'] = $this->unggah($request, 'gambar', 'slider');
        $data['aktif'] = $request->boolean('aktif');
        $data['created_at'] = now();
        $data['updated_at'] = now();
        DB::table('slider')->insert($data);
        return back()->with('sukses', 'Slider berhasil disimpan.');
    }

    public function hapusSlider(int $id)
    {
        $this->jaga();
        DB::table('slider')->where('id', $id)->delete();
        return back()->with('sukses', 'Slider dihapus.');
    }

    public function berita()
    {
        $this->jaga();
        return view('admin.berita', ['berita' => DB::table('berita')->latest()->get()]);
    }

    public function simpanBerita(Request $request)
    {
        $this->jaga();
        $data = $request->validate(['judul' => 'required', 'isi' => 'required', 'tanggal_berita' => 'nullable|date', 'foto_kegiatan' => 'nullable|image']);
        $data['foto_kegiatan'] = $this->unggah($request, 'foto_kegiatan', 'berita');
        $data['created_at'] = now();
        $data['updated_at'] = now();
        DB::table('berita')->insert($data);
        return back()->with('sukses', 'Berita berhasil disimpan.');
    }

    public function hapusBerita(int $id)
    {
        $this->jaga();
        DB::table('berita')->where('id', $id)->delete();
        return back()->with('sukses', 'Berita dihapus.');
    }

    public function prestasi()
    {
        $this->jaga();
        return view('admin.prestasi', ['prestasi' => DB::table('prestasi')->latest()->get()]);
    }

    public function simpanPrestasi(Request $request)
    {
        $this->jaga();
        $data = $request->validate([
            'judul' => 'required',
            'keterangan' => 'nullable',
            'foto' => 'nullable|image',
        ]);
        $data['foto'] = $this->unggah($request, 'foto', 'prestasi');
        $data['created_at'] = now();
        $data['updated_at'] = now();
        DB::table('prestasi')->insert($data);
        return back()->with('sukses', 'Prestasi berhasil disimpan.');
    }

    public function hapusPrestasi(int $id)
    {
        $this->jaga();
        DB::table('prestasi')->where('id', $id)->delete();
        return back()->with('sukses', 'Prestasi dihapus.');
    }

    public function galeri()
    {
        $this->jaga();
        return view('admin.galeri', ['galeri' => DB::table('galeri')->latest()->get()]);
    }

    public function simpanGaleri(Request $request)
    {
        $this->jaga();
        $data = $request->validate([
            'judul' => 'nullable',
            'foto' => 'required|image',
        ]);
        $data['foto'] = $this->unggah($request, 'foto', 'galeri');
        $data['created_at'] = now();
        $data['updated_at'] = now();
        DB::table('galeri')->insert($data);
        return back()->with('sukses', 'Foto galeri berhasil disimpan.');
    }

    public function hapusGaleri(int $id)
    {
        $this->jaga();
        DB::table('galeri')->where('id', $id)->delete();
        return back()->with('sukses', 'Foto galeri dihapus.');
    }

    public function informasi()
    {
        $this->jaga();
        return view('admin.informasi', ['informasi' => DB::table('informasi_sekolah')->latest()->get()]);
    }

    public function simpanInformasi(Request $request)
    {
        $this->jaga();
        $data = $request->validate(['judul' => 'required', 'isi' => 'required', 'kontak' => 'nullable']);
        $data['created_at'] = now();
        $data['updated_at'] = now();
        DB::table('informasi_sekolah')->insert($data);
        return back()->with('sukses', 'Informasi berhasil disimpan.');
    }

    public function hapusInformasi(int $id)
    {
        $this->jaga();
        DB::table('informasi_sekolah')->where('id', $id)->delete();
        return back()->with('sukses', 'Informasi dihapus.');
    }

    public function dataSekolah()
    {
        $this->jaga();

        return view('admin.data-sekolah', [
            'dataSekolah' => DB::table('data_sekolah')->first(),
        ]);
    }

    public function simpanDataSekolah(Request $request)
    {
        $this->jaga();
        $data = $request->validate([
            'kepala_mts' => 'nullable|string|max:255',
            'kepala_sma' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
        ]);

        $data['nama_mts'] = "MTs Ma'arif 20";
        $data['nama_sma'] = 'SMA Nurul Huda';
        $data['updated_at'] = now();
        $dataSekolah = DB::table('data_sekolah')->first();

        if ($dataSekolah) {
            DB::table('data_sekolah')->where('id', $dataSekolah->id)->update($data);
        } else {
            $data['created_at'] = now();
            DB::table('data_sekolah')->insert($data);
        }

        return back()->with('sukses', 'Data sekolah berhasil disimpan.');
    }

    public function guru()
    {
        $this->jaga();
        return view('admin.guru', [
            'guru' => DB::table('guru')->join('pengguna', 'pengguna.id', '=', 'guru.pengguna_id')->select('guru.*', 'pengguna.identitas')->latest('guru.id')->get(),
            'roles' => DB::table('guru_role')
                ->leftJoin('kelas', 'kelas.id', '=', 'guru_role.kelas_id')
                ->select('guru_role.*', 'kelas.nama_kelas')
                ->get()
                ->groupBy('guru_id'),
            'kelas' => DB::table('kelas')->orderBy('nama_kelas')->get(),
        ]);
    }

    public function simpanGuru(Request $request)
    {
        $this->jaga();
        $data = $request->validate([
            'id_guru' => 'required',
            'nama_guru' => 'required',
            'kata_sandi' => 'required',
            'role' => 'array',
            'role.*' => 'in:pengampu mata pelajaran,wali kelas,staff',
            'wali_kelas_id' => 'nullable|exists:kelas,id',
            'staff_jenis' => 'nullable|in:staff TU,staff keuangan',
            'telepon' => 'nullable',
            'alamat' => 'nullable',
        ]);

        $roles = $data['role'] ?? [];

        if (in_array('wali kelas', $roles, true) && empty($data['wali_kelas_id'])) {
            return back()->withInput()->withErrors(['wali_kelas_id' => 'Pilih kelas untuk wali kelas.']);
        }

        if (in_array('staff', $roles, true) && empty($data['staff_jenis'])) {
            return back()->withInput()->withErrors(['staff_jenis' => 'Pilih jenis staff.']);
        }

        DB::transaction(function () use ($data) {
            $penggunaId = DB::table('pengguna')->insertGetId([
                'nama' => $data['nama_guru'],
                'identitas' => $data['id_guru'],
                'kata_sandi' => Hash::make($data['kata_sandi']),
                'jenis_pengguna' => 'guru',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $guruId = DB::table('guru')->insertGetId([
                'pengguna_id' => $penggunaId,
                'id_guru' => $data['id_guru'],
                'nama_guru' => $data['nama_guru'],
                'telepon' => $data['telepon'] ?? null,
                'alamat' => $data['alamat'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            foreach ($data['role'] ?? [] as $role) {
                DB::table('guru_role')->insert([
                    'guru_id' => $guruId,
                    'role' => $role,
                    'kelas_id' => $role === 'wali kelas' ? $data['wali_kelas_id'] : null,
                    'staff_jenis' => $role === 'staff' ? $data['staff_jenis'] : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        return back()->with('sukses', 'Guru dan akun login berhasil dibuat.');
    }

    public function hapusGuru(int $id)
    {
        $this->jaga();
        $penggunaId = DB::table('guru')->where('id', $id)->value('pengguna_id');
        DB::table('pengguna')->where('id', $penggunaId)->delete();
        return back()->with('sukses', 'Guru dihapus.');
    }

    public function ubahGuru(Request $request, int $id)
    {
        $this->jaga();
        $guru = DB::table('guru')->where('id', $id)->first();

        abort_unless($guru, 404);

        $data = $request->validate([
            'id_guru' => [
                'required',
                Rule::unique('guru', 'id_guru')->ignore($id),
                Rule::unique('pengguna', 'identitas')->ignore($guru->pengguna_id),
            ],
            'nama_guru' => 'required',
            'role' => 'array',
            'role.*' => 'in:pengampu mata pelajaran,wali kelas,staff',
            'wali_kelas_id' => 'nullable|exists:kelas,id',
            'staff_jenis' => 'nullable|in:staff TU,staff keuangan',
            'telepon' => 'nullable',
            'alamat' => 'nullable',
        ]);

        $roles = $data['role'] ?? [];

        if (in_array('wali kelas', $roles, true) && empty($data['wali_kelas_id'])) {
            return back()->withInput()->withErrors(['wali_kelas_id' => 'Pilih kelas untuk wali kelas.']);
        }

        if (in_array('staff', $roles, true) && empty($data['staff_jenis'])) {
            return back()->withInput()->withErrors(['staff_jenis' => 'Pilih jenis staff.']);
        }

        DB::transaction(function () use ($data, $guru, $id) {
            DB::table('pengguna')->where('id', $guru->pengguna_id)->update([
                'nama' => $data['nama_guru'],
                'identitas' => $data['id_guru'],
                'updated_at' => now(),
            ]);

            DB::table('guru')->where('id', $id)->update([
                'id_guru' => $data['id_guru'],
                'nama_guru' => $data['nama_guru'],
                'telepon' => $data['telepon'] ?? null,
                'alamat' => $data['alamat'] ?? null,
                'updated_at' => now(),
            ]);

            DB::table('guru_role')->where('guru_id', $id)->delete();

            foreach ($data['role'] ?? [] as $role) {
                DB::table('guru_role')->insert([
                    'guru_id' => $id,
                    'role' => $role,
                    'kelas_id' => $role === 'wali kelas' ? $data['wali_kelas_id'] : null,
                    'staff_jenis' => $role === 'staff' ? $data['staff_jenis'] : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        return back()->with('sukses', 'Data guru berhasil diubah.');
    }

    public function ubahPasswordGuru(Request $request, int $id)
    {
        $this->jaga();
        $data = $request->validate(['kata_sandi' => 'required|min:6']);
        $penggunaId = DB::table('guru')->where('id', $id)->value('pengguna_id');
        DB::table('pengguna')->where('id', $penggunaId)->update(['kata_sandi' => Hash::make($data['kata_sandi']), 'updated_at' => now()]);
        return back()->with('sukses', 'Password guru berhasil diubah.');
    }

    public function siswa()
    {
        $this->jaga();
        return view('admin.siswa', [
            'siswa' => DB::table('siswa')->leftJoin('kelas', 'kelas.id', '=', 'siswa.kelas_id')->select('siswa.*', 'kelas.nama_kelas')->latest('siswa.id')->get(),
            'kelas' => DB::table('kelas')->orderBy('nama_kelas')->get(),
        ]);
    }

    public function simpanSiswa(Request $request)
    {
        $this->jaga();
        $tahunAjaran = $this->tahunAjaranAktif();
        $data = $request->validate([
            'nis' => 'required',
            'nama_siswa' => 'required',
            'kata_sandi' => 'required',
            'kelas_id' => 'nullable|exists:kelas,id',
            'jenis_kelamin' => 'nullable',
            'telepon' => 'nullable',
            'alamat' => 'nullable',
        ]);

        DB::transaction(function () use ($data, $tahunAjaran) {
            $penggunaId = DB::table('pengguna')->insertGetId([
                'nama' => $data['nama_siswa'],
                'identitas' => $data['nis'],
                'kata_sandi' => Hash::make($data['kata_sandi']),
                'jenis_pengguna' => 'siswa',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            DB::table('siswa')->insert([
                'pengguna_id' => $penggunaId,
                'kelas_id' => $data['kelas_id'] ?? null,
                'nis' => $data['nis'],
                'nama_siswa' => $data['nama_siswa'],
                'jenis_kelamin' => $data['jenis_kelamin'] ?? null,
                'telepon' => $data['telepon'] ?? null,
                'alamat' => $data['alamat'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if (! empty($data['kelas_id'])) {
                $siswaId = DB::table('siswa')->where('pengguna_id', $penggunaId)->value('id');
                DB::table('riwayat_kelas')->insert([
                    'siswa_id' => $siswaId,
                    'kelas_id' => $data['kelas_id'],
                    'tahun_ajaran_id' => $tahunAjaran->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        return back()->with('sukses', 'Siswa dan akun login berhasil dibuat.');
    }

    public function hapusSiswa(int $id)
    {
        $this->jaga();
        $penggunaId = DB::table('siswa')->where('id', $id)->value('pengguna_id');
        DB::table('pengguna')->where('id', $penggunaId)->delete();
        return back()->with('sukses', 'Siswa dihapus.');
    }

    public function ubahPasswordSiswa(Request $request, int $id)
    {
        $this->jaga();
        $data = $request->validate(['kata_sandi' => 'required|min:6']);
        $penggunaId = DB::table('siswa')->where('id', $id)->value('pengguna_id');
        DB::table('pengguna')->where('id', $penggunaId)->update(['kata_sandi' => Hash::make($data['kata_sandi']), 'updated_at' => now()]);
        return back()->with('sukses', 'Password siswa berhasil diubah.');
    }

    public function kelas()
    {
        $this->jaga();
        return view('admin.kelas', [
            'kelas' => DB::table('kelas')->orderBy('nama_kelas')->get(),
            'siswa' => DB::table('siswa')->where('status', 'aktif')->orderBy('nama_siswa')->get()->groupBy('kelas_id'),
        ]);
    }

    public function naikKelas()
    {
        $this->jaga();

        return view('admin.naik-kelas', [
            'tahunAjaran' => DB::table('tahun_ajaran')->orderByDesc('id')->get(),
            'tahunAktif' => $this->tahunAjaranAktif(),
            'kelas' => DB::table('kelas')->orderBy('nama_kelas')->get(),
            'siswa' => DB::table('siswa')
                ->leftJoin('kelas', 'kelas.id', '=', 'siswa.kelas_id')
                ->select('siswa.*', 'kelas.nama_kelas')
                ->where('siswa.status', 'aktif')
                ->orderBy('kelas.nama_kelas')
                ->orderBy('siswa.nama_siswa')
                ->get()
                ->groupBy('kelas_id'),
            'lulus' => DB::table('siswa')->where('status', 'lulus')->latest('tanggal_lulus')->get(),
        ]);
    }

    public function simpanTahunAjaran(Request $request)
    {
        $this->jaga();
        $data = $request->validate([
            'nama_tahun_ajaran' => [
                'required',
                Rule::unique('tahun_ajaran', 'nama_tahun_ajaran')->where('semester', $request->semester),
            ],
            'semester' => 'required|in:ganjil,genap',
        ]);

        DB::table('tahun_ajaran')->insert([
            'nama_tahun_ajaran' => $data['nama_tahun_ajaran'],
            'semester' => $data['semester'],
            'aktif' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('sukses', 'Tahun ajaran berhasil ditambahkan.');
    }

    public function aktifkanTahunAjaran(int $id)
    {
        $this->jaga();
        DB::transaction(function () use ($id) {
            DB::table('tahun_ajaran')->update(['aktif' => false, 'updated_at' => now()]);
            DB::table('tahun_ajaran')->where('id', $id)->update(['aktif' => true, 'updated_at' => now()]);
        });

        return back()->with('sukses', 'Tahun ajaran aktif berhasil diubah.');
    }

    public function prosesNaikKelas(Request $request)
    {
        $this->jaga();
        $data = $request->validate([
            'kelas_asal_id' => 'required|exists:kelas,id',
            'kelas_tujuan_id' => 'required|exists:kelas,id',
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
        ]);

        $siswa = DB::table('siswa')
            ->where('kelas_id', $data['kelas_asal_id'])
            ->where('status', 'aktif')
            ->get();

        DB::transaction(function () use ($data, $siswa) {
            foreach ($siswa as $murid) {
                DB::table('siswa')->where('id', $murid->id)->update([
                    'kelas_id' => $data['kelas_tujuan_id'],
                    'updated_at' => now(),
                ]);

                DB::table('riwayat_kelas')->updateOrInsert(
                    ['siswa_id' => $murid->id, 'tahun_ajaran_id' => $data['tahun_ajaran_id']],
                    ['kelas_id' => $data['kelas_tujuan_id'], 'created_at' => now(), 'updated_at' => now()]
                );
            }
        });

        return back()->with('sukses', $siswa->count().' siswa berhasil dinaikkan kelas.');
    }

    public function prosesLulusKelas(Request $request)
    {
        $this->jaga();
        $data = $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
        ]);

        $siswa = DB::table('siswa')
            ->where('kelas_id', $data['kelas_id'])
            ->where('status', 'aktif')
            ->get();

        DB::transaction(function () use ($data, $siswa) {
            foreach ($siswa as $murid) {
                DB::table('riwayat_kelas')->updateOrInsert(
                    ['siswa_id' => $murid->id, 'tahun_ajaran_id' => $data['tahun_ajaran_id']],
                    ['kelas_id' => $data['kelas_id'], 'created_at' => now(), 'updated_at' => now()]
                );

                DB::table('siswa')->where('id', $murid->id)->update([
                    'status' => 'lulus',
                    'tanggal_lulus' => now()->toDateString(),
                    'updated_at' => now(),
                ]);
            }
        });

        return back()->with('sukses', $siswa->count().' siswa berhasil diluluskan.');
    }

    public function simpanKelas(Request $request)
    {
        $this->jaga();
        $data = $request->validate(['nama_kelas' => 'required', 'tingkat' => 'nullable', 'keterangan' => 'nullable']);
        $data['created_at'] = now();
        $data['updated_at'] = now();
        DB::table('kelas')->insert($data);
        return back()->with('sukses', 'Kelas berhasil disimpan.');
    }

    public function mataPelajaran()
    {
        $this->jaga();
        return view('admin.mata-pelajaran', [
            'mapel' => DB::table('mata_pelajaran')
                ->leftJoin('kelas', 'kelas.id', '=', 'mata_pelajaran.kelas_id')
                ->leftJoin('guru', 'guru.id', '=', 'mata_pelajaran.guru_id')
                ->select('mata_pelajaran.*', 'kelas.nama_kelas', 'guru.nama_guru')
                ->latest('mata_pelajaran.id')
                ->get(),
            'kelas' => DB::table('kelas')->orderBy('nama_kelas')->get(),
            'guru' => DB::table('guru')->orderBy('nama_guru')->get(),
        ]);
    }

    public function simpanMataPelajaran(Request $request)
    {
        $this->jaga();
        $data = $request->validate([
            'nama_mata_pelajaran' => 'required',
            'kelas_id' => 'nullable|exists:kelas,id',
            'guru_id' => 'nullable|exists:guru,id',
            'keterangan' => 'nullable',
        ]);
        $data['created_at'] = now();
        $data['updated_at'] = now();
        DB::table('mata_pelajaran')->insert($data);
        return back()->with('sukses', 'Mata pelajaran berhasil disimpan.');
    }

    public function ubahMataPelajaran(Request $request, int $id)
    {
        $this->jaga();
        $data = $request->validate([
            'nama_mata_pelajaran' => 'required',
            'kelas_id' => 'nullable|exists:kelas,id',
            'guru_id' => 'nullable|exists:guru,id',
        ]);
        $data['updated_at'] = now();

        DB::table('mata_pelajaran')->where('id', $id)->update($data);

        return back()->with('sukses', 'Mata pelajaran berhasil diubah.');
    }

    public function hapusMataPelajaran(int $id)
    {
        $this->jaga();
        DB::table('nilai')->where('mata_pelajaran_id', $id)->delete();
        DB::table('mata_pelajaran')->where('id', $id)->delete();

        return back()->with('sukses', 'Mata pelajaran berhasil dihapus.');
    }
}
