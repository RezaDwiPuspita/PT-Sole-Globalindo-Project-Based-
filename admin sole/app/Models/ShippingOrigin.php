<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingOrigin extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'lat', 'lng', 'is_active'];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Relasi: ShippingOrigin → ShippingCost (one-to-many)
     * Satu origin bisa digunakan oleh banyak shipping costs
     */
    public function shippingCosts()
    {
        return $this->hasMany(ShippingCost::class, 'shipping_origin_id');
    }
}
