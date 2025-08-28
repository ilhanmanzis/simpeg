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
        Schema::create('pengajuan_bkd_penunjang', function (Blueprint $table) {
            $table->increments('id_pengajuan');
            $table->unsignedInteger('id_user');
            $table->string('dokumen');
            $table->string('name');
            $table->string('penyelenggara');
            $table->string('tanggal_diperoleh');
            $table->enum('status', ['pending', 'disetujui', 'ditolak'])->default('pending');
            $table->string('keterangan')->nullable();

            $table->timestamps();
            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_bkd_penunjang');
    }
};
