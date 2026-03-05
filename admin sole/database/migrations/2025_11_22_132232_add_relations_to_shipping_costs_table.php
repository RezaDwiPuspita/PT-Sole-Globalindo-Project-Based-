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
        Schema::table('shipping_costs', function (Blueprint $table) {
            // Relasi ke shipping_origin (origin yang digunakan saat perhitungan)
            $table->foreignId('shipping_origin_id')
                ->nullable()
                ->after('city_id')
                ->constrained('shipping_origins')
                ->nullOnDelete()
                ->comment('Origin gudang yang digunakan saat perhitungan');
            
            // Relasi ke shipping_config (rate tier yang digunakan)
            $table->foreignId('shipping_config_rate_id')
                ->nullable()
                ->after('shipping_origin_id')
                ->constrained('shipping_config')
                ->nullOnDelete()
                ->comment('Rate tier yang digunakan saat perhitungan (type=rate)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipping_costs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('shipping_origin_id');
            $table->dropConstrainedForeignId('shipping_config_rate_id');
        });
    }
};
