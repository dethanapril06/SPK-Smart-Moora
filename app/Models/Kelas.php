<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $table = 'tb_kelas';
    protected $primaryKey = 'id_kelas';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_kelas',
        'nama_kelas',
        'id_wali_kelas',
        'kapasitas',
    ];

    // Relasi dengan User (Wali Kelas)
    public function waliKelas()
    {
        return $this->belongsTo(User::class, 'id_wali_kelas');
    }

    // Relasi dengan Siswa
    public function siswa()
    {
        return $this->hasMany(Siswa::class, 'id_kelas');
    }

    // Relasi Many-to-Many dengan Mata Pelajaran
    public function mataPelajaran()
    {
        return $this->belongsToMany(MataPelajaran::class, 'tb_kelas_mata_pelajaran', 'id_kelas', 'id_mapel')
                    ->withTimestamps();
    }

    /**
     * Get Tingkat / Angkatan (X, XI, XII) from nama_kelas
     */
    public function getTingkatAttribute(): ?string
    {
        return self::resolveTingkat($this->nama_kelas);
    }

    /**
     * Resolve Tingkat from string class name
     */
    public static function resolveTingkat(?string $namaKelas): ?string
    {
        if (!$namaKelas) {
            return null;
        }

        $normalized = strtoupper($namaKelas);

        if (preg_match('/(^|[^A-Z0-9])XII([^A-Z0-9]|$)/', $normalized)) {
            return 'XII';
        }

        if (preg_match('/(^|[^A-Z0-9])XI([^A-Z0-9]|$)/', $normalized)) {
            return 'XI';
        }

        if (preg_match('/(^|[^A-Z0-9])X([^A-Z0-9]|$)/', $normalized)) {
            return 'X';
        }

        return null;
    }
}
