<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhoneStorePrice extends Model
{
    protected $fillable = [
        'phone_id',
        'store_id',
        'price',
        'product_url',
        'in_stock',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'in_stock' => 'boolean',
    ];

    public function phone()
    {
        return $this->belongsTo(Phone::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
