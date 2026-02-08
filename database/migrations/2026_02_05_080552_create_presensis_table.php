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
        Schema::create('presensis', function (Blueprint $table) {
            $table->bigIncrements('id_presensi');
            $table->unsignedInteger('id_user');

            $table->date('tanggal');
            $table->time('jam_datang')->nullable();
            $table->time('jam_pulang')->nullable();
            $table->integer('durasi_menit')->nullable();

            // lokasi presensi datang
            $table->decimal('lat_datang', 10, 7)->nullable();
            $table->decimal('long_datang', 10, 7)->nullable();
            $table->text('alamat_datang')->nullable();
            $table->integer('jarak_datang')->nullable();

            // lokasi presensi pulang
            $table->decimal('lat_pulang', 10, 7)->nullable();
            $table->decimal('long_pulang', 10, 7)->nullable();
            $table->text('alamat_pulang')->nullable();
            $table->integer('jarak_pulang')->nullable();

            $table->enum('status_lokasi_datang', ['didalam_radius', 'diluar_radius'])
                ->default('didalam_radius');
            $table->enum('status_lokasi_pulang', ['didalam_radius', 'diluar_radius'])
                ->default('didalam_radius');

            $table->enum('status_jam_kerja', ['hijau', 'kuning', 'merah'])
                ->default('hijau');
            $table->enum('status_kehadiran', ['hadir', 'izin', 'sakit', 'alpha'])
                ->default('hadir');

            $table->timestamps();

            $table->foreign('id_user')
                ->references('id_user')
                ->on('users')
                ->onDelete('cascade');

            $table->unique(['id_user', 'tanggal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presensis');
    }
};
