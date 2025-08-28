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
        Schema::create('bkd_pengabdian', function (Blueprint $table) {
            $table->increments('id_pengabdian');
            $table->unsignedInteger('id_user');
            $table->string('judul');
            $table->string('lokasi');
            $table->text('terimakasih')->nullable();
            $table->string('permohonan')->nullable();
            $table->string('tugas')->nullable();
            $table->string('modul')->nullable();
            $table->string('foto')->nullable();
            $table->timestamps();

            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
            $table->foreign('permohonan')->references('nomor_dokumen')->on('dokumen')->onDelete('cascade');
            $table->foreign('tugas')->references('nomor_dokumen')->on('dokumen')->onDelete('cascade');
            $table->foreign('modul')->references('nomor_dokumen')->on('dokumen')->onDelete('cascade');
            $table->foreign('foto')->references('nomor_dokumen')->on('dokumen')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bkd_pengabdian');
    }
};
