<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone_id',
        'user_id',
        'comment',
        'rating'];

    // One review belongs to one phone
    public function phone()
    {
        return $this->belongsTo(Phone::class);
    }
}
