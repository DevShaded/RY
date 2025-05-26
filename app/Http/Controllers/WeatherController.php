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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Inertia\Inertia;

final class WeatherController extends Controller
{
    public function index()
    {
        $weathers = Weather::with('forecast')->select([
            'id',
            'name',
            'country',
            'temperature',
        ])->get();

        return Inertia::render('Index', [
            'weathers' => WeatherResource::collection($weathers),
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

        // First try exact match (more efficient for indexed columns)
        $weather = Weather::where('name', $location)
            ->orWhere('name', 'like', $location.'%') // Then try starts with
            ->first();

        if (! $weather) {
            try {
                $currentWeather = WeatherFromAPIAction::handle($location);

                if (! $currentWeather) {
                    return redirect()->to(route('home'))
                        ->with('error', 'Værmelding for '.Str::title($location).' ble ikke funnet.');
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

                return redirect()->route('weather.show', [
                    'location' => $weather->name,
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

        return $weather->load('forecast');

        return Inertia::render('Weather/Show', [
            'weather' => new WeatherResource($weather->load('forecast')),
        ]);
    }
}
