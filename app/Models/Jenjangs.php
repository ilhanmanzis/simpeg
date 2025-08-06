<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jenjangs extends Model
{
    use HasFactory;
    protected $table = "jenjang";
    protected $primaryKey = "id_jenjang";
    protected $guarded = [];

    public function registerPendidikan()
    {
        return $this->hasMany(RegisterPendidikans::class, 'id_jenjang', 'id_jenjang');
    }
    public function pendidikan()
    {
        return $this->hasMany(Pendidikans::class, 'id_jenjang', 'id_jenjang');
    }
}
