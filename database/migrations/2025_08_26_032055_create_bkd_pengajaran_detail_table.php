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
        Schema::create('bkd_pengajaran_detail', function (Blueprint $table) {
            $table->increments('id_detail');
            $table->unsignedInteger('id_pengajaran');
            $table->string('nama_matkul');
            $table->string('sks');
            $table->string('bap');
            $table->string('nilai');
            $table->timestamps();

            $table->foreign('id_pengajaran')->references('id_pengajaran')->on('bkd_pengajaran')->onDelete('cascade');
            $table->foreign('bap')->references('nomor_dokumen')->on('dokumen')->onDelete('cascade');
            $table->foreign('nilai')->references('nomor_dokumen')->on('dokumen')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bkd_pengajaran_detail');
    }
};
