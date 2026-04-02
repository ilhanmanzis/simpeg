<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Pendidikans extends Model
{
    use HasFactory;
    protected $table = 'pendidikan';
    protected $primaryKey = 'id_pendidikan';
    protected $guarded = [];

    public function jenjang()
    {
        return $this->belongsTo(Jenjangs::class, 'id_jenjang', 'id_jenjang');
    }

    public function dokumenIjazah()
    {
        return $this->belongsTo(Dokumens::class, 'ijazah', 'nomor_dokumen');
    }

    public function dokumenTranskipNilai()
    {
        return $this->belongsTo(Dokumens::class, 'transkip_nilai', 'nomor_dokumen');
    }


    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function pengajuanPerubahanPendidikan()
    {
        return $this->hasMany(PengajuanPerubahanPendidikans::class, 'id_pendidikan', 'id_pendidikan');
    }


    // gelar
    protected static $urutanJenjang = [
        'SD' => 1,
        'MI' => 1,
        'SMP' => 2,
        'MTS' => 2,
        'SMA' => 3,
        'MA' => 3,
        'SMK' => 3,
        'D1' => 4,
        'D2' => 5,
        'D3' => 6,
        'D4' => 7,
        'S1' => 8,
        'S2' => 9,
        'S3' => 10,
    ];




    public static function getGelarByUser($id_user)
    {
        return Cache::remember("gelar_user_{$id_user}", now()->addDays(7), function () use ($id_user) {

            $data = self::with('jenjang')
                ->where('id_user', $id_user)
                ->get();

            $urutan = self::$urutanJenjang;

            $sorted = $data->sortBy(function ($item) use ($urutan) {

                $urutanJenjang = $urutan[$item->jenjang->nama_jenjang] ?? 999;
                $tahun = $item->tahun_lulus ?? 0;

                // gabungkan jadi 1 angka
                return ($urutanJenjang * 10000) + $tahun;
            });

            $gelars = $sorted->pluck('gelar')
                ->filter()
                ->map(function ($g) {
                    return rtrim($g, '.') . '.';
                });

            return $gelars->implode(', ');
        });
    }


    protected static function booted()
    {
        static::saved(function ($model) {
            Cache::forget("gelar_user_{$model->id_user}");
        });

        static::deleted(function ($model) {
            Cache::forget("gelar_user_{$model->id_user}");
        });
    }
}
