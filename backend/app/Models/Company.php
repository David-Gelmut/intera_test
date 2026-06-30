<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    protected $fillable = [
        'yandex_id',
        'yandex_url',
        'name',
        'rating',
        'reviews_count',
        'ratings_count',
        'error_message',
    ];

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}
