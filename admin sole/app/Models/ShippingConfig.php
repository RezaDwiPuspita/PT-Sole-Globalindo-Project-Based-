<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingConfig extends Model
{
    protected $table = 'shipping_config'; // Tabel singular, bukan plural

    protected $fillable = [
        'type',
        'key',
        'min_weight_kg',
        'max_weight_kg',
        'tarif_per_kg',
        'order',
        'value',
        'description',
        'is_active',
    ];

    protected $casts = [
        'min_weight_kg' => 'decimal:2',
        'max_weight_kg' => 'decimal:2',
        'tarif_per_kg' => 'decimal:2',
        'order' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Scope untuk rate tier aktif
     */
    public function scopeActiveRates($query)
    {
        return $query->where('type', 'rate')
            ->where('is_active', true)
            ->orderBy('order');
    }

    /**
     * Scope untuk setting aktif
     */
    public function scopeActiveSettings($query)
    {
        return $query->where('type', 'setting')
            ->where('is_active', true);
    }

    /**
     * Cari tarif per kg berdasarkan berat volumetrik
     * @return float Tarif per kg
     */
    public static function getTarifPerKg(float $volumeWeightKg): float
    {
        $rate = static::getRateByWeight($volumeWeightKg);
        return $rate ? (float) $rate->tarif_per_kg : 0;
    }

    /**
     * Cari rate object berdasarkan berat volumetrik (untuk mendapatkan ID)
     * @return ShippingConfig|null Rate object atau null
     */
    public static function getRateByWeight(float $volumeWeightKg): ?self
    {
        return static::activeRates()
            ->where('min_weight_kg', '<=', $volumeWeightKg)
            ->where(function ($query) use ($volumeWeightKg) {
                $query->whereNull('max_weight_kg')
                    ->orWhere('max_weight_kg', '>=', $volumeWeightKg);
            })
            ->orderBy('order', 'desc')
            ->first();
    }

    /**
     * Relasi: ShippingConfig → ShippingCost (one-to-many)
     * Satu rate tier bisa digunakan oleh banyak shipping costs
     */
    public function shippingCosts()
    {
        return $this->hasMany(ShippingCost::class, 'shipping_config_rate_id');
    }

    /**
     * Ambil nilai setting berdasarkan key
     */
    public static function getSettingValue(string $key, $default = null)
    {
        $setting = static::activeSettings()
            ->where('key', $key)
            ->first();
        
        return $setting ? $setting->value : $default;
    }

    /**
     * Ambil tarif per km dari database
     */
    public static function getTarifPerKm(): float
    {
        $value = static::getSettingValue('tarif_per_km', config('shipping.tarif_per_km', 2500));
        return (float) $value;
    }

    /**
     * Ambil volume divisor dari database
     */
    public static function getVolumeDivisor(): float
    {
        $value = static::getSettingValue('volume_divisor', config('shipping.volume_divisor', 6000));
        return (float) $value;
    }
}
