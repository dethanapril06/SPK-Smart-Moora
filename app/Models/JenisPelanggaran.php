<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisPelanggaran extends Model
{
    protected $table = 'tb_jenis_pelanggaran';
    protected $primaryKey = 'id_jenis_pelanggaran';

    protected $fillable = [
        'kategori_pelanggaran',
        'nama_pelanggaran',
        'bobot_poin',
    ];

    // Relasi dengan RiwayatPelanggaran
    public function riwayatPelanggaran()
    {
        return $this->hasMany(RiwayatPelanggaran::class, 'id_jenis_pelanggaran');
    }
}
