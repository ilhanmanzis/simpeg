<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Indexes extends Model
{
    use HasFactory;
    protected $table = 'indexes';
    protected $primaryKey = 'id_index';
    protected $fillable = ['name'];

    public function Penelitian()
    {
        return $this->hasMany(Penelitians::class, 'id_index', 'id_index');
    }

    public function PengajuanBkdPenelitian()
    {
        return $this->hasMany(PengajuanPenelitians::class, 'id_index', 'id_index');
    }
}
