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
        Schema::table('ketersediaan_pribadis', function (Blueprint $table) {
            $table->boolean('full_day')->default(false)->after('tanggal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ketersediaan_pribadis', function (Blueprint $table) {
            $table->dropColumn('full_day');
        });
    }
};
