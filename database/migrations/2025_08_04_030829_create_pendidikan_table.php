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
        Schema::create('pendidikan', function (Blueprint $table) {
            $table->increments('id_pendidikan');
            $table->unsignedInteger('id_user');
            $table->unsignedInteger('id_jenjang');
            $table->string('institusi');
            $table->string('gelar')->nullable();
            $table->string('program_studi')->nullable();
            $table->string('tahun_lulus');
            $table->string('ijazah');
            $table->string('transkip_nilai')->nullable();
            $table->timestamps();

            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
            $table->foreign('id_jenjang')->references('id_jenjang')->on('jenjang')->onDelete('cascade');
            $table->foreign('ijazah')->references('nomor_dokumen')->on('dokumen')->onDelete('cascade');
            $table->foreign('transkip_nilai')->references('nomor_dokumen')->on('dokumen')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pendidikan');
    }
};
