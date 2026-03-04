<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $table = 'assets';

    protected $fillable = [
        'asset_tag', 'name', 'type', 'brand', 'model', 'serial_number',
        'purchase_date', 'purchase_price', 'vendor',
        'assigned_to', 'status', 'warranty_expiry',
        'notes', 'remarks', 'checkup_interval',
        'last_checkup', 'next_checkup',
    ];

    protected $casts = [
        'purchase_date'    => 'date',
        'warranty_expiry'  => 'date',
        'last_checkup'     => 'date',
        'next_checkup'     => 'date',
        'purchase_price'   => 'float',
        'checkup_interval' => 'integer',
    ];

    public function assignee()
    {
        return $this->belongsTo(Staff::class, 'assigned_to');
    }

    public function assignments()
    {
        return $this->hasMany(AssetAssignment::class, 'asset_id');
    }

    public function repairs()
    {
        return $this->hasMany(AssetRepair::class, 'asset_id');
    }

    public function checkups()
    {
        return $this->hasMany(AssetCheckup::class, 'asset_id');
    }

    public function qrCode()
    {
        return $this->hasOne(QrCode::class, 'asset_id');
    }

    public static function nextTag(): string
    {
        $max = self::selectRaw("MAX(CAST(REPLACE(REPLACE(asset_tag,'AST-',''),'GLA','') AS UNSIGNED)) as mx")
            ->value('mx') ?? 0;
        return 'AST-' . str_pad($max + 1, 3, '0', STR_PAD_LEFT);
    }
}
