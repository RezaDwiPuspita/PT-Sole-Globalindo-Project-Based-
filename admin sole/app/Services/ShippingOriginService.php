<?php

namespace App\Services;

use App\Models\ShippingOrigin;

class ShippingOriginService
{
    public function getActiveOrigin(): ?ShippingOrigin
    {
        return ShippingOrigin::active()->first();
    }

    public function getOriginCoordinates(): array
    {
        $origin = $this->getActiveOrigin();
        if ($origin) {
            return [
                'lat' => (float)$origin->lat, 
                'lng' => (float)$origin->lng,
                'origin' => $origin, // Tambahkan origin object untuk relasi
            ];
        }

        return [
            'lat' => config('shipping.origin_lat', -6.5841000),
            'lng' => config('shipping.origin_lng', 110.6700000),
            'origin' => null,
        ];
    }
}



