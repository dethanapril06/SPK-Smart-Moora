<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HasilAkhir extends Model
{
    protected $table = 'tb_hasil_akhir';
    protected $primaryKey = 'id_hasil';

    protected $fillable = [
        'id_siswa',
        'id_ta',
        'user_id',
        'skor_smart',
        'rank_smart',
        'skor_moora',
        'rank_moora',
    ];


    // Relasi dengan Siswa
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa');
    }

    // Relasi dengan TahunAjaran
    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'id_ta');
    }

    // Relasi dengan User (pemilik perhitungan)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
