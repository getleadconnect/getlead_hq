<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'business_id',
        'name',
        'email',
        'country_code',
        'mobile',
        'created_by',
    ];
}
