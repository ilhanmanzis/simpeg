<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PresensiDokumen extends Model
{
    use HasFactory;

    protected $table = 'presensi_dokumen';

    protected $guarded = [];

    // =====================
    // RELATIONSHIPS
    // =====================

    public function presensi()
    {
        return $this->belongsTo(Presensi::class, 'id_presensi', 'id_presensi');
    }

    public function dokumen()
    {
        return $this->belongsTo(Dokumens::class, 'nomor_dokumen', 'nomor_dokumen');
    }
}
