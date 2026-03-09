<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Staff extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'staff';

    protected $fillable = [
        'name',
        'mobile',
        'telegram_id',
        'pin',
        'role',
        'active',
    ];

    protected $hidden = [
        'pin',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'pin'    => 'hashed',
            'active' => 'boolean',
        ];
    }
}
