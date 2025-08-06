<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pendidikans extends Model
{
    use HasFactory;
    protected $table = 'pendidikan';
    protected $primaryKey = 'id_pendidikan';
    protected $guarded = [];
    
}
