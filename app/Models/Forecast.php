<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Forecast extends Model
{
    protected $fillable = [
        'weather_id',
        'day',
        'condition',
        'high',
        'low',
        'icon',
    ];

    public function weather(): BelongsTo
    {
        return $this->belongsTo(Weather::class);
    }
}
