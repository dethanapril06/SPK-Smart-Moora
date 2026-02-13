<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kriteria extends Model
{
    protected $table = 'tb_kriteria';
    protected $primaryKey = 'id_kriteria';

    protected $fillable = [
        'kode_kriteria',
        'nama_kriteria',
        'jenis_kriteria',
        'bobot',
    ];

    // Relasi dengan SubKriteria
    public function subKriteria()
    {
        return $this->hasMany(SubKriteria::class, 'id_kriteria');
    }

    // Relasi dengan Penilaian
    public function penilaian()
    {
        return $this->hasMany(Penilaian::class, 'id_kriteria');
    }
}
