<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $fillable = [
        'name',
        'logo_url',
        'website_url',
    ];

    public function phonePrices()
    {
        return $this->hasMany(PhoneStorePrice::class);
    }
}
