<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\VisualCrossingDataFromLastSevenDays;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UsersTableSeeder::class);
        $this->call(MeasurementSeeder::class);
        $this->call(StationSeeder::class);
        $this->call(MeasurementSeeder::class);
        $this->call(DataFromLastSevenDaysSeeder::class);

        if(VisualCrossingDataFromLastSevenDays::all()->isEmpty()){
            Schema::dropIfExists('visual_crossing_data_from_last_seven_days');
            $filename = "visual_crossing_data_from_last_seven_days.sql";
            // Create backup folder and set permission if not exist
            $databaseAt = str_replace('\\', '/', database_path() . "/table_exports/");
            $path = $databaseAt . $filename;
            DB::unprepared(file_get_contents($path));
        }
    }
}
