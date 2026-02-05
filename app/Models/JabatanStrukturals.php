<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JabatanStrukturals extends Model
{
    use HasFactory;
    protected $table = 'jabatan_struktural';
    protected $primaryKey = "id_struktural";
    protected $guarded = [];

    public function strukturalUser()
    {
        return $this->hasMany(StrukturalUsers::class, 'id_struktural', 'id_struktural');
    }


    // Ambil yang aktif (jika ada)
    public function activeCurrent()
    {
        return $this->hasOne(StrukturalUsers::class, 'id_struktural', 'id_struktural')
            ->where('status', 'aktif')
            ->latestOfMany('id_struktural_user'); // paling baru berdasarkan PK
    }

    // Ambil terbaru apapun statusnya (fallback)
    public function latestAny()
    {
        return $this->hasOne(StrukturalUsers::class, 'id_struktural', 'id_struktural')
            ->latestOfMany('id_struktural_user'); // paling baru
    }
}
