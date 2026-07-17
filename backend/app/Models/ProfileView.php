<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfileView extends Model
{
    protected $fillable = [
        'profile_views',
        'master_id',
        'viewer_id',
        'city',
        'source',
        'viewed_at'
    ];


}
