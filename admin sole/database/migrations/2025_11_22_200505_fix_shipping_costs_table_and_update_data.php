<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\ShippingConfig;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('shipping_costs', function (Blueprint $table) {
            // Drop kolom tarif_per_km jika ada (seharusnya tidak ada di tabel)
            if (Schema::hasColumn('shipping_costs', 'tarif_per_km')) {
                $table->dropColumn('tarif_per_km');
            }
        });

        // Update data yang sudah ada
        $this->updateExistingData();
    }

    /**
     * Update data yang sudah ada untuk menghitung berat_volume, biaya_jarak, dan biaya_berat
     */
    private function updateExistingData(): void
    {
        // Ambil config
        $divisor = ShippingConfig::getVolumeDivisor();
        $tarifPerKm = ShippingConfig::getTarifPerKm();

        // Update setiap record
        $shippingCosts = DB::table('shipping_costs')->get();

        foreach ($shippingCosts as $sc) {
            $updates = [];

            // Hitung berat_volume jika total_volume_cm3 ada
            if ($sc->total_volume_cm3 > 0 && $divisor > 0) {
                $beratVolume = $sc->total_volume_cm3 / $divisor;
                $updates['berat_volume'] = round($beratVolume, 2);

                // Hitung biaya_berat jika berat_volume sudah dihitung
                if ($beratVolume > 0) {
                    $rateConfig = ShippingConfig::getRateByWeight($beratVolume);
                    if ($rateConfig) {
                        $tarifPerKg = (float) $rateConfig->tarif_per_kg;
                        $biayaBerat = $beratVolume * $tarifPerKg;
                        $updates['biaya_berat'] = round($biayaBerat, 2);
                    }
                }
            }

            // Hitung biaya_jarak jika distance_km ada
            if ($sc->distance_km > 0 && $tarifPerKm > 0) {
                $biayaJarak = $sc->distance_km * $tarifPerKm;
                $updates['biaya_jarak'] = round($biayaJarak, 2);
            }

            // Update jika ada perubahan
            if (!empty($updates)) {
                DB::table('shipping_costs')
                    ->where('id', $sc->id)
                    ->update($updates);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak perlu restore karena ini adalah fix migration
        // Jika perlu rollback, bisa tambahkan kolom tarif_per_km kembali
        Schema::table('shipping_costs', function (Blueprint $table) {
            if (!Schema::hasColumn('shipping_costs', 'tarif_per_km')) {
                $table->decimal('tarif_per_km', 12, 2)->nullable()->after('distance_km');
            }
        });
    }
};
