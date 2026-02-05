<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penunjangs extends Model
{
    use HasFactory;
    protected $table = 'bkd_penunjang';
    protected $primaryKey = 'id_penunjang';
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function dokumenPenunjang()
    {
        return $this->belongsTo(Dokumens::class, 'dokumen', 'nomor_dokumen');
    }
    // scope untuk pencarian judul
    public function scopeSearchJudul($query, $keyword)
    {
        if (!empty($keyword)) {
            return $query->where('name', 'like', "%{$keyword}%");
        }

        return $query;
    }
}
