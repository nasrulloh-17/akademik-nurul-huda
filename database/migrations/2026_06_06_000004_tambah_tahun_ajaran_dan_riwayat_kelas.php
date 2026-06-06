<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('tahun_ajaran')) {
            Schema::create('tahun_ajaran', function (Blueprint $table) {
                $table->id();
                $table->string('nama_tahun_ajaran');
                $table->enum('semester', ['ganjil', 'genap'])->default('ganjil');
                $table->boolean('aktif')->default(false);
                $table->timestamps();
                $table->unique(['nama_tahun_ajaran', 'semester']);
            });
        }

        $tahunAjaran = DB::table('tahun_ajaran')->where('aktif', true)->first();
        $tahunAjaranId = $tahunAjaran?->id;

        if (! $tahunAjaranId) {
            $tahunAjaranId = DB::table('tahun_ajaran')->insertGetId([
                'nama_tahun_ajaran' => now()->month >= 7 ? now()->year.'/'.now()->copy()->addYear()->year : now()->copy()->subYear()->year.'/'.now()->year,
                'semester' => now()->month >= 7 ? 'ganjil' : 'genap',
                'aktif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Schema::table('siswa', function (Blueprint $table) {
            if (! Schema::hasColumn('siswa', 'status')) {
                $table->enum('status', ['aktif', 'lulus'])->default('aktif')->after('foto_profil');
            }

            if (! Schema::hasColumn('siswa', 'tanggal_lulus')) {
                $table->date('tanggal_lulus')->nullable()->after('status');
            }
        });

        if (! Schema::hasTable('riwayat_kelas')) {
            Schema::create('riwayat_kelas', function (Blueprint $table) {
                $table->id();
                $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
                $table->foreignId('kelas_id')->nullable()->constrained('kelas')->nullOnDelete();
                $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajaran')->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['siswa_id', 'tahun_ajaran_id']);
            });
        }

        if (! Schema::hasColumn('nilai', 'tahun_ajaran_id')) {
            if (! $this->indexExists('nilai', 'nilai_siswa_id_index_for_tahun')) {
                DB::statement('ALTER TABLE nilai ADD INDEX nilai_siswa_id_index_for_tahun (siswa_id)');
            }

            Schema::table('nilai', function (Blueprint $table) {
                $table->dropUnique(['siswa_id', 'mata_pelajaran_id']);
                $table->foreignId('tahun_ajaran_id')->nullable()->after('mata_pelajaran_id')->constrained('tahun_ajaran')->nullOnDelete();
                $table->unique(['siswa_id', 'mata_pelajaran_id', 'tahun_ajaran_id'], 'nilai_siswa_mapel_tahun_unique');
            });
        }

        $siswa = DB::table('siswa')->whereNotNull('kelas_id')->get();

        foreach ($siswa as $murid) {
            DB::table('riwayat_kelas')->insert([
                'siswa_id' => $murid->id,
                'kelas_id' => $murid->kelas_id,
                'tahun_ajaran_id' => $tahunAjaranId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('nilai')->whereNull('tahun_ajaran_id')->update(['tahun_ajaran_id' => $tahunAjaranId]);
    }

    public function down(): void
    {
        Schema::table('nilai', function (Blueprint $table) {
            $table->dropUnique('nilai_siswa_mapel_tahun_unique');
            $table->dropConstrainedForeignId('tahun_ajaran_id');
            $table->unique(['siswa_id', 'mata_pelajaran_id']);
        });

        Schema::dropIfExists('riwayat_kelas');

        Schema::table('siswa', function (Blueprint $table) {
            $table->dropColumn(['status', 'tanggal_lulus']);
        });

        Schema::dropIfExists('tahun_ajaran');
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
