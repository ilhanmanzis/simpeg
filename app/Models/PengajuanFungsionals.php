<?php

namespace App\Models;

use App\Http\Controllers\Dosen\Fungsional;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanFungsionals extends Model
{
    use HasFactory;
    protected $table = 'pengajuan_fungsional';
    protected $primaryKey = 'id_pengajuan_fungsional';
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function fungsional()
    {
        return $this->belongsTo(JabatanFungsionals::class, 'id_fungsional', 'id_fungsional');
    }
}
