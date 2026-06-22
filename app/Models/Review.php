<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'company_id',
        'yandex_author_id',
        'yandex_author_name',
        'text',
        'rating',
        'published_at',
        'review_hash',
    ];
}
