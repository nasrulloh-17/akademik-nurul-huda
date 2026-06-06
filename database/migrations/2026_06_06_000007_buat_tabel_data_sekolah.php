<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('data_sekolah')) {
            return;
        }

        Schema::create('data_sekolah', function (Blueprint $table) {
            $table->id();
            $table->string('nama_mts')->nullable();
            $table->string('kepala_mts')->nullable();
            $table->string('nama_sma')->nullable();
            $table->string('kepala_sma')->nullable();
            $table->text('alamat')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_sekolah');
    }
};
