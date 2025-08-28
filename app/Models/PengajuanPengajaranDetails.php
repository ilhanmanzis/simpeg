<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanPengajaranDetails extends Model
{
    use HasFactory;
    protected $table = 'pengajuan_bkd_pengajaran_detail';
    protected $primaryKey = 'id_detail';
    protected $guarded = [];

    public function pengajuanPengajaran()
    {
        return $this->belongsTo(PengajuanPengajarans::class, 'id_pengajuan_pengajaran', 'id_pengajuan_pengajaran');
    }
}
