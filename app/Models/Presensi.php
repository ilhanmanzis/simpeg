<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    use HasFactory;

    protected $table = 'presensis';
    protected $primaryKey = 'id_presensi';

    protected $guarded = [];

    // =====================
    // RELATIONSHIPS
    // =====================

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function aktivitas()
    {
        return $this->hasOne(PresensiAktivitas::class, 'id_presensi', 'id_presensi');
    }

    public function dokumen()
    {
        return $this->belongsToMany(
            Dokumens::class,
            'presensi_dokumen',
            'id_presensi',
            'nomor_dokumen'
        )->withTimestamps();
    }
}
