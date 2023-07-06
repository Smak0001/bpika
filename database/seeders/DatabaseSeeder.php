<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UsersTableSeeder::class);
        $this->call(StationSeeder::class);
        $this->call(MeasurementSeeder::class);
        $this->call(DataFromLastSevenDaysSeeder::class);
    }
}
