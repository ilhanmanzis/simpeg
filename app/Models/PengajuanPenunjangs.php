<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanPenunjangs extends Model
{
    use HasFactory;
    protected $table = 'pengajuan_bkd_penunjang';
    protected $primaryKey = 'id_pengajuan';
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
