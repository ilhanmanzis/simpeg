<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegisterPendidikans extends Model
{
    use HasFactory;
    protected $table = 'register_pendidikan';
    protected $primaryKey = 'id_register_pendidikan';
    protected $guarded = [];

    public function jenjang()
    {
        return $this->belongsTo(Jenjangs::class, 'id_jenjang', 'id_jenjang');
    }
    public function register()
    {
        return $this->belongsTo(Registers::class, 'id_register', 'id_register');
    }
}
