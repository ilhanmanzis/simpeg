<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Semesters extends Model
{
    use HasFactory;
    protected $table = 'semester';
    protected $primaryKey = "id_semester";
    protected $guarded = [];

    public function pengajaran()
    {
        return $this->hasMany(Pengajarans::class, 'id_semester', 'id_semester');
    }
}
