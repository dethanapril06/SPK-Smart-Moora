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

    /**
     * Hitung rata-rata nilai sikap (spiritual + sosial)
     */
    public function getNilaiRataRataAttribute()
    {
        $spiritual = self::konversiPredikat($this->sikap_spiritual);
        $sosial = self::konversiPredikat($this->sikap_sosial);
        return ($spiritual + $sosial) / 2;
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa');
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'id_ta');
    }
}
