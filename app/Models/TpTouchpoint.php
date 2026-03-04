<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TpTouchpoint extends Model
{
    public $timestamps = false;

    protected $table = 'tp_touchpoints';

    protected $fillable = [
        'customer_id', 'stage', 'due_date', 'assigned_to',
        'status', 'outcome', 'outcome_notes', 'completed_at',
    ];

    protected $casts = [
        'due_date'     => 'date',
        'completed_at' => 'datetime',
        'created_at'   => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(TpCustomer::class, 'customer_id');
    }

    public function assignee()
    {
        return $this->belongsTo(Staff::class, 'assigned_to');
    }

    public function callLogs()
    {
        return $this->hasMany(TpCallLog::class, 'touchpoint_id');
    }
}
