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
        Schema::create('pengajuan_sertifikat', function (Blueprint $table) {
            $table->increments('id_pengajuan');
            $table->unsignedInteger('id_sertifikat');
            $table->unsignedInteger('id_user');
            $table->string('dokumen')->nullable();
            $table->string('kategori')->nullable();
            $table->string('nama_sertifikat')->nullable();
            $table->string('penyelenggara')->nullable();
            $table->string('keterangan')->nullable();
            $table->date('tanggal_diperoleh')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->enum('status', ['pending', 'disetujui', 'ditolak'])->default('pending');
            $table->enum('jenis', ['tambah', 'edit', 'hapus'])->default('edit');
            $table->timestamps();

            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
            $table->foreign('id_sertifikat')->references('id_sertifikat')->on('sertifikat')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_sertifikat');
    }
};
