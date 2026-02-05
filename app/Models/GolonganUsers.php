<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GolonganUsers extends Model
{
    use HasFactory;
    protected $table = 'golongan_user';
    protected $primaryKey = 'id_golongan_user';
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function golongan()
    {
        return $this->belongsTo(Golongans::class, 'id_golongan', 'id_golongan');
    }

    public function dokumen()
    {
        return $this->belongsTo(Dokumens::class, 'sk', 'nomor_dokumen');
    }
}
