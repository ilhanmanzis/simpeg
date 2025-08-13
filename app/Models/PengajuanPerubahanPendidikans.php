<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanPerubahanPendidikans extends Model
{
    use HasFactory;
    protected $table = 'pengajuan_perubahan_pendidikan';
    protected $primaryKey = 'id_perubahan';
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function pendidikan()
    {
        return $this->belongsTo(Pendidikans::class, 'id_pendidikan', 'id_pendidikan');
    }

    public function jenjang()
    {
        return $this->belongsTo(Jenjangs::class, 'id_jenjang', 'id_jenjang');
    }
}
