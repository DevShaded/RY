<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Weather extends Model
{
    protected $table = 'weathers';

    protected $fillable = [
        'name',
        'country',
        'temperature',
        'condition',
        'humidity',
        'wind_speed',
        'visibility',
        'feels_like',
        'icon',
    ];

    public function forecast(): HasMany
    {
        return $this->hasMany(Forecast::class, 'weather_id');
    }
}
