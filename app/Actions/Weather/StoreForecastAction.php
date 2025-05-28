<?php

declare(strict_types=1);

namespace App\Actions\Weather;

use App\Models\Weather;

final class StoreForecastAction
{
    public static function handle(Weather $weather, array $forecastData): void
    {
        // Prepare forecast entries
        $forecasts = array_map(function ($day) {
            return [
                'day' => $day['day'],
                'condition' => $day['condition'],
                'high' => $day['high'],
                'low' => $day['low'],
                'icon' => $day['icon'],
            ];
        }, $forecastData);

        // Insert new forecasts
        $weather->forecast()->createMany($forecasts);
    }
}
