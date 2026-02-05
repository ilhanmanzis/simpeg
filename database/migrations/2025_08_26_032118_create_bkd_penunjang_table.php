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
        Schema::create('bkd_penunjang', function (Blueprint $table) {
            $table->increments('id_penunjang');
            $table->unsignedInteger('id_user');
            $table->string('dokumen');
            $table->string('name');
            $table->string('penyelenggara');
            $table->string('tanggal_diperoleh');

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
        Schema::dropIfExists('bkd_penunjang');
    }
};
