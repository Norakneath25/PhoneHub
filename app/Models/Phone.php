<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Phone extends Model
{
    use HasFactory;

    // tells Laravel which columns are allowed to be filled with data
    protected $fillable = [
        'brand',
        'model',
        'price',
        'image',
        'store_url',
        'ram',
        'storage',
        'camera',
        'battery',
        'screen_size',
        'os',
        'release_date',
        'rating',
    ];
    // One phone has many reviews
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
