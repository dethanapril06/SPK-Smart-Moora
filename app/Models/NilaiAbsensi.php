<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NilaiAbsensi extends Model
{
    protected $table = 'tb_nilai_absensi';
    protected $primaryKey = 'id_nilai_absensi';

    protected $fillable = [
        'id_siswa',
        'id_ta',
        'id_semester',
        'jumlah_sakit',
        'jumlah_izin',
        'jumlah_alpa',
    ];

    /**
     * Hitung total tidak hadir (sakit + izin + alpa)
     */
    public function getTotalTidakHadirAttribute()
    {
        return $this->jumlah_sakit + $this->jumlah_izin + $this->jumlah_alpa;
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa');
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
