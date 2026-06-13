<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavedComparison extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'phone_ids',
    ];

    protected $casts = [
        'phone_ids' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function phones()
    {
        return Phone::whereIn('id', $this->phone_ids ?? [])->get();
    }
}
