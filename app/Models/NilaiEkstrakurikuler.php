<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NilaiEkstrakurikuler extends Model
{
    protected $table = 'tb_nilai_ekstrakurikuler';
    protected $primaryKey = 'id_nilai_ekskul';

    protected $fillable = [
        'id_siswa',
        'id_ta',
        'id_semester',
        'nama_ekskul',
        'predikat',
    ];

    /**
     * Konversi predikat ke nilai angka untuk agregasi
     */
    public static function konversiPredikat($predikat)
    {
        return match ($predikat) {
            'Sangat Baik' => 95,
            'Baik' => 85,
            'Cukup' => 75,
            'Kurang' => 60,
            default => 0,
        };
    }

    /**
     * Get nilai konversi dari predikat
     */
    public function getNilaiKonversiAttribute()
    {
        return self::konversiPredikat($this->predikat);
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
