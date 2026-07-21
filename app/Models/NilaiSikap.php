<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NilaiSikap extends Model
{
    protected $table = 'tb_nilai_sikap';
    protected $primaryKey = 'id_nilai_sikap';

    protected $fillable = [
        'id_siswa',
        'id_ta',
        'id_semester',
        'sikap_spiritual',
        'sikap_sosial',
    ];

    /**
     * Konversi predikat ke nilai angka untuk agregasi
     */
    public static function konversiPredikat($predikat)
    {
        return match ($predikat) {
            'Sangat Baik' => 95,
            'Baik' => 80,
            'Cukup' => 67,
            'Kurang' => 50,
            default => 0,
        };
    }

    public function getNilaiSpiritualAttribute()
    {
        return self::konversiPredikat($this->sikap_spiritual);
    }

    public function getNilaiSosialAttribute()
    {
        return self::konversiPredikat($this->sikap_sosial);
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
