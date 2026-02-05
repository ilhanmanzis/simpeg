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
        Schema::create('presensi_aktivitas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_presensi');

            // SKS
            $table->integer('sks_siang')->nullable();
            $table->integer('sks_malam')->nullable();
            $table->integer('sks_praktikum_siang')->nullable();
            $table->integer('sks_praktikum_malam')->nullable();

            $table->text('mata_kuliah')->nullable();
            $table->text('kegiatan')->nullable();

            // aktivitas dengan jumlah + keterangan
            $table->integer('seminar_jumlah')->nullable();
            $table->text('seminar_keterangan')->nullable();

            $table->integer('pembimbing_jumlah')->nullable();
            $table->text('pembimbing_keterangan')->nullable();

            $table->integer('penguji_jumlah')->nullable();
            $table->text('penguji_keterangan')->nullable();

            $table->integer('kkl_jumlah')->nullable();
            $table->text('kkl_keterangan')->nullable();

            $table->integer('tugas_luar_jumlah')->nullable();
            $table->text('tugas_luar_keterangan')->nullable();

            $table->timestamps();

            $table->foreign('id_presensi')
                ->references('id_presensi')
                ->on('presensis')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presensi_aktivitas');
    }
};
