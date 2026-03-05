<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\ShippingConfig;

class UpdateShippingCostsData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shipping-costs:update-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update existing shipping_costs data: calculate berat_volume, biaya_jarak, and biaya_berat';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating shipping_costs data...');

        // Ambil config
        $divisor = ShippingConfig::getVolumeDivisor();
        $tarifPerKm = ShippingConfig::getTarifPerKm();

        $this->info("Volume Divisor: {$divisor}");
        $this->info("Tarif per Km: {$tarifPerKm}");

        // Update setiap record
        $shippingCosts = DB::table('shipping_costs')->get();
        $total = $shippingCosts->count();
        $updated = 0;

        $this->info("Found {$total} records to process...");

        foreach ($shippingCosts as $sc) {
            $updates = [];

            // Hitung berat_volume jika total_volume_cm3 ada
            if ($sc->total_volume_cm3 > 0 && $divisor > 0) {
                $beratVolume = $sc->total_volume_cm3 / $divisor;
                $updates['berat_volume'] = round($beratVolume, 2);

                $this->line("  ID {$sc->id}: total_volume_cm3 = {$sc->total_volume_cm3}, berat_volume = {$updates['berat_volume']}");

                // Hitung biaya_berat jika berat_volume sudah dihitung
                if ($beratVolume > 0) {
                    $rateConfig = ShippingConfig::getRateByWeight($beratVolume);
                    if ($rateConfig) {
                        $tarifPerKg = (float) $rateConfig->tarif_per_kg;
                        $biayaBerat = $beratVolume * $tarifPerKg;
                        $updates['biaya_berat'] = round($biayaBerat, 2);
                        $this->line("    Tarif per Kg: {$tarifPerKg}, biaya_berat = {$updates['biaya_berat']}");
                    } else {
                        $this->warn("    No rate config found for weight: {$beratVolume}");
                    }
                }
            } else {
                $this->warn("  ID {$sc->id}: total_volume_cm3 = {$sc->total_volume_cm3} (skipped)");
            }

            // Hitung biaya_jarak jika distance_km ada
            if ($sc->distance_km > 0 && $tarifPerKm > 0) {
                $biayaJarak = $sc->distance_km * $tarifPerKm;
                $updates['biaya_jarak'] = round($biayaJarak, 2);
                $this->line("    distance_km = {$sc->distance_km}, biaya_jarak = {$updates['biaya_jarak']}");
            } else {
                $this->warn("    distance_km = {$sc->distance_km} (skipped)");
            }

            // Update jika ada perubahan
            if (!empty($updates)) {
                DB::table('shipping_costs')
                    ->where('id', $sc->id)
                    ->update($updates);
                $updated++;
                $this->info("  ✓ Updated ID {$sc->id}");
            } else {
                $this->warn("  ✗ No updates for ID {$sc->id}");
            }
        }

        $this->info("\nCompleted! Updated {$updated} out of {$total} records.");
        return Command::SUCCESS;
    }
}
