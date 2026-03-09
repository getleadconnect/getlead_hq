<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QrCode extends Model
{
    protected $table = 'qr_codes';

    protected $fillable = ['qr_code', 'asset_id', 'mapped_at', 'mapped_by'];

    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function mapper()
    {
        return $this->belongsTo(Staff::class, 'mapped_by');
    }
}
