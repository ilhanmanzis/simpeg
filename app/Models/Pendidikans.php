<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pendidikans extends Model
{
    use HasFactory;
    protected $table = 'pendidikan';
    protected $primaryKey = 'id_pendidikan';
    protected $guarded = [];

    public function jenjang()
    {
        return $this->belongsTo(Jenjangs::class, 'id_jenjang', 'id_jenjang');
    }

    public function dokumenIjazah()
    {
        return $this->belongsTo(Dokumens::class, 'ijazah', 'nomor_dokumen');
    }

    public function dokumenTranskipNilai()
    {
        return $this->belongsTo(Dokumens::class, 'transkip_nilai', 'nomor_dokumen');
    }


    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function pengajuanPerubahanPendidikan()
    {
        return $this->hasMany(PengajuanPerubahanPendidikans::class, 'id_pendidikan', 'id_pendidikan');
    }
}
