<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrJobOpening extends Model
{
    protected $guarded = [];

    protected $primaryKey = 'id';
    protected $table = 'hr_job_openings';

    protected $casts = [
        'job_closing_date' => 'datetime',
    ];

    // Status constants
    const ACTIVE = 1;
    const INACTIVE = 0;


    // Scope for active job openings
    public function scopeActive($query)
    {
        return $query->where('status', self::ACTIVE);
    }

    // Check if job opening is active
    public function isActive()
    {
        return $this->status == self::ACTIVE;
    }

    // Relationships
    public function jobCategory()
    {
        return $this->belongsTo(HrJobCategory::class, 'job_category_id');
    }

    public function designation()
    {
        return $this->belongsTo(HrDesignation::class, 'job_designation_id');
    }

}
