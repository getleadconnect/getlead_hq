<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table      = 'settings';
    protected $primaryKey = 'key';
    protected $keyType    = 'string';
    public    $incrementing = false;

    protected $fillable = ['key', 'value'];

    /**
     * Get all settings as a key→value map.
     */
    public static function getAll(): array
    {
        return static::pluck('value', 'key')->toArray();
    }

    /**
     * Get a single setting value with optional default.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return static::where('key', $key)->value('value') ?? $default;
    }

    /**
     * Set / upsert a setting.
     */
    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
