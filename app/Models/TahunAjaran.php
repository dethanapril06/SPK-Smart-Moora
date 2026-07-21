<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TahunAjaran extends Model
{
    protected $table = 'tb_tahun_ajaran';
    protected $primaryKey = 'id_ta';

    protected $fillable = [
        'tahun_ajaran',
        'semester',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getSemesterAttribute($value)
    {
        $activeSemester = $this->relationLoaded('activeSemester')
            ? $this->getRelationValue('activeSemester')
            : $this->activeSemester()->first();

        return $activeSemester?->nama_semester ?? $value;
    }

    // Relasi dengan Siswa
    public function siswa()
    {
        return $this->hasMany(Siswa::class, 'id_ta');
    }

    // Relasi dengan Penilaian
    public function penilaian()
    {
        return $this->hasMany(Penilaian::class, 'id_ta');
    }

    // Relasi dengan HasilAkhir
    public function hasilAkhir()
    {
        return $this->hasMany(HasilAkhir::class, 'id_ta');
    }

    public function semesters()
    {
        return $this->hasMany(Semester::class, 'id_ta');
    }

    public function activeSemester()
    {
        return $this->hasOne(Semester::class, 'id_ta')->where('is_active', true);
    }

    public function ensureDefaultSemesters(bool $active = false): void
    {
        foreach (['Ganjil', 'Genap'] as $semesterName) {
            $semester = $this->semesters()->firstOrCreate(
                ['nama_semester' => $semesterName],
                ['is_active' => false]
            );

            $semester->update([
                'is_active' => $active && $semesterName === 'Ganjil',
            ]);
        }
    }

    public function activateSemester(string $semesterName): void
    {
        $this->semesters()->update(['is_active' => false]);

        $this->semesters()->updateOrCreate(
            ['nama_semester' => $semesterName],
            ['is_active' => true]
        );
    }

    public function scopeRepresentatives($query)
    {
        $representativeIds = self::query()
            ->selectRaw('MIN(id_ta) as id_ta')
            ->groupBy('tahun_ajaran')
            ->pluck('id_ta');

        return $query->whereIn('id_ta', $representativeIds);
    }
}
