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
        Schema::create('detail_logistiks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_logistik_id')->constrained('pengajuan_logistiks')->onDelete('cascade')->onUpdate('cascade');
            $table->string('item');
            $table->integer('jumlah');
            $table->string('satuan');
            $table->text('keterangan')->nullable();            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_logistiks');
    }
};
