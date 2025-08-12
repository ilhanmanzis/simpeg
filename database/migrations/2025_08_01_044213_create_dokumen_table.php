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
        Schema::create('dokumen', function (Blueprint $table) {
            $table->string('nomor_dokumen')->primary();
            $table->unsignedInteger('id_user');
            $table->string('path_file');
            $table->date('tanggal_upload');
            $table->string('file_id')->nullable();
            $table->string('view_url')->nullable();
            $table->string('download_url')->nullable();
            $table->string('preview_url')->nullable();
            $table->timestamps();

            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumen');
    }
};
