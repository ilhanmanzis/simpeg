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
        Schema::create('sertifikat', function (Blueprint $table) {
            $table->increments('id_sertifikat');
            $table->unsignedInteger('id_user');
            $table->string('dokumen');
            $table->string('kategori');
            $table->string('nama_sertifikat');
            $table->string('penyelenggara');
            $table->date('tanggal_diperoleh');
            $table->date('tanggal_selesai')->nullable();
            $table->timestamps();

            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
            $table->foreign('dokumen')->references('nomor_dokumen')->on('dokumen')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sertifikat');
    }
};
