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
}
