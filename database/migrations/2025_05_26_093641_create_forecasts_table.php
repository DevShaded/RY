<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forecasts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('weather_id')->references('id')->on('weathers')->cascadeOnDelete();
            $table->string('day');
            $table->string('condition');
            $table->integer('high');
            $table->integer('low');
            $table->string('icon');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forecasts');
    }
};
