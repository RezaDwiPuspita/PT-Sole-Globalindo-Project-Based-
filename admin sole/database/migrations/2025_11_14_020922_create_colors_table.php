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
        Schema::create('colors', function (Blueprint $table) {
            $table->id();

            // Nama warna, misal: "Natural Jati", "Walnut Brown", "Merah"
            $table->string('name');

            // Jenis warna: wood = warna kayu, rattan = warna rotan
            // (kalau mau pakai 'kayu' dan 'rotan' juga boleh, asal konsisten di model & form)
            $table->enum('type', ['wood', 'rattan']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('colors');
    }
};
