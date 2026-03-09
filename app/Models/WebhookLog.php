<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookLog extends Model
{
    protected $table = 'webhook_log';

    protected $fillable = ['event', 'payload', 'result'];

    protected $casts = [
        'payload'    => 'array',
        'result'     => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
