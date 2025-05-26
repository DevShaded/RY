<?php

declare(strict_types=1);

namespace App\Actions\Weather;

final class WeatherIconAction
{
    public static function handle(string $iconCode): string
    {
        $iconMap = [
            '01d' => 'sun',
            '01n' => 'moon',
            '02d' => 'cloud-sun',
            '02n' => 'cloud-moon',
            '03d' => 'cloud',
            '03n' => 'cloud',
            '04d' => 'cloud',
            '04n' => 'cloud',
            '09d' => 'cloud-rain',
            '09n' => 'cloud-rain',
            '10d' => 'cloud-rain',
            '10n' => 'cloud-rain',
            '11d' => 'cloud-lightning',
            '11n' => 'cloud-lightning',
            '13d' => 'cloud-snow',
            '13n' => 'cloud-snow',
            '50d' => 'cloud-fog',
            '50n' => 'cloud-fog',
        ];

        return $iconMap[$iconCode] ?? 'cloud';
    }
}
