<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class StrukturalUsers extends Model
{
    use HasFactory;
    protected $table = 'jabatan_struktural_user';
    protected $primaryKey = 'id_struktural_user';
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function struktural()
    {
        return $this->belongsTo(JabatanStrukturals::class, 'id_struktural', 'id_struktural');
    }

    public function dokumen()
    {
        return $this->belongsTo(Dokumens::class, 'sk', 'nomor_dokumen');
    }


    public function scopeAktifPadaTanggal($query, ?Carbon $tanggal = null)
    {
        $tanggal = $tanggal ?? Carbon::today();

        return $query->where('status', 'aktif')
            ->whereIn('id_struktural', [1, 2])
            ->where('tanggal_mulai', '<=', $tanggal)
            ->where(function ($q) use ($tanggal) {
                $q->whereNull('tanggal_selesai')
                    ->orWhere('tanggal_selesai', '>=', $tanggal);
            });
    }

    protected static function booted()
    {
        static::saved(function ($model) {
            self::clearUserCache($model->id_user);
        });

        static::deleted(function ($model) {
            self::clearUserCache($model->id_user);
        });
    }

    protected static function clearUserCache($userId)
    {
        // hapus cache hari ini
        $today = Carbon::today()->toDateString();

        Cache::forget("jabatan_struktural_aktif_{$userId}_{$today}");
    }
}
