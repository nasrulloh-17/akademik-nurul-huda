<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class LandingController extends Controller
{
    public function index()
    {
        return view('landing.index', [
            'slider' => DB::table('slider')->where('aktif', true)->latest()->get(),
            'berita' => DB::table('berita')->latest('tanggal_berita')->limit(6)->get(),
            'prestasi' => DB::table('prestasi')->latest()->get(),
            'galeri' => DB::table('galeri')->latest()->get(),
            'informasi' => DB::table('informasi_sekolah')->latest()->get(),
        ]);
    }

    public function semuaBerita()
    {
        return view('landing.berita-semua', [
            'berita' => DB::table('berita')
                ->latest('tanggal_berita')
                ->paginate(9),
        ]);
    }

    public function berita(int $id)
    {
        $berita = DB::table('berita')->where('id', $id)->first();

        abort_if(! $berita, 404);

        return view('landing.berita-detail', [
            'berita' => $berita,
            'beritaLainnya' => DB::table('berita')
                ->where('id', '!=', $id)
                ->latest('tanggal_berita')
                ->limit(3)
                ->get(),
        ]);
    }

    public function upload(string $path)
    {
        abort_if(str_contains($path, '..'), 404);

        $file = public_path('uploads/'.$path);

        abort_if(! is_file($file), 404);

        return response()->file($file);
    }
}
