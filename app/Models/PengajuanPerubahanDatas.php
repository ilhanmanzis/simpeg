<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanPerubahanDatas extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_perubahan_data';
    protected $primaryKey = 'id_perubahan';
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
