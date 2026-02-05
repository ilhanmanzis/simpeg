<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanGolongans extends Model
{
    use HasFactory;
    protected $table = 'pengajuan_golongan';
    protected $primaryKey = 'id_pengajuan_golongan';
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function golongan()
    {
        return $this->belongsTo(Golongans::class, 'id_golongan', 'id_golongan');
    }
}
