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
}
