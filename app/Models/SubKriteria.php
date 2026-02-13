<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubKriteria extends Model
{
    protected $table = 'tb_subkriteria';
    protected $primaryKey = 'id_subkriteria';

    protected $fillable = [
        'id_kriteria',
        'nama_subkriteria',
        'nilai_awal',
        'nilai_akhir',
        'bobot_subkriteria',
    ];

    // Relasi dengan Kriteria
    public function kriteria()
    {
        return $this->belongsTo(Kriteria::class, 'id_kriteria');
    }
}
