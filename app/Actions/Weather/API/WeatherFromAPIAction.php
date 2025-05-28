<?php

declare(strict_types=1);

namespace App\Actions\Weather\API;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

final class WeatherFromAPIAction
{
    /**
     * @throws ConnectionException
     */
    public static function handle(string $city)
    {
        $response = Http::get(config('services.openweather.base_url').'/weather', [
            'q' => $city,
            'appid' => config('services.openweather.api_key'),
            'units' => 'metric',
        ]);

        // if location not found return null
        if (! $response->ok()) {
            return null;
        }

        return $response->json();
    }
}
