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
    protected $fillable = [
        'name',
        'email',
        'password',
        'level',
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

    // Relasi dengan Kelas (sebagai Wali Kelas)
    public function kelas()
    {
        return $this->hasOne(Kelas::class, 'id_wali_kelas');
    }

    // Helper methods untuk cek level user
    public function isAdmin()
    {
        return $this->level === 'Admin';
    }

    public function isWaliKelas()
    {
        return $this->level === 'Wali Kelas';
    }

    public function isKepalaSekolah()
    {
        return $this->level === 'Kepala Sekolah';
    }
}
