<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('slider', function (Blueprint $table) {
            if (! Schema::hasColumn('slider', 'link')) {
                $table->string('link')->nullable()->after('gambar');
            }
        });
    }

    public function down(): void
    {
        Schema::table('slider', function (Blueprint $table) {
            if (Schema::hasColumn('slider', 'link')) {
                $table->dropColumn('link');
            }
        });
    }
};
