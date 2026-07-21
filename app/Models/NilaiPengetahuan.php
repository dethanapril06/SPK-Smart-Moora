<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NilaiPengetahuan extends Model
{
    protected $table = 'tb_nilai_pengetahuan';
    protected $primaryKey = 'id_nilai_pengetahuan';

    protected $fillable = [
        'id_siswa',
        'id_mapel',
        'id_ta',
        'id_semester',
        'nilai',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa');
    }

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class, 'id_mapel');
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'id_ta');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'id_semester');
    }
}
