<?php

namespace Fixit\Models;

use Illuminate\Database\Eloquent\Model;

class FixitError extends Model
{
    protected $table = 'fixit_errors';

    protected $fillable = [
        'url', 'request', 'response', 'ip', 'status'
    ];

    protected $casts = [
        'request' => 'array',
        'response' => 'array',
    ];
}

