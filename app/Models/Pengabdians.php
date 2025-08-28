<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengabdians extends Model
{
    use HasFactory;
    protected $table = 'bkd_pengabdian';
    protected $primaryKey = 'id_pengabdian';
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function permohonanPengabdian()
    {
        return $this->belongsTo(Dokumens::class, 'permohonan', 'nomor_dokumen');
    }

    public function tugasPengabdian()
    {
        return $this->belongsTo(Dokumens::class, 'tugas', 'nomor_dokumen');
    }

    public function modulPengabdian()
    {
        return $this->belongsTo(Dokumens::class, 'modul', 'nomor_dokumen');
    }

    public function fotoPengabdian()
    {
        return $this->belongsTo(Dokumens::class, 'foto', 'nomor_dokumen');
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
