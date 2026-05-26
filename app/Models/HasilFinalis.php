<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HasilFinalis extends Model
{
    protected $table = 'tb_hasil_finalis';
    protected $primaryKey = 'id_hasil_finalis';

    protected $fillable = [
        'id_siswa',
        'id_ta',
        'user_id',
        'metode',
        'tingkat',
        'skor',
        'rank',
        'source_rank',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa');
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'id_ta');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
