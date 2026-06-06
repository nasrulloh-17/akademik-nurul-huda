<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            if (! Schema::hasColumn('siswa', 'nisn')) {
                $table->string('nisn')->nullable()->after('nis');
            }
        });

        DB::table('siswa')
            ->whereNull('nisn')
            ->orderBy('id')
            ->get()
            ->each(function ($siswa) {
                DB::table('siswa')->where('id', $siswa->id)->update([
                    'nisn' => $siswa->nis,
                    'updated_at' => now(),
                ]);
            });

        if (! $this->indexExists('siswa', 'siswa_nisn_unique')) {
            Schema::table('siswa', function (Blueprint $table) {
                $table->unique('nisn');
            });
        }

        DB::table('siswa')
            ->join('pengguna', 'pengguna.id', '=', 'siswa.pengguna_id')
            ->where('pengguna.jenis_pengguna', 'siswa')
            ->update(['pengguna.identitas' => DB::raw('siswa.nisn')]);
    }

    public function down(): void
    {
        DB::table('siswa')
            ->join('pengguna', 'pengguna.id', '=', 'siswa.pengguna_id')
            ->where('pengguna.jenis_pengguna', 'siswa')
            ->update(['pengguna.identitas' => DB::raw('siswa.nis')]);

        Schema::table('siswa', function (Blueprint $table) {
            if (Schema::hasColumn('siswa', 'nisn')) {
                if ($this->indexExists('siswa', 'siswa_nisn_unique')) {
                    $table->dropUnique('siswa_nisn_unique');
                }

                $table->dropColumn('nisn');
            }
        });
    }

    private function indexExists(string $table, string $index): bool
    {
        return DB::table('information_schema.statistics')
            ->where('table_schema', DB::getDatabaseName())
            ->where('table_name', $table)
            ->where('index_name', $index)
            ->exists();
    }
};
