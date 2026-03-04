<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginHistory extends Model
{
    public $timestamps = false;

    protected $table = 'login_history';

    protected $fillable = [
        'staff_id',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}
