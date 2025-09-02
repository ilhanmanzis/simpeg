<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataDiri extends Model
{
    use HasFactory;
    protected $table = 'data_diri';
    protected $primaryKey = "id_data_diri";
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }


    public function dokumen()
    {
        return $this->belongsTo(Dokumens::class, 'foto', 'nomor_dokumen');
    }
    public function serdosen()
    {
        return $this->belongsTo(Dokumens::class, 'serdos', 'nomor_dokumen');
    }
}
