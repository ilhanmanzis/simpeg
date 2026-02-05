<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajaranDetails extends Model
{
    use HasFactory;
    protected $table = 'bkd_pengajaran_detail';
    protected $primaryKey = 'id_detail';
    protected $guarded = [];

    public function pengajaran()
    {
        return $this->belongsTo(Pengajarans::class, 'id_pengajaran', 'id_pengajaran');
    }


    public function nilaiPengajaran()
    {
        return $this->belongsTo(Dokumens::class, 'nilai', 'nomor_dokumen');
    }
}
