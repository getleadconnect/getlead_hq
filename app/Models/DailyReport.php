<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyReport extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'staff_id',
        'report_date',
        'report_data',
        'submitted_at',
        'updated_at',
    ];

    protected $casts = [
        'report_date'  => 'date',
        'report_data'  => 'array',
        'submitted_at' => 'datetime',
        'updated_at'   => 'datetime',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}
