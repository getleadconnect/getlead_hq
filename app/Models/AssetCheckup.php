<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetCheckup extends Model
{
    protected $table = 'asset_checkups';

    protected $fillable = ['asset_id', 'checked_by', 'checkup_date', 'conditions', 'remarks'];

    protected $casts = [
        'checkup_date' => 'date',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function checker()
    {
        return $this->belongsTo(Staff::class, 'checked_by');
    }
}
