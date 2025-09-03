<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sertifikats extends Model
{
    use HasFactory;
    protected $table = 'sertifikat';
    protected $primaryKey = 'id_sertifikat';
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function dokumenSertifikat()
    {
        return $this->belongsTo(Dokumens::class, 'dokumen', 'nomor_dokumen');
    }

    public function scopeSearchJudul($query, $keyword)
    {
        if (!empty($keyword)) {
            return $query->where('nama_sertifikat', 'like', "%{$keyword}%");
        }

        return $query;
    }

    public function pengajuanSertifikats()
    {
        return $this->hasMany(PengajuanSertifikats::class, 'id_sertifikat', 'id_sertifikat');
    }
}
