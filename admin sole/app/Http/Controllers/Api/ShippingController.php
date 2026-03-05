<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\ShippingConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Services\ShippingOriginService;

class ShippingController extends Controller
{
    public function __construct(protected ShippingOriginService $originService)
    {
    }

    public function quote(Request $request)
    {
        // ===================== VALIDASI INPUT =====================
        $data = $request->validate([
            'destination.province'    => 'required_without:destination.city_id|string',
            'destination.city'        => 'required_without:destination.city_id|string',
            'destination.city_id'     => 'nullable|exists:cities,id',
            'destination.district'    => 'nullable|string',
            'destination.postal_code' => 'nullable|string',
            'items'                   => 'required|array|min:1',
            'items.*.length_cm'       => 'required|numeric|min:1',
            'items.*.width_cm'        => 'required|numeric|min:1',
            'items.*.height_cm'       => 'required|numeric|min:1',
            'items.*.qty'             => 'required|integer|min:1',
            'volume_divisor'          => 'nullable|numeric|min:1',
            'prefer'                  => 'nullable|in:cheapest,fastest',
        ]);

        $province = $data['destination']['province'] ?? null;
        $cityName = $data['destination']['city'] ?? null;
        $cityId = $data['destination']['city_id'] ?? null;

        // ===================== CARI KOTA / KABUPATEN =====================
        $city = null;
        if ($cityId) {
            $city = City::find($cityId);
        }

        if (!$city && $province && $cityName) {
            $city = $this->resolveCity($province, $cityName);
        }

        if (!$city) {
            return response()->json([
                'message' => 'Kota/Kabupaten tidak dikenali, mohon cek penulisan.'
            ], 422);
        }

        // ===================== KOORDINAT ASAL (GUDANG JEPARA) =====================
        ['lat' => $originLat, 'lng' => $originLng] = $this->originService->getOriginCoordinates();

        if ($originLat === null || $originLng === null) {
            return response()->json([
                'message' => 'Konfigurasi origin (shipping.origin_lat/lng) belum diset.'
            ], 500);
        }

        // ===================== VALIDASI KOORDINAT KOTA =====================
        if (!$city || $city->lat === null || $city->lng === null) {
            return response()->json([
                'message' => 'Koordinat kota tujuan tidak tersedia. Silakan coba lagi atau hubungi admin.'
            ], 422);
        }

        // ===================== HITUNG JARAK (KM)  =====================
        $distanceKm = $this->haversine(
            (float) $originLat,
            (float) $originLng,
            (float) $city->lat,
            (float) $city->lng
        );

        // ===================== HITUNG VOLUME & BERAT VOLUMETRIK =====================
        // Ambil volume divisor dari database, fallback ke config
        $divisor = $data['volume_divisor'] ?? ShippingConfig::getVolumeDivisor();
        if ($divisor <= 0) {
            // Fallback ke config jika database belum diisi
            $divisor = config('shipping.volume_divisor', 6000);
        }

        $totalVolumeCm3 = 0;
        foreach ($data['items'] as $item) {
            // volume per item: P x L x T x qty (cm³)
            $totalVolumeCm3 +=
                $item['length_cm'] *
                $item['width_cm'] *
                $item['height_cm'] *
                $item['qty'];
        }

        // Berat volume dalam kg: (total_volume / divisor)
        $beratVolume = $totalVolumeCm3 / $divisor;

        // ===================== TARIF PER KG (TIER) =====================
        // Ambil tarif per kg dari database berdasarkan tier berat
        $tarifPerKg = ShippingConfig::getTarifPerKg($beratVolume);

        // ===================== TARIF PER KM =====================
        // Ambil tarif per km dari database, fallback ke config
        $tarifPerKm = ShippingConfig::getTarifPerKm();

        if ($tarifPerKm <= 0 || $tarifPerKg <= 0) {
            return response()->json([
                'message' => 'Konfigurasi tarif shipping belum diset dengan benar.'
            ], 500);
        }

        // ===================== HITUNG BIAYA =====================
        $biayaBerat = $beratVolume * $tarifPerKg;
        $biayaJarak = max(0, $distanceKm) * $tarifPerKm;

        // TOTAL = biaya berat + biaya jarak
        $totalShipping = round($biayaBerat + $biayaJarak);

        // ===================== RESPON JSON =====================
        return response()->json([
            'courier'        => 'Internal',
            'service'        => 'Regular',
            'etd_days'       => 7,
            'price'          => $totalShipping,
            'distance_km'    => round($distanceKm, 1),
            'total_volume'   => round($totalVolumeCm3, 2), // rename dari volume_weight
            'berat_volume'   => round($beratVolume, 2), // berat volume (calculated)
            'tarif_per_km'   => $tarifPerKm, // tarif per km dari config
            'biaya_jarak'    => round($biayaJarak, 2), // biaya jarak (calculated)
            'biaya_berat'    => round($biayaBerat, 2), // biaya berat (calculated)
            'city_id'        => $city->id, // PERBAIKAN: Kirim city_id untuk digunakan saat checkout
        ]);
    }

    protected function resolveCity(string $province, string $cityName): ?City
    {
        $city = City::where('province', $province)
            ->where('kabupaten', 'LIKE', '%' . $cityName . '%')
            ->first();

        if ($city) {
            return $city;
        }

        $query = trim($cityName . ', ' . $province . ', Indonesia');

        $baseUrl = config('shipping.geocoding.base_url', 'https://nominatim.openstreetmap.org/search');

        try {
            $response = Http::withHeaders([
                'User-Agent' => config('app.name', 'Laravel') . ' Shipping Geocoder',
            ])->get($baseUrl, [
                'q'              => $query,
                'format'         => 'json',
                'addressdetails' => 1,
                'limit'          => 1,
                'countrycodes'   => 'id',
            ]);
        } catch (\Throwable $e) {
            report($e);
            return null;
        }

        if (!$response->ok()) {
            return null;
        }

        $results = $response->json();

        if (!is_array($results) || count($results) === 0) {
            return null;
        }

        $first = $results[0];

        if (empty($first['lat']) || empty($first['lon'])) {
            return null;
        }

        $lat = (float) $first['lat'];
        $lng = (float) $first['lon'];

        if ($lat < -11 || $lat > 6 || $lng < 95 || $lng > 141) {
            return null;
        }

        return City::create([
            'kabupaten' => $cityName,
            'province'  => $province,
            'lat'       => $lat,
            'lng'       => $lng,
        ]);
    }

    protected function haversine($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) ** 2 +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
