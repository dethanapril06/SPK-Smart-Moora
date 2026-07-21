<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'tb_nilai_pengetahuan',
            'tb_nilai_keterampilan',
            'tb_nilai_sikap',
            'tb_nilai_ekstrakurikuler',
            'tb_nilai_absensi',
            'tb_riwayat_pelanggaran',
            'tb_penilaian',
            'tb_hasil_akhir',
            'tb_hasil_finalis',
        ];

        // 1. Add nullable id_semester column first
        foreach ($tables as $tableName) {
            if (!Schema::hasColumn($tableName, 'id_semester')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->unsignedBigInteger('id_semester')->nullable()->after('id_ta');
                });
            }
        }

        // 2. Backfill existing data
        DB::transaction(function () use ($tables) {
            foreach ($tables as $tableName) {
                $distinctTas = DB::table($tableName)->select('id_ta')->distinct()->pluck('id_ta');
                foreach ($distinctTas as $idTa) {
                    if (!$idTa) continue;

                    $semester = DB::table('tb_semester')
                        ->where('id_ta', $idTa)
                        ->orderByDesc('is_active')
                        ->orderBy('id_semester')
                        ->first();

                    if ($semester) {
                        DB::table($tableName)
                            ->where('id_ta', $idTa)
                            ->whereNull('id_semester')
                            ->update(['id_semester' => $semester->id_semester]);
                    }
                }
            }
        });

        // Helper functions for safe schema checks
        $hasIndex = function ($table, $indexName) {
            $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
            return !empty($indexes);
        };

        $hasFk = function ($table, $fkName) {
            $fks = DB::select("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND CONSTRAINT_NAME = ?", [$table, $fkName]);
            return !empty($fks);
        };

        // 3. Add foreign key & update unique constraints safely
        Schema::table('tb_nilai_pengetahuan', function (Blueprint $table) use ($hasIndex, $hasFk) {
            if (!$hasFk('tb_nilai_pengetahuan', 'tb_nilai_pengetahuan_id_semester_foreign')) {
                $table->foreign('id_semester')->references('id_semester')->on('tb_semester')->cascadeOnDelete();
            }
            if (!$hasIndex('tb_nilai_pengetahuan', 'nilai_pen_siswa_mapel_sem_unique')) {
                $table->unique(['id_siswa', 'id_mapel', 'id_semester'], 'nilai_pen_siswa_mapel_sem_unique');
            }
            if ($hasIndex('tb_nilai_pengetahuan', 'nilai_pengetahuan_unique')) {
                $table->dropUnique('nilai_pengetahuan_unique');
            }
        });

        Schema::table('tb_nilai_keterampilan', function (Blueprint $table) use ($hasIndex, $hasFk) {
            if (!$hasFk('tb_nilai_keterampilan', 'tb_nilai_keterampilan_id_semester_foreign')) {
                $table->foreign('id_semester')->references('id_semester')->on('tb_semester')->cascadeOnDelete();
            }
            if (!$hasIndex('tb_nilai_keterampilan', 'nilai_ket_siswa_mapel_sem_unique')) {
                $table->unique(['id_siswa', 'id_mapel', 'id_semester'], 'nilai_ket_siswa_mapel_sem_unique');
            }
            if ($hasIndex('tb_nilai_keterampilan', 'nilai_keterampilan_unique')) {
                $table->dropUnique('nilai_keterampilan_unique');
            }
        });

        Schema::table('tb_nilai_sikap', function (Blueprint $table) use ($hasIndex, $hasFk) {
            if (!$hasFk('tb_nilai_sikap', 'tb_nilai_sikap_id_semester_foreign')) {
                $table->foreign('id_semester')->references('id_semester')->on('tb_semester')->cascadeOnDelete();
            }
            if (!$hasIndex('tb_nilai_sikap', 'nilai_sikap_siswa_sem_unique')) {
                $table->unique(['id_siswa', 'id_semester'], 'nilai_sikap_siswa_sem_unique');
            }
            if ($hasIndex('tb_nilai_sikap', 'nilai_sikap_unique')) {
                $table->dropUnique('nilai_sikap_unique');
            }
        });

        Schema::table('tb_nilai_ekstrakurikuler', function (Blueprint $table) use ($hasFk) {
            if (!$hasFk('tb_nilai_ekstrakurikuler', 'tb_nilai_ekstrakurikuler_id_semester_foreign')) {
                $table->foreign('id_semester')->references('id_semester')->on('tb_semester')->cascadeOnDelete();
            }
        });

        Schema::table('tb_nilai_absensi', function (Blueprint $table) use ($hasIndex, $hasFk) {
            if (!$hasFk('tb_nilai_absensi', 'tb_nilai_absensi_id_semester_foreign')) {
                $table->foreign('id_semester')->references('id_semester')->on('tb_semester')->cascadeOnDelete();
            }
            if (!$hasIndex('tb_nilai_absensi', 'nilai_absensi_siswa_sem_unique')) {
                $table->unique(['id_siswa', 'id_semester'], 'nilai_absensi_siswa_sem_unique');
            }
            if ($hasIndex('tb_nilai_absensi', 'nilai_absensi_unique')) {
                $table->dropUnique('nilai_absensi_unique');
            }
        });

        Schema::table('tb_riwayat_pelanggaran', function (Blueprint $table) use ($hasFk) {
            if (!$hasFk('tb_riwayat_pelanggaran', 'tb_riwayat_pelanggaran_id_semester_foreign')) {
                $table->foreign('id_semester')->references('id_semester')->on('tb_semester')->cascadeOnDelete();
            }
        });

        Schema::table('tb_penilaian', function (Blueprint $table) use ($hasIndex, $hasFk) {
            if (!$hasFk('tb_penilaian', 'tb_penilaian_id_semester_foreign')) {
                $table->foreign('id_semester')->references('id_semester')->on('tb_semester')->cascadeOnDelete();
            }
            if (!$hasIndex('tb_penilaian', 'penilaian_siswa_kriteria_semester_unique')) {
                $table->unique(['id_siswa', 'id_kriteria', 'id_semester'], 'penilaian_siswa_kriteria_semester_unique');
            }
        });

        Schema::table('tb_hasil_akhir', function (Blueprint $table) use ($hasIndex, $hasFk) {
            if (!$hasFk('tb_hasil_akhir', 'tb_hasil_akhir_id_semester_foreign')) {
                $table->foreign('id_semester')->references('id_semester')->on('tb_semester')->cascadeOnDelete();
            }
            if (!$hasIndex('tb_hasil_akhir', 'hasil_siswa_sem_user_unique')) {
                $table->unique(['id_siswa', 'id_semester', 'user_id'], 'hasil_siswa_sem_user_unique');
            }
            if ($hasIndex('tb_hasil_akhir', 'hasil_siswa_ta_user_unique')) {
                $table->dropUnique('hasil_siswa_ta_user_unique');
            }
        });

        Schema::table('tb_hasil_finalis', function (Blueprint $table) use ($hasIndex, $hasFk) {
            if (!$hasFk('tb_hasil_finalis', 'tb_hasil_finalis_id_semester_foreign')) {
                $table->foreign('id_semester')->references('id_semester')->on('tb_semester')->cascadeOnDelete();
            }
            if (!$hasIndex('tb_hasil_finalis', 'hasil_finalis_sem_unique')) {
                $table->unique(['id_siswa', 'id_semester', 'user_id', 'metode'], 'hasil_finalis_sem_unique');
            }
            if ($hasIndex('tb_hasil_finalis', 'hasil_finalis_unique')) {
                $table->dropUnique('hasil_finalis_unique');
            }
        });
    }

    public function down(): void
    {
        $hasIndex = function ($table, $indexName) {
            $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
            return !empty($indexes);
        };

        $hasFk = function ($table, $fkName) {
            $fks = DB::select("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND CONSTRAINT_NAME = ?", [$table, $fkName]);
            return !empty($fks);
        };

        Schema::table('tb_hasil_finalis', function (Blueprint $table) use ($hasIndex, $hasFk) {
            if (!$hasIndex('tb_hasil_finalis', 'hasil_finalis_unique')) {
                $table->unique(['id_siswa', 'id_ta', 'user_id', 'metode'], 'hasil_finalis_unique');
            }
            if ($hasIndex('tb_hasil_finalis', 'hasil_finalis_sem_unique')) {
                $table->dropUnique('hasil_finalis_sem_unique');
            }
            if ($hasFk('tb_hasil_finalis', 'tb_hasil_finalis_id_semester_foreign')) {
                $table->dropForeign(['id_semester']);
            }
            if (Schema::hasColumn('tb_hasil_finalis', 'id_semester')) {
                $table->dropColumn('id_semester');
            }
        });

        Schema::table('tb_hasil_akhir', function (Blueprint $table) use ($hasIndex, $hasFk) {
            if (!$hasIndex('tb_hasil_akhir', 'hasil_siswa_ta_user_unique')) {
                $table->unique(['id_siswa', 'id_ta', 'user_id'], 'hasil_siswa_ta_user_unique');
            }
            if ($hasIndex('tb_hasil_akhir', 'hasil_siswa_sem_user_unique')) {
                $table->dropUnique('hasil_siswa_sem_user_unique');
            }
            if ($hasFk('tb_hasil_akhir', 'tb_hasil_akhir_id_semester_foreign')) {
                $table->dropForeign(['id_semester']);
            }
            if (Schema::hasColumn('tb_hasil_akhir', 'id_semester')) {
                $table->dropColumn('id_semester');
            }
        });

        Schema::table('tb_penilaian', function (Blueprint $table) use ($hasIndex, $hasFk) {
            if ($hasIndex('tb_penilaian', 'penilaian_siswa_kriteria_semester_unique')) {
                $table->dropUnique('penilaian_siswa_kriteria_semester_unique');
            }
            if ($hasFk('tb_penilaian', 'tb_penilaian_id_semester_foreign')) {
                $table->dropForeign(['id_semester']);
            }
            if (Schema::hasColumn('tb_penilaian', 'id_semester')) {
                $table->dropColumn('id_semester');
            }
        });

        Schema::table('tb_riwayat_pelanggaran', function (Blueprint $table) use ($hasFk) {
            if ($hasFk('tb_riwayat_pelanggaran', 'tb_riwayat_pelanggaran_id_semester_foreign')) {
                $table->dropForeign(['id_semester']);
            }
            if (Schema::hasColumn('tb_riwayat_pelanggaran', 'id_semester')) {
                $table->dropColumn('id_semester');
            }
        });

        Schema::table('tb_nilai_absensi', function (Blueprint $table) use ($hasIndex, $hasFk) {
            if (!$hasIndex('tb_nilai_absensi', 'nilai_absensi_unique')) {
                $table->unique(['id_siswa', 'id_ta'], 'nilai_absensi_unique');
            }
            if ($hasIndex('tb_nilai_absensi', 'nilai_absensi_siswa_sem_unique')) {
                $table->dropUnique('nilai_absensi_siswa_sem_unique');
            }
            if ($hasFk('tb_nilai_absensi', 'tb_nilai_absensi_id_semester_foreign')) {
                $table->dropForeign(['id_semester']);
            }
            if (Schema::hasColumn('tb_nilai_absensi', 'id_semester')) {
                $table->dropColumn('id_semester');
            }
        });

        Schema::table('tb_nilai_ekstrakurikuler', function (Blueprint $table) use ($hasFk) {
            if ($hasFk('tb_nilai_ekstrakurikuler', 'tb_nilai_ekstrakurikuler_id_semester_foreign')) {
                $table->dropForeign(['id_semester']);
            }
            if (Schema::hasColumn('tb_nilai_ekstrakurikuler', 'id_semester')) {
                $table->dropColumn('id_semester');
            }
        });

        Schema::table('tb_nilai_sikap', function (Blueprint $table) use ($hasIndex, $hasFk) {
            if (!$hasIndex('tb_nilai_sikap', 'nilai_sikap_unique')) {
                $table->unique(['id_siswa', 'id_ta'], 'nilai_sikap_unique');
            }
            if ($hasIndex('tb_nilai_sikap', 'nilai_sikap_siswa_sem_unique')) {
                $table->dropUnique('nilai_sikap_siswa_sem_unique');
            }
            if ($hasFk('tb_nilai_sikap', 'tb_nilai_sikap_id_semester_foreign')) {
                $table->dropForeign(['id_semester']);
            }
            if (Schema::hasColumn('tb_nilai_sikap', 'id_semester')) {
                $table->dropColumn('id_semester');
            }
        });

        Schema::table('tb_nilai_keterampilan', function (Blueprint $table) use ($hasIndex, $hasFk) {
            if (!$hasIndex('tb_nilai_keterampilan', 'nilai_keterampilan_unique')) {
                $table->unique(['id_siswa', 'id_mapel', 'id_ta'], 'nilai_keterampilan_unique');
            }
            if ($hasIndex('tb_nilai_keterampilan', 'nilai_ket_siswa_mapel_sem_unique')) {
                $table->dropUnique('nilai_ket_siswa_mapel_sem_unique');
            }
            if ($hasFk('tb_nilai_keterampilan', 'tb_nilai_keterampilan_id_semester_foreign')) {
                $table->dropForeign(['id_semester']);
            }
            if (Schema::hasColumn('tb_nilai_keterampilan', 'id_semester')) {
                $table->dropColumn('id_semester');
            }
        });

        Schema::table('tb_nilai_pengetahuan', function (Blueprint $table) use ($hasIndex, $hasFk) {
            if (!$hasIndex('tb_nilai_pengetahuan', 'nilai_pengetahuan_unique')) {
                $table->unique(['id_siswa', 'id_mapel', 'id_ta'], 'nilai_pengetahuan_unique');
            }
            if ($hasIndex('tb_nilai_pengetahuan', 'nilai_pen_siswa_mapel_sem_unique')) {
                $table->dropUnique('nilai_pen_siswa_mapel_sem_unique');
            }
            if ($hasFk('tb_nilai_pengetahuan', 'tb_nilai_pengetahuan_id_semester_foreign')) {
                $table->dropForeign(['id_semester']);
            }
            if (Schema::hasColumn('tb_nilai_pengetahuan', 'id_semester')) {
                $table->dropColumn('id_semester');
            }
        });
    }
};
