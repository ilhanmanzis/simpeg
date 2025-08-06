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
        Schema::create('register_pendidikan', function (Blueprint $table) {
            $table->increments('id_register_pendidikan');
            $table->unsignedInteger('id_register');
            $table->unsignedInteger('id_jenjang');
            $table->string('institusi');
            $table->string('gelar')->nullable();
            $table->string('program_studi');
            $table->string('tahun_lulus');
            $table->string('ijazah');
            $table->string('transkip_nilai')->nullable();
            $table->timestamps();

            $table->foreign('id_register')->references('id_register')->on('register')->onDelete('cascade');
            $table->foreign('id_jenjang')->references('id_jenjang')->on('jenjang')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('register_pendidikan');
    }
};
