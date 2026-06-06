<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mata_pelajaran', function (Blueprint $table) {
            if (! Schema::hasColumn('mata_pelajaran', 'kkm')) {
                $table->decimal('kkm', 5, 2)->nullable()->after('nama_mata_pelajaran');
            }
        });
    }

    public function down(): void
    {
        Schema::table('mata_pelajaran', function (Blueprint $table) {
            if (Schema::hasColumn('mata_pelajaran', 'kkm')) {
                $table->dropColumn('kkm');
            }
        });
    }
};
