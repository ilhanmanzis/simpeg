<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanSertifikats extends Model
{
    use HasFactory;
    protected $table = 'pengajuan_sertifikat';
    protected $primaryKey = 'id_pengajuan';
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function sertifikat()
    {
        return $this->belongsTo(Sertifikats::class, 'id_sertifikat', 'id_sertifikat');
    }
}
