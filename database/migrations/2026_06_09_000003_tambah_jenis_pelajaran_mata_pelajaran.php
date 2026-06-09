<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mata_pelajaran', function (Blueprint $table) {
            if (! Schema::hasColumn('mata_pelajaran', 'jenis_pelajaran')) {
                $table->enum('jenis_pelajaran', ['Formal', 'Non formal'])
                    ->default('Formal')
                    ->after('nama_mata_pelajaran');
            }
        });
    }

    public function down(): void
    {
        Schema::table('mata_pelajaran', function (Blueprint $table) {
            if (Schema::hasColumn('mata_pelajaran', 'jenis_pelajaran')) {
                $table->dropColumn('jenis_pelajaran');
            }
        });
    }
};
