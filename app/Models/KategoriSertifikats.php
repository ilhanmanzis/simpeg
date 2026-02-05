<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriSertifikats extends Model
{
    use HasFactory;
    protected $table = 'kategori_sertifikat';
    protected $primaryKey = 'id_kategori';
    protected $guarded = [];

    public function sertifikat()
    {
        return $this->hasMany(Sertifikats::class, 'id_kategori', 'id_kategori');
    }
    public function pengajuanSertifikat()
    {
        return $this->hasMany(PengajuanSertifikats::class, 'id_kategori', 'id_kategori');
    }
}
