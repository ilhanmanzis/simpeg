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
        Schema::create('data_diri', function (Blueprint $table) {
            $table->increments('id_data_diri');
            $table->unsignedInteger('id_user');
            $table->string('nuptk')->nullable();
            $table->string('nip')->nullable();
            $table->string('nidk')->nullable();
            $table->string('nidn')->nullable();
            $table->string('name');
            $table->string('no_ktp');
            $table->string('no_hp')->nullable();
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['Laki-Laki', 'Perempuan'])->default('Laki-Laki');
            $table->enum('agama', ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Budha', 'Konghucu'])->default('Islam');
            $table->date('tanggal_bergabung');
            $table->text('alamat')->nullable();
            $table->string('rt')->nullable();
            $table->string('rw')->nullable();
            $table->string('desa')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kabupaten')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('foto')->nullable();
            $table->enum('tersertifikasi', ['sudah', 'tidak'])->default('tidak');
            $table->string('serdos')->nullable();
            $table->enum('pimpinan', ['aktif', 'nonaktif'])->default('nonaktif');
            $table->string('bpjs')->nullable();
            $table->integer('anak');
            $table->integer('istri');
            $table->enum('golongan_darah', ['A', 'B', 'AB', 'O', '-'])->nullable();
            $table->timestamps();

            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
            $table->foreign('foto')->references('nomor_dokumen')->on('dokumen')->onDelete('cascade');
            $table->foreign('serdos')->references('nomor_dokumen')->on('dokumen')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_diri');
    }
};
