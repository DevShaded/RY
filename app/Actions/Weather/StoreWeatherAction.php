<?php

declare(strict_types=1);

namespace App\Actions\Weather;

use App\Models\Weather;

final class StoreWeatherAction
{
    public static function handle(array $data): Weather
    {
        return Weather::updateOrCreate(
            [
                'name' => $data['name'],
                'country' => $data['sys']['country'],
            ],
            [
                'name' => $data['name'],
                'country' => $data['sys']['country'],
                'temperature' => round($data['main']['temp']),
                'condition' => $data['weather'][0]['description'],
                'humidity' => round($data['main']['humidity']),
                'wind_speed' => round($data['wind']['speed']),
                'visibility' => round($data['visibility'] / 1000),
                'feels_like' => round($data['main']['feels_like']),
                'icon' => WeatherIconAction::handle($data['weather'][0]['icon']),
            ]);
    }
}
