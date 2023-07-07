<?php

namespace Database\Seeders;

use App\Models\Station;
use App\Models\VisualCrossingDataFromLastSevenDays;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VisualCrossingDataFromLastSevenDaysSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = [];

        $stations = Station::all();
        foreach($stations as $station) {
            if(!in_array($station->city, $locations)) {
                array_push($locations, $station->city);
            }
        }

        foreach($locations as $location) {
            // This is limited to 1000 calls per day for the free version, To get all the information for
            // 5 locations you need aroundabout 990 calls. Scheduled this in Kernel to be called at 01:00:00 every day.
            $url = 'https://weather.visualcrossing.com/VisualCrossingWebServices/rest/services/timeline/' . $location . '/last7days/today?unitGroup=uk&key=RPKNC6N43DFEJGKPH46G2DGR3&contentType=json';

            $client = new Client();

            try {
                $api_response = $client->get($url);
                $response= json_decode($api_response->getBody()->getContents(),true);
                foreach($response['days'] as $day) {
                    foreach($day['hours'] as $hour) {
                        VisualCrossingDataFromLastSevenDays::insert([
                            'location' => $location,
                            'datetime' => $day['datetime'] . ' ' . $hour['datetime'],
                            'temperature' => $hour['temp'],
                            'temperatureFeelsLike' => $hour['feelslike'],
                            'humidity' => $hour['humidity'],
                            'dew' => $hour['dew'],
                            'rain' => $hour['precip'],
                            'rain_chance' => $hour['precipprob'],
                            'rain_type' => null,
                            'snow' => $hour['snow'],
                            'snow_depth' => $hour['snowdepth'],
                            'wind_speed' => $hour['windspeed'],
                            'wind_gust' => $hour['windgust'],
                            'wind_direction' => $hour['winddir'],
                            'visibility' => $hour['visibility'],
                            'cloud_cover' => $hour['cloudcover'],
                            'pressure' => $hour['pressure'],
                            'solar_radiation' => $hour['solarradiation'],
                            'solar_energy' => $hour['solarenergy'],
                            'uv_index' => $hour['uvindex'],
                            'icon' => $hour['icon'],
                            'conditions' => $hour['conditions'],
                            'source' => $hour['source'],
                        ]);
                    }
                }
            } catch (Throwable $e) {
                report($e);
            }
        }

        //https://weather.visualcrossing.com/VisualCrossingWebServices/rest/services/timeline/Groningen/last7days/today?unitGroup=us&key=PEHEDXVWCA3E5PSVKDPNUXZCV&contentType=json

        /*$sevenDaysAgo = Carbon::now()->subDays(7);
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
        }*/
    }
}
