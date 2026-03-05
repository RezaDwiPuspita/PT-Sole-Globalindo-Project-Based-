<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Color extends Model
{
    use HasFactory;

    /**
     * Field yang boleh diisi mass assignment
     * (saat create/update pakai ::create() atau ->update()).
     */
    protected $fillable = [
        'name',   // contoh: "Natural Jati", "Walnut Brown", "Merah"
        'type',   // 'wood' (kayu) atau 'rattan' (rotan)
    ];

    /**
     * Relasi many-to-many ke Product lewat tabel pivot color_product.
     * Setiap relasi punya extra_price dan is_default di pivot.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)
            ->withPivot('extra_price', 'is_default')
            ->withTimestamps();
    }
}
