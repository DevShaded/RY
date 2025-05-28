<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Weather\API\SearchCitiesAction;
use App\Actions\Weather\StoreCitiesAction;
use App\Models\City;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

final class CitySearchController extends Controller
{
    /**s
     * Search for cities by name
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'query' => ['required', 'string', 'min:2', 'max:255'],
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid query'], 422);
        }

        $query = $request->input('query');

        // Check cache first (cache for 24 hours)
        $cacheKey = 'city_search_'.mb_strtolower($query);
        if (Cache::has($cacheKey)) {
            return response()->json(Cache::get($cacheKey));
        }

        // Then check database
        $cities = City::where('name', 'like', $query.'%')
            ->select(['id', 'name', 'country', 'latitude', 'longitude'])
            ->limit(5)
            ->get();

        if ($cities->isNotEmpty()) {
            // Store in cache and return
            Cache::put($cacheKey, $cities, now()->addHours(24));

            return response()->json($cities);
        }

        // If not in database, fetch from API
        $apiResults = SearchCitiesAction::handle($query);

        if (! $apiResults) {
            return response()->json([], 200);
        }

        $results = [];

        foreach ($apiResults as $result) {
            // Store each city in database
            $city = StoreCitiesAction::handle(
                $result['name'],
                $result['country'],
                $result['lat'],
                $result['lon']
            );

            if (! $city) {
                return response()->json(['error' => 'Failed to store city'], 500);
            }

            $results[] = [
                'id' => $city->id,
                'name' => $city->name,
                'country' => $city->country,
                'latitude' => $city->latitude,
                'longitude' => $city->longitude,
            ];
        }

        // Store in cache
        Cache::put($cacheKey, $results, now()->addHours(24));

        return response()->json($results);
    }
}
