<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_hasil_finalis', function (Blueprint $table) {
            $table->string('tingkat', 10)->default('X')->after('metode');
        });
    }

    public function down(): void
    {
        Schema::table('tb_hasil_finalis', function (Blueprint $table) {
            $table->dropColumn('tingkat');
        });
    }
};
