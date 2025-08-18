<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StrukturalUsers extends Model
{
    use HasFactory;
    protected $table = 'jabatan_struktural_user';
    protected $primaryKey = 'id_struktural_user';
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function struktural()
    {
        return $this->belongsTo(JabatanStrukturals::class, 'id_struktural', 'id_struktural');
    }

    public function dokumen()
    {
        return $this->belongsTo(Dokumens::class, 'sk', 'nomor_dokumen');
    }
}
