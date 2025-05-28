<?php

declare(strict_types=1);

namespace App\Actions\Weather\API;

use Exception;
use Illuminate\Support\Facades\Http;

final class SearchCitiesAction
{
    public static function handle(string $query)
    {
        try {
            $response = Http::get('https://api.openweathermap.org/geo/1.0/direct', [
                'q' => $query,
                'limit' => 5, // Limit to 5 suggestions
                'appid' => config('services.openweather.api_key'),
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (Exception $e) {
            report($e);

            return null;
        }
    }
}
