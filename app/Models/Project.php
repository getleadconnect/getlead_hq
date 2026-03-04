<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $fillable = [
        'name', 'description', 'status',
        'project_lead', 'start_date', 'target_date', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'start_date'  => 'date',
            'target_date' => 'date',
        ];
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'project_lead');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'created_by');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}
