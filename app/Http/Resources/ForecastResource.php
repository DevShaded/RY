<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Forecast;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Forecast */
final class ForecastResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'day' => $this->day,
            'condition' => $this->condition,
            'high' => $this->high,
            'low' => $this->low,
            'icon' => $this->icon,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'weather_id' => $this->weather_id,
        ];
    }
}
