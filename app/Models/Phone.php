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

    // One phone belongs to many favorites (via users)
    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    // One phone has many prices across different stores
    public function storePrices()
    {
        return $this->hasMany(PhoneStorePrice::class);
    }
}
