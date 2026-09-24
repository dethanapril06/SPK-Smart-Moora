<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    protected $table = 'tb_semester';
    protected $primaryKey = 'id_semester';

    protected $fillable = [
        'id_ta',
        'nama_semester',
        'periode_bulan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getPeriodeBulanAttribute($value)
    {
        if ($value) {
            return $value;
        }

        return $this->nama_semester === 'Ganjil' ? 'Juli - Desember' : 'Januari - Juni';
    }

    public function getNamaLengkapAttribute(): string
    {
        return "{$this->nama_semester} ({$this->periode_bulan})";
    }

    public function siswa()
    {
        return $this->hasMany(Siswa::class, 'id_semester');
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'id_ta');
    }

    public function penilaian()
    {
        return $this->hasMany(Penilaian::class, 'id_semester');
    }

    public function hasilAkhir()
    {
        return $this->hasMany(HasilAkhir::class, 'id_semester');
    }

    public function hasilFinalis()
    {
        return $this->hasMany(HasilFinalis::class, 'id_semester');
    }

    public function nilaiPengetahuan()
    {
        return $this->hasMany(NilaiPengetahuan::class, 'id_semester');
    }

    public function nilaiKeterampilan()
    {
        return $this->hasMany(NilaiKeterampilan::class, 'id_semester');
    }

    public function nilaiSikap()
    {
        return $this->hasMany(NilaiSikap::class, 'id_semester');
    }

    public function nilaiEkstrakurikuler()
    {
        return $this->hasMany(NilaiEkstrakurikuler::class, 'id_semester');
    }

    public function nilaiAbsensi()
    {
        return $this->hasMany(NilaiAbsensi::class, 'id_semester');
    }

    public function riwayatPelanggaran()
    {
        return $this->hasMany(RiwayatPelanggaran::class, 'id_semester');
    }
}