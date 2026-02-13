<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tb_hasil_akhir', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('id_ta');
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();

            // Drop old unique if exists, add new composite unique
            $table->unique(['id_siswa', 'id_ta', 'user_id'], 'hasil_siswa_ta_user_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_hasil_akhir', function (Blueprint $table) {
            $table->dropUnique('hasil_siswa_ta_user_unique');
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
