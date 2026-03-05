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
        Schema::create('shipping_config', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['rate', 'setting'])->comment('rate = tarif per kg tier, setting = konfigurasi global');
            $table->string('key')->nullable()->comment('Untuk setting: key (tarif_per_km, volume_divisor). Untuk rate: null');
            
            // Untuk rate tier
            $table->decimal('min_weight_kg', 10, 2)->nullable()->comment('Berat minimum (kg) - hanya untuk type=rate');
            $table->decimal('max_weight_kg', 10, 2)->nullable()->comment('Berat maksimum (kg), null = unlimited - hanya untuk type=rate');
            $table->decimal('tarif_per_kg', 12, 2)->nullable()->comment('Tarif per kg - hanya untuk type=rate');
            $table->integer('order')->default(0)->comment('Urutan untuk sorting - hanya untuk type=rate');
            
            // Untuk setting
            $table->text('value')->nullable()->comment('Nilai setting - hanya untuk type=setting');
            $table->string('description')->nullable()->comment('Deskripsi');
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['type', 'is_active']);
            $table->index(['type', 'min_weight_kg', 'max_weight_kg']);
            $table->unique(['type', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_config');
    }
};
