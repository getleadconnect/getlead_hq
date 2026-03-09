<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetRepair extends Model
{
    protected $table = 'asset_repairs';

    protected $fillable = ['asset_id', 'date', 'issue', 'cost', 'vendor', 'status', 'notes'];

    protected $casts = [
        'date' => 'date',
        'cost' => 'float',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }
}
