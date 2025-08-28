<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengajarans extends Model
{
    use HasFactory;
    protected $table = 'bkd_pengajaran';
    protected $primaryKey = 'id_pengajaran';
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function semester()
    {
        return $this->belongsTo(Semesters::class, 'id_semester', 'id_semester');
    }

    public function skPengajaran()
    {
        return $this->belongsTo(Dokumens::class, 'sk', 'nomor_dokumen');
    }

    public function detail()
    {
        return $this->hasMany(PengajaranDetails::class, 'id_pengajaran', 'id_pengajaran');
    }
}
