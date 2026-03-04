<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TpCallLog extends Model
{
    public $timestamps = false;

    protected $table = 'tp_call_logs';

    protected $fillable = [
        'touchpoint_id', 'called_by', 'outcome', 'notes', 'follow_up_date',
    ];

    protected $casts = [
        'call_time'      => 'datetime',
        'follow_up_date' => 'date',
    ];

    public function touchpoint()
    {
        return $this->belongsTo(TpTouchpoint::class, 'touchpoint_id');
    }

    public function caller()
    {
        return $this->belongsTo(Staff::class, 'called_by');
    }
}
