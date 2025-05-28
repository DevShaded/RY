<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Weather\API\ForecastFromAPIAction;
use App\Actions\Weather\API\WeatherFromAPIAction;
use App\Actions\Weather\StoreForecastAction;
use App\Actions\Weather\StoreWeatherAction;
use App\Http\Resources\WeatherResource;
use App\Models\Weather;
use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Inertia\Inertia;

final class WeatherController extends Controller
{
    private const CACHE_TTL_SECONDS = 900; // 15 minutes

    public function index()
    {
        $recentSearches = session('recent_weather_searches', []);

        $weatherData = collect($recentSearches)->map(function ($search, $index) {
            return [
                'id' => $index + 1,
                'name' => $search['name'],
                'country' => $search['country'],
                'temperature' => $search['temperature'],
                'icon' => $search['icon'],
            ];
        })->values()->all();

        return Inertia::render('Index', [
            'weathers' => [
                'data' => $weatherData
            ],
        ]);
    }

    /**
     * Display weather information for a location
     *
     * @throws Exception
     */
    public function show(string $location)
    {
        // validate the location
        $validator = Validator::make(['location' => $location], [
            'location' => ['required', 'string', 'min:2', 'max:255'],
        ]);

        if ($validator->fails()) {
            return redirect()->route('home')
                ->withErrors($validator)
                ->withInput();
        }

        $cacheKey = $this->makeCacheKey($location);

        $weather = Cache::remember(
            $cacheKey,
            self::CACHE_TTL_SECONDS,
            function () use ($location) {
                // First try exact match (more efficient for indexed columns)
                $weather = Weather::where('name', $location)
                    ->orWhere('name', 'like', $location.'%') // Then try starts with
                    ->first();

                if (! $weather) {
                    try {
                        $currentWeather = WeatherFromAPIAction::handle($location);

                        if (! $currentWeather) {
                            return null;
                        }

                        $weather = DB::transaction(function () use ($currentWeather) {
                            try {
                                $weather = StoreWeatherAction::handle($currentWeather);

                                $forecastData = ForecastFromAPIAction::handle(
                                    $currentWeather['coord']['lat'],
                                    $currentWeather['coord']['lon']
                                );

                                StoreForecastAction::handle($weather, $forecastData);

                                return $weather;
                            } catch (Exception $e) {
                                // Re-throw to trigger transaction rollback
                                throw $e;
                            }
                        });
                    } catch (ConnectionException $e) {
                        // Don't cache connection errors, re-throw to handle in controller
                        throw $e;
                    } catch (Exception $e) {
                        // Don't cache other errors, re-throw to handle in controller
                        throw $e;
                    }
                }

                return $weather;
            }
        );

        // Handle cases where weather data couldn't be found
        if (! $weather) {
            return redirect()->to(route('home'))
                ->with('error', 'Værmelding for '.Str::title($location).' ble ikke funnet.');
        }

        // Handle exceptions that weren't cached
        try {
            // If we got weather from cache but it doesn't have forecast loaded, load it
            if (! $weather->relationLoaded('forecast')) {
                $weather->load('forecast');
            }

            // Store this search in session for recent searches
            $this->storeRecentSearch($weather);

            return Inertia::render('Weather/Show', [
                'weather' => new WeatherResource($weather),
            ]);
        } catch (ConnectionException $e) {
            // Handle network connection issues
            return redirect()->route('home')
                ->with('error', 'Kunne ikke koble til værtjenesten. Vennligst prøv igjen senere.');
        } catch (Exception $e) {
            // Handle any other unexpected errors
            report($e); // Log the exception

            return redirect()->route('home')
                ->with('error', 'Det oppsto en feil ved henting av værdata. Vennligst prøv igjen senere.');
        }
    }

    /**
     * Store a weather search in the session for recent searches
     */
    private function storeRecentSearch(Weather $weather): void
    {
        $recentSearches = session('recent_weather_searches', []);

        // Create search entry
        $searchEntry = [
            'name' => $weather->name,
            'country' => $weather->country,
            'temperature' => $weather->temperature,
            'icon' => $weather->icon,
            'searched_at' => now()->toISOString(),
        ];

        // Remove if already exists (to avoid duplicates)
        $recentSearches = collect($recentSearches)
            ->reject(fn($search) => $search['name'] === $weather->name)
            ->values()
            ->all();

        // Add to beginning of array
        array_unshift($recentSearches, $searchEntry);

        // Keep only last 10 searches
        $recentSearches = array_slice($recentSearches, 0, 10);

        // Store back in session
        session(['recent_weather_searches' => $recentSearches]);
    }

    private function makeCacheKey(string $location): string
    {
        return sprintf(
            'weather_show_%s',
            mb_strtolower(trim($location))
        );
    }
}
