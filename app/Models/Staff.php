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

    /** The HR employee profile linked to this staff account (via hr_employees.staff_id). */
    public function employee()
    {
        return $this->hasOne(HrEmployee::class, 'staff_id');
    }
}
