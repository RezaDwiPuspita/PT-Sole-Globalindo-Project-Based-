<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\City;
use App\Models\ShippingOrigin;
use App\Models\ShippingConfig;

class ShippingCost extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'item_summary' => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function shippingOrigin(): BelongsTo
    {
        return $this->belongsTo(ShippingOrigin::class, 'shipping_origin_id');
    }

    public function shippingConfigRate(): BelongsTo
    {
        return $this->belongsTo(ShippingConfig::class, 'shipping_config_rate_id');
    }

    public function scopeWithDetails($query)
    {
        return $query->select([
            'shipping_costs.*',
            'orders.total_amount',
        ]);
    }
}
