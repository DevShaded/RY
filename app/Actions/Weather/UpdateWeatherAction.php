<?php

declare(strict_types=1);

namespace App\Actions\Weather;

use App\Actions\Weather\API\ForecastFromAPIAction;
use App\Actions\Weather\API\WeatherFromAPIAction;
use App\Models\Weather;
use DB;
use Illuminate\Http\Client\ConnectionException;
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

        DB::transaction(function () use ($currentWeatherData) {
            $updateWeather = StoreWeatherAction::handle($currentWeatherData);

            $updateWeather->forecast()->delete();

            $forecastData = ForecastFromAPIAction::handle(
                $currentWeatherData['coord']['lat'],
                $currentWeatherData['coord']['lon']
            );

            StoreForecastAction::handle($updateWeather, $forecastData);
        });
    }
}
