<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tahun_ajaran', function (Blueprint $table) {
            if (! Schema::hasColumn('tahun_ajaran', 'semester')) {
                $table->enum('semester', ['ganjil', 'genap'])->default('ganjil')->after('nama_tahun_ajaran');
            }
        });

        if ($this->indexExists('tahun_ajaran', 'tahun_ajaran_nama_tahun_ajaran_unique')) {
            DB::statement('ALTER TABLE tahun_ajaran DROP INDEX tahun_ajaran_nama_tahun_ajaran_unique');
        }

        if (! $this->indexExists('tahun_ajaran', 'tahun_ajaran_nama_tahun_ajaran_semester_unique')) {
            Schema::table('tahun_ajaran', function (Blueprint $table) {
                $table->unique(['nama_tahun_ajaran', 'semester']);
            });
        }
    }

    public function down(): void
    {
        if ($this->indexExists('tahun_ajaran', 'tahun_ajaran_nama_tahun_ajaran_semester_unique')) {
            Schema::table('tahun_ajaran', function (Blueprint $table) {
                $table->dropUnique('tahun_ajaran_nama_tahun_ajaran_semester_unique');
            });
        }

        Schema::table('tahun_ajaran', function (Blueprint $table) {
            if (Schema::hasColumn('tahun_ajaran', 'semester')) {
                $table->dropColumn('semester');
            }
        });

        if (! $this->indexExists('tahun_ajaran', 'tahun_ajaran_nama_tahun_ajaran_unique')) {
            Schema::table('tahun_ajaran', function (Blueprint $table) {
                $table->unique('nama_tahun_ajaran');
            });
        }
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
