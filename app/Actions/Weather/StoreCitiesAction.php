<?php

declare(strict_types=1);

namespace App\Actions\Weather;

use App\Models\City;

final class StoreCitiesAction
{
    public static function handle(string $name, string $country, float $latitude, float $longitude): ?City
    {
        $city = City::where('name', $name)
            ->where('country', $country)
            ->first();

        if (! $city) {
            return City::create([
                'name' => $name,
                'country' => $country,
                'latitude' => $latitude,
                'longitude' => $longitude,
            ]);
        }

        return $city;
    }
}
