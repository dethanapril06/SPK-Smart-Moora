<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TahunAjaran extends Model
{
    protected $table = 'tb_tahun_ajaran';
    protected $primaryKey = 'id_ta';

    protected $fillable = [
        'tahun_ajaran',
        'semester',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relasi dengan Siswa
    public function siswa()
    {
        return $this->hasMany(Siswa::class, 'id_ta');
    }

    // Relasi dengan Penilaian
    public function penilaian()
    {
        return $this->hasMany(Penilaian::class, 'id_ta');
    }

    // Relasi dengan HasilAkhir
    public function hasilAkhir()
    {
        return $this->hasMany(HasilAkhir::class, 'id_ta');
    }
}
