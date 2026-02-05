<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('presensi_dokumen', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_presensi');
            $table->string('nomor_dokumen');
            $table->timestamps();

            $table->foreign('id_presensi')
                ->references('id_presensi')
                ->on('presensis')
                ->onDelete('cascade');

            $table->foreign('nomor_dokumen')
                ->references('nomor_dokumen')
                ->on('dokumen')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presensi_dokumen');
    }
};
