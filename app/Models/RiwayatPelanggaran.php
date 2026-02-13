<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatPelanggaran extends Model
{
    protected $table = 'tb_riwayat_pelanggaran';
    protected $primaryKey = 'id_riwayat';

    protected $fillable = [
        'id_siswa',
        'id_jenis_pelanggaran',
        'id_ta',
        'tanggal_kejadian',
        'keterangan_tambahan',
    ];

    protected $casts = [
        'tanggal_kejadian' => 'date',
    ];

    // Relasi dengan Siswa
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa');
    }

    // Relasi dengan JenisPelanggaran
    public function jenisPelanggaran()
    {
        return $this->belongsTo(JenisPelanggaran::class, 'id_jenis_pelanggaran');
    }

    // Relasi dengan TahunAjaran
    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'id_ta');
    }
}
