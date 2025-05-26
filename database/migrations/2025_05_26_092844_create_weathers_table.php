<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weathers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('country');
            $table->integer('temperature');
            $table->string('condition');
            $table->integer('humidity');
            $table->integer('wind_speed');
            $table->integer('visibility');
            $table->integer('feels_like');
            $table->string('icon');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weathers');
    }
};
