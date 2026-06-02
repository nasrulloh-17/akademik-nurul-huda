<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('pengguna')->updateOrInsert(
            ['identitas' => 'superadmin'],
            [
                'nama' => 'Superadmin',
                'kata_sandi' => Hash::make('admin123'),
                'jenis_pengguna' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('slider')->insertOrIgnore([
            'id' => 1,
            'judul' => 'Yayasan Nurul Huda Munjuk',
            'deskripsi' => 'Membangun generasi berilmu, berakhlak, dan siap menghadapi masa depan.',
            'aktif' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('informasi_sekolah')->insertOrIgnore([
            'id' => 1,
            'judul' => 'Informasi Sekolah',
            'isi' => 'Sistem informasi akademik terpadu untuk guru, siswa, dan administrasi sekolah.',
            'kontak' => 'Munjuk, Lombok Timur',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
