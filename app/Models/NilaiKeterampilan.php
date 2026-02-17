<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NilaiKeterampilan extends Model
{
    protected $table = 'tb_nilai_keterampilan';
    protected $primaryKey = 'id_nilai_keterampilan';

    protected $fillable = [
        'id_siswa',
        'id_mapel',
        'id_ta',
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
}
