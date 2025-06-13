<?php

namespace Fixit\Models;

use Illuminate\Database\Eloquent\Model;

class FixitError extends Model
{
    protected $table = 'fixit_errors';

    protected $fillable = [
        'url', 'request', 'response', 'ip', 'status',
        'exception', 'file', 'line', 'trace'
    ];

    protected $casts = [
        'request' => 'array',
        'response' => 'array',
    ];
}

