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
        Schema::create('jabatan_fungsional', function (Blueprint $table) {
            $table->increments('id_fungsional');
            $table->unsignedInteger('id_golongan');
            $table->string('nama_jabatan');
            $table->foreign('id_golongan')->references('id_golongan')->on('golongan')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jabatan_fungsional');
    }
};
