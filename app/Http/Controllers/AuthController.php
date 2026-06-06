<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function formAdmin()
    {
        return view('auth.login', ['jenis' => 'admin', 'judul' => 'Login Admin', 'label' => 'Username Admin']);
    }

    public function formGuru()
    {
        return view('auth.login', ['jenis' => 'guru', 'judul' => 'Login Guru', 'label' => 'ID Guru']);
    }

    public function formSiswa()
    {
        return view('auth.login', ['jenis' => 'siswa', 'judul' => 'Login Siswa', 'label' => 'NIS Siswa']);
    }

    public function loginAdmin(Request $request)
    {
        return $this->login($request, 'admin', 'admin.dashboard');
    }

    public function loginGuru(Request $request)
    {
        return $this->login($request, 'guru', 'guru.dashboard');
    }

    public function loginSiswa(Request $request)
    {
        return $this->login($request, 'siswa', 'siswa.dashboard');
    }

    public function formUbahPassword()
    {
        abort_unless(session('pengguna_id'), 403);

        return view('auth.ubah-password');
    }

    public function ubahPassword(Request $request)
    {
        abort_unless(session('pengguna_id'), 403);

        $data = $request->validate([
            'kata_sandi_lama' => ['required'],
            'kata_sandi_baru' => ['required', 'min:6', 'confirmed'],
        ]);

        $pengguna = DB::table('pengguna')->where('id', session('pengguna_id'))->first();

        if (! $pengguna || ! Hash::check($data['kata_sandi_lama'], $pengguna->kata_sandi)) {
            return back()->withErrors(['kata_sandi_lama' => 'Password lama tidak sesuai.']);
        }

        DB::table('pengguna')->where('id', $pengguna->id)->update([
            'kata_sandi' => Hash::make($data['kata_sandi_baru']),
            'updated_at' => now(),
        ]);

        return back()->with('sukses', 'Password berhasil diubah.');
    }

    private function login(Request $request, string $jenis, string $tujuan)
    {
        $data = $request->validate([
            'identitas' => ['required'],
            'kata_sandi' => ['required'],
        ]);

        $pengguna = DB::table('pengguna')
            ->where('identitas', $data['identitas'])
            ->where('jenis_pengguna', $jenis)
            ->first();

        if (! $pengguna || ! Hash::check($data['kata_sandi'], $pengguna->kata_sandi)) {
            return back()->withErrors(['identitas' => 'Identitas atau kata sandi tidak sesuai.'])->withInput();
        }

        $request->session()->regenerate();
        session([
            'pengguna_id' => $pengguna->id,
            'jenis_pengguna' => $pengguna->jenis_pengguna,
            'nama_pengguna' => $pengguna->nama,
        ]);

        return redirect()->route($tujuan);
    }

    public function keluar(Request $request)
    {
        $request->session()->flush();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('beranda');
    }
}
