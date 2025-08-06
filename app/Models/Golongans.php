<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Golongans extends Model
{
    use HasFactory;
    protected $table = 'golongan';
    protected $primaryKey = "id_golongan";
    protected $guarded = [];

    public function fungsionals()
    {
        return $this->hasMany(JabatanFungsionals::class, 'id_golongan', 'id_golongan');
    }
}
