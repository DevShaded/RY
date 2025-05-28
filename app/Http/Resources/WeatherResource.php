<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Weather;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Weather */
final class WeatherResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'country' => $this->country,
            'temperature' => $this->temperature,
            'condition' => $this->condition,
            'humidity' => $this->humidity,
            'wind_speed' => $this->wind_speed,
            'visibility' => $this->visibility,
            'feels_like' => $this->feels_like,
            'icon' => $this->icon,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'forecast' => ForecastResource::collection($this->whenLoaded('forecast')),
        ];
    }
}
