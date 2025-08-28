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
        Schema::create('pengajuan_bkd_pengajaran_detail', function (Blueprint $table) {
            $table->increments('id_detail');
            $table->unsignedInteger('id_pengajuan_pengajaran');
            $table->string('nama_matkul');
            $table->string('sks');
            $table->string('bap');
            $table->string('nilai');


            $table->timestamps();

            $table->foreign('id_pengajuan_pengajaran')->references('id_pengajuan_pengajaran')->on('pengajuan_bkd_pengajaran')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_bkd_pengajaran_detail');
    }
};
