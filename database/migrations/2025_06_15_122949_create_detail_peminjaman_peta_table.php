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
        Schema::create('detail_peminjaman_peta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_peminjaman')->references('id')->on('peminjaman')->cascadeOnDelete();
            $table->foreignId('id_unit_peta')->references('id')->on('unit_peta')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_peminjaman_peta');
    }
};
