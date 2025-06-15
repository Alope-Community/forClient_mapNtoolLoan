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
        Schema::create('unit_peta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_peta');
            $table->enum('kondisi', ['baik', 'rusak']);
            $table->string('lokasi');
            $table->boolean('is_dipinjam')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_peta');
    }
};
