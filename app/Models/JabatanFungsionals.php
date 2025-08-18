<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JabatanFungsionals extends Model
{
    use HasFactory;
    protected $table = 'jabatan_fungsional';
    protected $primaryKey = "id_fungsional";
    protected $guarded = [];

    public function golongan()
    {
        return $this->belongsTo(Golongans::class, 'id_golongan', 'id_golongan');
    }

    public function fungsionalUser()
    {
        return $this->hasMany(FungsionalUsers::class, 'id_fungsional', 'id_fungsional');
    }
}
