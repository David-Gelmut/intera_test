<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attachment extends Model
{
    protected $fillable = [
        'file_size',
        'driver',
        'file_type',
        'file_name',
        'file_path',
        'message_id',
    ];

    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }
}
