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
        Schema::create('data_from_last_seven_days', function (Blueprint $table) {
            $table->id();
            $table->string('station');
            $table->string('dateTime')->nullable();
            $table->float('PET', 17, 14)->nullable();
            $table->timestamps();

            $table->foreign('station')->references('code')->on('stations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_from_last_seven_days');
    }
};
