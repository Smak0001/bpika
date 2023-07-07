<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('visual_crossing_data_from_last_seven_days', function (Blueprint $table) {
            $table->id();
            $table->string('location');
            $table->string('datetime')->nullable();
            $table->float('temperature')->nullable();
            $table->float('temperatureFeelsLike')->nullable();
            $table->float('humidity')->nullable();
            $table->float('dew')->nullable();
            $table->float('rain')->nullable();
            $table->float('rain_chance')->nullable();
            $table->string('rain_type')->nullable();
            $table->float('snow')->nullable();
            $table->float('snow_depth')->nullable();
            $table->float('wind_speed')->nullable();
            $table->float('wind_gust')->nullable();
            $table->float('wind_direction')->nullable();
            $table->float('visibility')->nullable();
            $table->float('cloud_cover')->nullable();
            $table->float('pressure')->nullable();
            $table->float('solar_radiation')->nullable();
            $table->float('solar_energy')->nullable();
            $table->float('uv_index')->nullable();
            $table->string('icon')->nullable();
            $table->string('conditions')->nullable();
            $table->string('source')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visual_crossing_data_from_last_seven_days');
    }
};
