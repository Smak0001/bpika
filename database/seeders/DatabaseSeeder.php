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
        /*$this->call(UsersTableSeeder::class);
        $this->call(StationSeeder::class);
        $this->call(MeasurementSeeder::class);
        $this->call(DataFromLastSevenDaysSeeder::class);*/

        if(VisualCrossingDataFromLastSevenDays::all()->isEmpty()){
            Schema::dropIfExists('visual_crossing_data_from_last_seven_days');
            $path = 'database/table_exports/visual_crossing_data_from_last_seven_days.sql';
            DB::unprepared(file_get_contents($path));
        }
    }
}
