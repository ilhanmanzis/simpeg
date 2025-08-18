<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FungsionalUsers extends Model
{
    use HasFactory;
    protected $table = 'jabatan_fungsional_user';
    protected $primaryKey = 'id_fungsional_user';
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function fungsional()
    {
        return $this->belongsTo(JabatanFungsionals::class, 'id_fungsional', 'id_fungsional');
    }

    public function dokumen()
    {
        return $this->belongsTo(Dokumens::class, 'sk', 'nomor_dokumen');
    }
}
