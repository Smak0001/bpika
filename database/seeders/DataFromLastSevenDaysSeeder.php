<?php

namespace Database\Seeders;

use App\Models\DataFromLastSevenDays;
use App\Models\Station;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use GuzzleHttp\Client;
use Carbon\Carbon;

class DataFromLastSevenDaysSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sevenDaysAgo = Carbon::now()->subDays(7);
        $timeString = $sevenDaysAgo->toISOString();
        $trimmedTimeString = substr($timeString, 0, 23) . 'Z';

        $stations = Station::all();

        DataFromLastSevenDays::truncate();
        foreach($stations as $station) {
            $url = 'http://127.0.0.10' . '/api/stations/' . $station->code . '/measurements?startDate=' . $trimmedTimeString . '&grouping=hourly&column=pet';
            $client = new Client();

            try {
                $api_response = $client->get($url);
                $response= json_decode($api_response->getBody()->getContents(),true);
                foreach($response['data'] as $row) {
                    DataFromLastSevenDays::insert([
                        'station' => $station->code,
                        'dateTime' => $row['x'],
                        'PET' => $row['y'],
                    ]);
                }
            } catch (Throwable $e) {
                report($e);
            }

        }
    }
}
