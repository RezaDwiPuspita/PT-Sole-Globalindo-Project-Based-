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
            // Simpan snapshot tarif yang digunakan saat perhitungan (untuk audit trail)
            $table->decimal('tarif_per_kg_used', 12, 2)->nullable()->after('volume_weight')->comment('Tarif per kg yang digunakan saat perhitungan');
            $table->decimal('tarif_per_km_used', 12, 2)->nullable()->after('tarif_per_kg_used')->comment('Tarif per km yang digunakan saat perhitungan');
            $table->decimal('volume_divisor_used', 10, 2)->nullable()->after('tarif_per_km_used')->comment('Volume divisor yang digunakan saat perhitungan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipping_costs', function (Blueprint $table) {
            $table->dropColumn(['tarif_per_kg_used', 'tarif_per_km_used', 'volume_divisor_used']);
        });
    }
};
