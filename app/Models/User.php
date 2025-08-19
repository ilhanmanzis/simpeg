<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $primaryKey = 'id_user';
    protected $fillable = [
        'npp',
        'email',
        'password',
        'status_keaktifan',
        'role'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function dataDiri()
    {
        return $this->hasOne(DataDiri::class, 'id_user');
    }

    public function pendidikan()
    {
        return $this->hasMany(Pendidikans::class, 'id_user', 'id_user');
    }

    public function pengajuanPerubahanData()
    {
        return $this->hasMany(PengajuanPerubahanDatas::class, 'id_user', 'id_user');
    }

    public function pengajuanPerubahanPendidikan()
    {
        return $this->hasMany(PengajuanPerubahanPendidikans::class, 'id_user', 'id_user');
    }

    // app/Models/User.php
    public function scopeSearchDosen($query, $keyword)
    {
        return $query->where('role', 'dosen') // filter role = dosen
            ->where(function ($q) use ($keyword) {
                $q->where('npp', 'like', "%{$keyword}%")
                    ->orWhereHas('dataDiri', function ($q2) use ($keyword) {
                        $q2->where('name', 'like', "%{$keyword}%");
                    });
            });
    }
    public function scopeSearchKaryawan($query, $keyword)
    {
        return $query->where('role', 'karyawan') // filter role = karyawan
            ->where(function ($q) use ($keyword) {
                $q->where('npp', 'like', "%{$keyword}%")
                    ->orWhereHas('dataDiri', function ($q2) use ($keyword) {
                        $q2->where('name', 'like', "%{$keyword}%");
                    });
            });
    }



    public function golongan()
    {
        return $this->hasMany(GolonganUsers::class, 'id_user', 'id_user');
    }
    public function fungsional()
    {
        return $this->hasMany(FungsionalUsers::class, 'id_user', 'id_user');
    }
    public function struktural()
    {
        return $this->hasMany(StrukturalUsers::class, 'id_user', 'id_user');
    }

    public function pengajuanGolongan()
    {
        return $this->hasMany(PengajuanGolongans::class, 'id_user', 'id_user');
    }

    public function pengajuanFungsional()
    {
        return $this->hasMany(PengajuanFungsionals::class, 'id_user', 'id_user');
    }
}
