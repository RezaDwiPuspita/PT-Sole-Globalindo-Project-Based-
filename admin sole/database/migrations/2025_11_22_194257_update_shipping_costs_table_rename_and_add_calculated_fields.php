<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('shipping_costs', function (Blueprint $table) {
            // 1. Drop volume_weight (sudah ada total_volume_cm3 yang mewakili total volume)
            if (Schema::hasColumn('shipping_costs', 'volume_weight')) {
                $table->dropColumn('volume_weight');
            }

            // 2. Drop tarif_per_km_used (tidak perlu disimpan, diambil dari config saat runtime)
            if (Schema::hasColumn('shipping_costs', 'tarif_per_km_used')) {
                $table->dropColumn('tarif_per_km_used');
            }

            // 3. Tambah field biaya_jarak (calculated: distance × tarif_per_km)
            // Urutan: setelah distance_km langsung biaya_jarak (tidak ada tarif_per_km di tabel)
            if (!Schema::hasColumn('shipping_costs', 'biaya_jarak')) {
                $table->decimal('biaya_jarak', 12, 2)->default(0)->after('distance_km')->comment('Biaya jarak = distance × tarif_per_km (tarif_per_km diambil dari config)');
            }

            // 4. Drop volume_divisor_used dan tambahkan berat_volume (calculated: total_volume_cm3 / divisor)
            if (Schema::hasColumn('shipping_costs', 'volume_divisor_used')) {
                $table->dropColumn('volume_divisor_used');
            }
            
            if (!Schema::hasColumn('shipping_costs', 'berat_volume')) {
                $table->decimal('berat_volume', 12, 2)->default(0)->after('total_volume_cm3')->comment('Berat volume = total_volume_cm3 / divisor (calculated)');
            }

            // 5. Rename tarif_per_kg_used menjadi biaya_berat (calculated: berat_volume × tarif_per_kg)
            if (Schema::hasColumn('shipping_costs', 'tarif_per_kg_used')) {
                DB::statement("ALTER TABLE `shipping_costs` CHANGE `tarif_per_kg_used` `biaya_berat` DECIMAL(12,2) NULL COMMENT 'Biaya berat = berat_volume × tarif_per_kg (calculated)'");
            } else {
                // Jika kolom belum ada, tambahkan
                $table->decimal('biaya_berat', 12, 2)->default(0)->after('berat_volume')->comment('Biaya berat = berat_volume × tarif_per_kg (calculated)');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipping_costs', function (Blueprint $table) {
            // Restore kolom lama
            if (Schema::hasColumn('shipping_costs', 'biaya_jarak')) {
                $table->dropColumn('biaya_jarak');
            }

            // Restore tarif_per_km_used jika perlu
            if (!Schema::hasColumn('shipping_costs', 'tarif_per_km_used')) {
                $table->decimal('tarif_per_km_used', 12, 2)->nullable()->after('distance_km');
            }

            if (Schema::hasColumn('shipping_costs', 'berat_volume')) {
                $table->dropColumn('berat_volume');
            }

            if (Schema::hasColumn('shipping_costs', 'biaya_berat')) {
                DB::statement("ALTER TABLE `shipping_costs` CHANGE `biaya_berat` `tarif_per_kg_used` DECIMAL(12,2) NULL");
            }

            // Restore volume_weight jika perlu
            if (!Schema::hasColumn('shipping_costs', 'volume_weight')) {
                $table->decimal('volume_weight', 12, 2)->default(0)->after('distance_km');
            }

            // Restore volume_divisor_used jika perlu
            if (!Schema::hasColumn('shipping_costs', 'volume_divisor_used')) {
                $table->decimal('volume_divisor_used', 10, 2)->nullable()->after('biaya_berat');
            }
        });
    }
};
