<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dokumens extends Model
{
    use HasFactory;
    protected $table = 'dokumen';
    protected $primaryKey = "nomor_dokumen";
    public $incrementing = false;
    protected $keyType = 'string';

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function dataDiri()
    {
        return $this->belongsTo(DataDiri::class, 'foto', 'nomor_dokumen');
    }

    public function pendidikansSebagaiIjazah()
    {
        return $this->belongsTo(Pendidikans::class, 'ijazah', 'nomor_dokumen');
    }

    public function pendidikansSebagaiTranskip()
    {
        return $this->belongsTo(Pendidikans::class, 'transkip_nilai', 'nomor_dokumen');
    }

    public function golongan()
    {
        return $this->belongsTo(GolonganUsers::class, 'sk', 'nomor_dokumen');
    }
    public function struktural()
    {
        return $this->belongsTo(StrukturalUsers::class, 'sk', 'nomor_dokumen');
    }
    public function fungsional()
    {
        return $this->belongsTo(FungsionalUsers::class, 'sk', 'nomor_dokumen');
    }
}
