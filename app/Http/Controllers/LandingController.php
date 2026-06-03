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
            'informasi' => DB::table('informasi_sekolah')->latest()->get(),
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
}
