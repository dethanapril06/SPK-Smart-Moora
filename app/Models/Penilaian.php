<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penilaian extends Model
{
    protected $table = 'tb_penilaian';
    protected $primaryKey = 'id_penilaian';

    protected $fillable = [
        'id_siswa',
        'id_kriteria',
        'id_ta',
        'nilai_asli',
        'nilai_konversi',
    ];

    // Relasi dengan Siswa
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa');
    }

    // Relasi dengan Kriteria
    public function kriteria()
    {
        return $this->belongsTo(Kriteria::class, 'id_kriteria');
    }

    // Relasi dengan TahunAjaran
    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'id_ta');
    }
}
