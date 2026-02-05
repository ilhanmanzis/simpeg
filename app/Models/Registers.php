<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registers extends Model
{
    use HasFactory;
    protected $table = 'register';
    protected $primaryKey = 'id_register';
    protected $guarded = [];

    public function registerPendidikan()
    {
        return $this->hasMany(RegisterPendidikans::class, 'id_register', 'id_register');
    }
}
