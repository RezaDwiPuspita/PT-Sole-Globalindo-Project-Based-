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
        // Hapus tabel lama setelah data dipindah ke shipping_config
        Schema::dropIfExists('shipping_settings');
        Schema::dropIfExists('shipping_rates');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate tables (untuk rollback, tapi data tidak bisa dikembalikan)
        Schema::create('shipping_rates', function (Blueprint $table) {
            $table->id();
            $table->decimal('min_weight_kg', 10, 2)->default(0);
            $table->decimal('max_weight_kg', 10, 2)->nullable();
            $table->decimal('tarif_per_kg', 12, 2);
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('shipping_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value');
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }
};
