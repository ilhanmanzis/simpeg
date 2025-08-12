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
        Schema::create('pengajuan_perubahan_pendidikan', function (Blueprint $table) {
            $table->increments('id_perubahan');
            $table->unsignedInteger('id_user');
            $table->unsignedInteger('id_jenjang')->nullable();
            $table->unsignedInteger('id_pendidikan')->nullable();
            $table->string('institusi')->nullable();
            $table->string('gelar')->nullable();
            $table->string('program_studi')->nullable();
            $table->string('tahun_lulus')->nullable();
            $table->string('ijazah')->nullable();
            $table->string('transkip_nilai')->nullable();
            $table->string('keterangan')->nullable();
            $table->enum('jenis', ['tambah', 'edit', 'delete'])->default('edit');
            $table->enum('status', ['disetujui', 'pending', 'ditolak'])->default('pending');
            $table->timestamps();

            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
            $table->foreign('id_jenjang')->references('id_jenjang')->on('jenjang')->onDelete('cascade');
            $table->foreign('id_pendidikan')->references('id_pendidikan')->on('pendidikan')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_perubahan_pendidikan');
    }
};
