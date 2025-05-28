<?php

declare(strict_types=1);

namespace App\Actions\Weather;

use App\Actions\Weather\API\ForecastFromAPIAction;
use App\Actions\Weather\API\WeatherFromAPIAction;
use App\Models\Weather;
use DB;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Throwable;

final class UpdateWeatherAction
{
    /**
     * @throws ConnectionException
     * @throws Throwable
     */
    public static function handle(Weather $weather): void
    {
        $currentWeatherData = WeatherFromAPIAction::handle($weather->name);

        DB::transaction(function () use ($currentWeatherData, $weather) {
            $updateWeather = StoreWeatherAction::handle($currentWeatherData);

            $updateWeather->forecast()->delete();

            $forecastData = ForecastFromAPIAction::handle(
                $currentWeatherData['coord']['lat'],
                $currentWeatherData['coord']['lon']
            );

            StoreForecastAction::handle($updateWeather, $forecastData);

            // Clear the cache for this weather location
            self::clearWeatherCache($weather->name);
        });
    }

    private static function clearWeatherCache(string $location): void
    {
        $cacheKey = sprintf(
            'weather_show_%s',
            mb_strtolower(trim($location))
        );
        
        Cache::forget($cacheKey);
    }
}
