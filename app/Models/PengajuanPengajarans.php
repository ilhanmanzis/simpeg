<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanPengajarans extends Model
{
    use HasFactory;
    protected $table = 'pengajuan_bkd_pengajaran';
    protected $primaryKey = 'id_pengajuan_pengajaran';
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function semester()
    {
        return $this->belongsTo(Semesters::class, 'id_semester', 'id_semester');
    }

    public function detail()
    {
        return $this->hasMany(PengajuanPengajaranDetails::class, 'id_pengajuan_pengajaran', 'id_pengajuan_pengajaran');
    }
}
