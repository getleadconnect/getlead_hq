<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TpCustomer extends Model
{
    protected $table = 'tp_customers';

    protected $fillable = [
        'name', 'company', 'phone', 'email',
        'subscription_type', 'start_date', 'expiry_date',
        'status', 'health', 'notes',
    ];

    protected $casts = [
        'start_date'  => 'date',
        'expiry_date' => 'date',
    ];

    public function touchpoints()
    {
        return $this->hasMany(TpTouchpoint::class, 'customer_id');
    }
}
