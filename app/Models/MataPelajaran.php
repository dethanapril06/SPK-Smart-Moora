<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MataPelajaran extends Model
{
    protected $table = 'tb_mata_pelajaran';
    protected $primaryKey = 'id_mapel';

    protected $fillable = [
        'kode_mapel',
        'nama_mapel',
    ];

    public function nilaiPengetahuan()
    {
        return $this->hasMany(NilaiPengetahuan::class, 'id_mapel');
    }

    public function nilaiKeterampilan()
    {
        return $this->hasMany(NilaiKeterampilan::class, 'id_mapel');
    }
}
