<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class WeatherRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'min:2'],
            'country' => ['sometimes'],
            'temperature' => ['sometimes', 'integer'],
            'condition' => ['sometimes'],
            'humidity' => ['sometimes', 'integer'],
            'wind_speed' => ['sometimes', 'integer'],
            'visibility' => ['sometimes', 'integer'],
            'feels_like' => ['sometimes', 'integer'],
            'icon' => ['sometimes'],
        ];
    }
}
