<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PresensiAktivitas extends Model
{
    use HasFactory;

    protected $table = 'presensi_aktivitas';

    protected $guarded = [];

    // =====================
    // RELATIONSHIPS
    // =====================

    public function presensi()
    {
        return $this->belongsTo(Presensi::class, 'id_presensi', 'id_presensi');
    }
}
