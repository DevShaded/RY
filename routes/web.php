<?php

declare(strict_types=1);

use App\Http\Controllers\WeatherController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [WeatherController::class, 'index'])
    ->name('home');

Route::get('/weather/{location}', [WeatherController::class, 'show'])
    ->name('weather.show');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
