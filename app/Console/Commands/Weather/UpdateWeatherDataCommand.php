<?php

declare(strict_types=1);

namespace App\Console\Commands\Weather;

use App\Actions\Weather\UpdateWeatherAction;
use App\Models\Weather;
use Illuminate\Console\Command;
use Illuminate\Http\Client\ConnectionException;
use Throwable;

final class UpdateWeatherDataCommand extends Command
{
    protected $signature = 'weather:update-weather-data';

    protected $description = 'Command description';

    /**
     * @throws Throwable
     * @throws ConnectionException
     */
    public function handle(): void
    {
        $staleWeathers = Weather::where('updated_at', '<', now()->subSeconds(1))
            ->get();

        if ($staleWeathers->isEmpty()) {
            $this->info('No stale weather data found.');

            return;
        }

        foreach ($staleWeathers as $weather) {
            UpdateWeatherAction::handle($weather);
        }

        $this->info('Weather data updated successfully for stale entries.');
    }
}
