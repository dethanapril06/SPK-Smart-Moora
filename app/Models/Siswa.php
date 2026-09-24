<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Siswa extends Model
{
    use HasFactory;
    protected $table = 'tb_siswa';
    protected $primaryKey = 'id_siswa';

    protected $fillable = [
        'nisn',
        'nama_siswa',
        'jenis_kelamin',
        'alamat',
        'id_kelas',
        'id_ta',
        'id_semester',
        'status',
        'tahun_lulus',
    ];

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeLulus($query)
    {
        return $query->where('status', 'lulus');
    }

    public function scopeForSemester($query, $idSemester)
    {
        if (!$idSemester) {
            return $query;
        }

        $semester = $idSemester instanceof Semester ? $idSemester : Semester::find($idSemester);
        if (!$semester) {
            return $query;
        }

        if ($semester->nama_semester === 'Ganjil') {
            return $query->where('tb_siswa.id_ta', $semester->id_ta)
                ->where('tb_siswa.id_semester', $semester->id_semester);
        }

        return $query->where('tb_siswa.id_ta', $semester->id_ta);
    }

    public function isSiswaBaruGenap(): bool
    {
        $sem = $this->relationLoaded('semester') ? $this->semester : $this->semester()->first();
        return $sem?->nama_semester === 'Genap';
    }

    // Relasi dengan Kelas
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas');
    }

    // Relasi dengan TahunAjaran
    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'id_ta');
    }

    // Relasi dengan Semester Masuk
    public function semester()
    {
        return $this->belongsTo(Semester::class, 'id_semester');
    }

    // Relasi dengan Penilaian
    public function penilaian()
    {
        return $this->hasMany(Penilaian::class, 'id_siswa');
    }

    // Relasi dengan RiwayatPelanggaran
    public function riwayatPelanggaran()
    {
        return $this->hasMany(RiwayatPelanggaran::class, 'id_siswa');
    }

    // Relasi dengan HasilAkhir
    public function hasilAkhir()
    {
        return $this->hasMany(HasilAkhir::class, 'id_siswa');
    }

    // Relasi dengan NilaiPengetahuan
    public function nilaiPengetahuan()
    {
        return $this->hasMany(NilaiPengetahuan::class, 'id_siswa');
    }

    // Relasi dengan NilaiKeterampilan
    public function nilaiKeterampilan()
    {
        return $this->hasMany(NilaiKeterampilan::class, 'id_siswa');
    }

    // Relasi dengan NilaiSikap
    public function nilaiSikap()
    {
        return $this->hasMany(NilaiSikap::class, 'id_siswa');
    }

    // Relasi dengan NilaiEkstrakurikuler
    public function nilaiEkstrakurikuler()
    {
        return $this->hasMany(NilaiEkstrakurikuler::class, 'id_siswa');
    }

    // Relasi dengan NilaiAbsensi
    public function nilaiAbsensi()
    {
        return $this->hasMany(NilaiAbsensi::class, 'id_siswa');
    }
}
