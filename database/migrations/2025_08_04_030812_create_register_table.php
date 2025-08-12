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
        Schema::create('register', function (Blueprint $table) {
            $table->increments('id_register');
            $table->string('email');
            $table->string('npp');
            $table->string('password');
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
            $table->enum('role', ['dosen', 'karyawan'])->default('dosen');
            $table->enum('status', ['disetujui', 'pending', 'ditolak'])->default('pending');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('register');
    }
};
