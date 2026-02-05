<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penelitians extends Model
{
    use HasFactory;
    protected $table = 'bkd_penelitian';
    protected $primaryKey = 'id_penelitian';
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    // scope untuk pencarian judul
    public function scopeSearchJudul($query, $keyword)
    {
        if (!empty($keyword)) {
            return $query->where('judul', 'like', "%{$keyword}%");
        }

        return $query;
    }
}
