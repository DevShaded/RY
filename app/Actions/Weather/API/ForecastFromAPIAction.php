<?php

namespace App\Actions\Weather\API;

use App\Actions\Weather\WeatherIconAction;
use Carbon\Carbon;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ForecastFromAPIAction
{
    private const CACHE_TTL_SECONDS = 1800; // 30 minutes
    private const DEFAULT_FORECAST_DAYS = 5;

    /**
     * @throws ConnectionException
     * @throws \Exception
     */
    public static function handle(float $latitude, float $longitude): array
    {
        $cacheKey = self::makeCacheKey($latitude, $longitude);

        return Cache::remember(
            $cacheKey,
            self::CACHE_TTL_SECONDS,
            fn() => self::fetchAndFormat($latitude, $longitude)
        );
    }

    private static function makeCacheKey(float $lat, float $lon): string
    {
        return sprintf(
            'forecast_%s_%s',
            round($lat, 2),
            round($lon, 2)
        );
    }

    /**
     * @throws ConnectionException
     * @throws \Exception
     */
    private static function fetchAndFormat(float $lat, float $lon): array
    {
        try {
            $resp    = self::callApi($lat, $lon);
            $rawData = self::validateResponse($resp);

            $grouped = self::groupByDate($rawData['list']);
            $days    = self::sliceDays($grouped);

            return self::buildForecast($days);

        } catch (ConnectionException $e) {
            Log::error('Weather API connection error: ' . $e->getMessage());
            throw new ConnectionException('Kunne ikke koble til værtjenesten', 0, $e);

        } catch (\Exception $e) {
            if (preg_match('/^(Kunne ikke|Uventet|Værtjeneste|For mange)/u', $e->getMessage())) {
                throw $e;
            }

            Log::error('Weather forecast error: ' . $e->getMessage());
            throw new \Exception('Det oppsto en feil ved henting av værmelding', 0, $e);
        }
    }

    /**
     * @throws ConnectionException
     */
    private static function callApi(float $lat, float $lon): Response
    {
        $url = config('services.openweather.base_url') . '/forecast';

        $resp = Http::get($url, [
            'lat'   => $lat,
            'lon'   => $lon,
            'appid' => config('services.openweather.api_key'),
            'units' => config('services.openweather.units', 'metric'),
        ]);

        if (! $resp->ok()) {
            self::handleError($resp);
        }

        return $resp;
    }

    /**
     * @throws \Exception
     */
    private static function handleError(Response $resp): void
    {
        $code    = $resp->status();
        $message = $resp->json('message', 'Ukjent feil');

        Log::error("Weather API error: {$message} (Status: {$code})");

        if ($code === 401) {
            throw new \Exception("Værtjeneste API-nøkkel er ugyldig");
        }

        if ($code === 429) {
            throw new \Exception("For mange forespørsler til værtjenesten");
        }

        throw new \Exception("Kunne ikke hente værmelding: {$message}");
    }

    /**
     * @throws \Exception
     */
    private static function validateResponse(Response $resp): array
    {
        $data = $resp->json();

        if (empty($data['list']) || ! is_array($data['list'])) {
            throw new \Exception("Uventet dataformat fra værtjenesten");
        }

        return $data;
    }

    /**
     * @return array<string, array{dt:int, temps:float[], conds:string[], icons:string[]}>
     */
    private static function groupByDate(array $items): array
    {
        $days = [];

        foreach ($items as $item) {
            if (
                ! isset($item['dt'], $item['main']['temp'], $item['weather'][0]['description'], $item['weather'][0]['icon'])
            ) {
                continue;
            }

            $key = date('Y-m-d', $item['dt']);

            $days[$key]['dt']    ??= $item['dt'];
            $days[$key]['temps'][] = $item['main']['temp'];
            $days[$key]['conds'][] = $item['weather'][0]['description'];
            $days[$key]['icons'][] = $item['weather'][0]['icon'];
        }

        return $days;
    }

    /**
     * Take only the first N days.
     *
     * @param  array<string,mixed>  $grouped
     * @return array<int,mixed>
     */
    private static function sliceDays(array $grouped): array
    {
        $limit = config('services.openweather.forecast_days', self::DEFAULT_FORECAST_DAYS);
        return array_slice($grouped, 0, $limit, true);
    }

    private static function buildForecast(array $days): array
    {
        $result = [];
        $i      = 0;

        foreach ($days as $day) {
            $result[] = [
                'day'       => self::formatDay($day['dt'], $i),
                'condition' => self::mostCommon($day['conds'], true),
                'high'      => round(max($day['temps'])),
                'low'       => round(min($day['temps'])),
                'icon'      => WeatherIconAction::handle(
                    self::mostCommon($day['icons'])
                ),
            ];
            $i++;
        }

        return $result;
    }

    /** @param bool $capitalize */
    private static function mostCommon(array $values, bool $capitalize = false): string
    {
        $counts = array_count_values($values);
        arsort($counts);
        $key    = array_key_first($counts);

        if ($capitalize) {
            return mb_convert_case($key, MB_CASE_TITLE, 'UTF-8');
        }

        return $key ?: '';
    }

    private static function formatDay(int $timestamp, int $index): string
    {
        return match ($index) {
            0 => 'I dag',
            1 => 'I morgen',
            default => Carbon::createFromTimestamp($timestamp)
                ->locale('nb')
                ->isoFormat('dddd'),
        };
    }
}
