<?php

namespace Spatie\DiskMonitor\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiskMonitorEntry extends Model
{
    use HasFactory;

    public $guarded = [];

    public $casts = [
        'file_count' => 'integer',
    ];

    public static function last(): ?self
    {
        return static::orderByDesc('id')->first();
    }
}
