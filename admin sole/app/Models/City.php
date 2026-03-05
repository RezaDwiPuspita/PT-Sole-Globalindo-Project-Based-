<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $table = 'cities';

    protected $fillable = [
        'kabupaten',
        'province',
        'lat',
        'lng',
    ];

    public function getNameAttribute(): ?string
    {
        return $this->kabupaten;
    }

    public function setNameAttribute(string $value): void
    {
        $this->attributes['kabupaten'] = $value;
    }
}



