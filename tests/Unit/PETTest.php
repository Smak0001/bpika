<?php

namespace Tests\Unit;

use App\Services\PETService;
use DateTime;
use DateTimeZone;
use ErrorException;
use Tests\TestCase;

class PETTest extends TestCase {

    /**
     * @var PETService
     */
    private $PETService;

    public function setUp(): void {

        parent::setUp(); // TODO: Change the autogenerated
        $this->PETService = $this->app->make('App\Services\PETService');
    }

    public function testCanOpenTestFiles() {

        try {
            $inputFile = fopen(asset('storage/Binnenstad.csv'), 'r');
        }
        catch (ErrorException $exception) {
            $inputFile = FALSE;
            echo 'The input file doesn\'t exist';
        }

        try {
            $outputFile = fopen(asset('storage/Binnenstad_TCPET.csv'), 'r');
        }
        catch (ErrorException $exception) {
            $outputFile = FALSE;
            echo 'The output file doesn\'t exist';
        }

        try {
            $notExistingFile = fopen(asset('storage/notExisting.csv'), 'r');
        }
        catch (ErrorException $exception) {
            $notExistingFile = FALSE;
        }

        $this->assertFalse($inputFile === FALSE);
        $this->assertFalse($outputFile === FALSE);
        $this->assertTrue($notExistingFile === FALSE);
    }

    public function testInputShouldBeEqualToOutput() {

        /*
         * This file will be the input to test the PET calculation
         * It has been created from the values in Binnenstad.csv
         * For reference, the file has the following columns:
         * Year (The last two digits of the year of the measurement),
         * Month (The month of the measurement),
         * Day (The day of the measurement),
         * DOY (The Day of the Year of the measurement),
         * Hour (The hour of the measurement),
         * Minute (The minute of the measurement),
         * Decimal Time [H] (Combined time of the day in hours as a decimal number, ranges from 0 to below 24),
         * Decimal Time Year[D] (Combined time of the year in days as a decimal number, ranges from 0 to below 365 or 366, depending on if the year is a leap year or not),
         * Air Pressure [hPa] (Air pressure in hectoPascal),
         * Precipitation [mm] (Amount of precipitation in millimeters),
         * Air Temperature [°C] (Air temperature in Celsius),
         * Relative Humidity [%] (Relative air Humidity in percentages),
         * Dew Point [°C] (Dew point in Celsius),
         * Wind Speed [m/s] (Wind speed in meters per second),
         * Unscreened Solar Radiation [W/m2] (Unscreened solar radiation in Watt per m²),
         * Screened Solar Radiation [W/m2] (Screened solar radiation in Watt per m²),
         * Fraction of Direct Solar Radiation (Fraction of direct solar radiation),
         * Fraction of Diffuse Solar Radiation (Fraction of diffuse solar radiation),
         * Wet Bulb Temperature [°C] (Wet bulb temperature in Celsius),
         * Globe Temperature [°C] (Globe temperature in Celsius),
         * Mean Radiant Temperature [°C] (Mean radiant temperature in Celsius),
         * Wet Bulb Globe Temperature [°C] (Wet bulb globe temperature in Celsius),
         * Cosine Of Zenith Angle (Cosine of the Zenith Angle),
         * Core Temperature [°C] (Core temperature of the average person in Celsius),
         * Skin Temperature [°C] (Skin temperature of the average person in Celsius),
         * Clothes Temperature [°C] (Clothes temperature of the average person in Celsius),
         * Evaporation of Sweat (Evaporation of sweat of the average person),
         * Physiologically Equivalent Temperature [°C] (Physiologically Equivalent Temperature in Celsius)
        */
        $inputFile = fopen(asset('storage/Binnenstad_TCPET.csv'), 'r');
        $isFirstLine = true;
        $hasNotReachedEndOfFile = true;
        while ($hasNotReachedEndOfFile) {

            // Get the data from this row
            $rowData = fgetcsv($inputFile);

            // Check if we reached the end of the file
            if ($rowData === false) {
                $hasNotReachedEndOfFile = false;
                continue;
            }

            // Skip the first line, because these are column names
            if ($isFirstLine) {
                $isFirstLine = false;
                continue;
            }

            // Replace '#N/A' with null value
            $rowData = array_replace($rowData,
                array_fill_keys(
                    array_keys($rowData, '#N/A'),
                    null
                )
            );

            // Assign the values to local variables for readability
            // The values are currently all strings, so we need to get the right value,
            // because null will be interpreted as 0, check if value is null first
            $year =                             $rowData[0] === null ? null : intval($rowData[0]);
            $month =                            $rowData[1] === null ? null : intval($rowData[1]);
            $day =                              $rowData[2] === null ? null : intval($rowData[2]);
            $DOY =                              $rowData[3] === null ? null : intval($rowData[3]);
            $hour =                             $rowData[4] === null ? null : intval($rowData[4]);
            $minute =                           $rowData[5] === null ? null : intval($rowData[5]);
            $decimalTime =                      $rowData[6] === null ? null : floatval($rowData[6]);
            $decimalYear =                      $rowData[7] === null ? null : floatval($rowData[7]);
            $airPressure =                      $rowData[8] === null ? null : floatval($rowData[8]);
            $precipitation =                    $rowData[9] === null ? null : floatval($rowData[9]);
            $airTemperature =                   $rowData[10] === null ? null : floatval($rowData[10]);
            $humidity =                         $rowData[11] === null ? null : floatval($rowData[11]);
            $dewPoint =                         $rowData[12] === null ? null : floatval($rowData[12]);
            $windSpeed =                        $rowData[13] === null ? null : floatval($rowData[13]);
            $unscreenedSolarRadiation =         $rowData[14] === null ? null : floatval($rowData[14]);
            $screenedSolarRadiation =           $rowData[15] === null ? null : floatval($rowData[15]);
            $fractionOfDirectSolarRadiation =   $rowData[16] === null ? null : floatval($rowData[16]);
            $fractionOfDiffuseSolarRadiation =  $rowData[17] === null ? null : floatval($rowData[17]);
            $wetBulbTemperature =               $rowData[18] === null ? null : floatval($rowData[18]);
            $globeTemperature =                 $rowData[19] === null ? null : floatval($rowData[19]);
            $meanRadiantTemperature =           $rowData[20] === null ? null : floatval($rowData[20]);
            $wetBulbGlobeTemperature =          $rowData[21] === null ? null : floatval($rowData[21]);
            $cosineOfZenithAngle =              $rowData[22] === null ? null : floatval($rowData[22]);
            $coreTemperature =                  $rowData[23] === null ? null : floatval($rowData[23]);
            $temperatureOfSkin =                $rowData[24] === null ? null : floatval($rowData[24]);
            $temperatureOfClothes =             $rowData[25] === null ? null : floatval($rowData[25]);
            $evaporationOfSweat =               $rowData[26] === null ? null : floatval($rowData[26]);
            $PET =                              $rowData[27] === null ? null : floatval($rowData[27]);

            // All the measurements from this file are from the Station HZ2
            // Therefore we will assign the latitude and the longitude from this station
            $latitude = 51.5;
            $longitude = 3.75;

            // Construct datetime from values
            if ($day !== null and
                $month !== null and
                $year !== null and
                $hour !== null and
                $minute !== null) {

                $createdDateTimeString = sprintf(   '%u-%u-20%u %u:%u',
                                                    $day, $month, $year, $hour, $minute);
                $createdDateTime = new DateTime($createdDateTimeString);
                $createdDateTime->setTimezone(new DateTimeZone('UTC'));

                // Check DOY and Decimal time
                // Add one to DOY because date starts at 0, and DOY at 1
                $calculatedDOY = date('z', $createdDateTime->getTimestamp()) + 1;
                $this->assertTrue(  $calculatedDOY === $DOY,
                                    sprintf('The day of year calculation is off by %u days.',
                                            abs($calculatedDOY - $DOY)));
                $calculatedDecimalTime =    floatval($createdDateTime->format('H')) +
                                            (floatval($createdDateTime->format('i')) / 60) +
                                            (floatval($createdDateTime->format('s')) / 3600);
                $this->assertTrue(  $calculatedDecimalTime === $decimalTime,
                                    sprintf('The decimal time calculation is off by %u days.',
                                            abs($calculatedDecimalTime - $decimalTime)));
            }
            else {
                $createdDateTimeString = null;
            }

            // Calculate and check intermediate values if they are within acceptable limits
            // Currently, the accepted limit is: 0,001
            $acceptedLimit = 0.2;

            // Diffuse and Direct solar radiation
            if ($screenedSolarRadiation !== null and
                $DOY !== null and
                $decimalTime !== null and
                $fractionOfDiffuseSolarRadiation !== null and
                $fractionOfDirectSolarRadiation !== null) {

                $calculatedFractionOfDiffuseSolarRadiation = $this->PETService->fr_diffuse( $screenedSolarRadiation,
                                                                                            $latitude,
                                                                                            $longitude,
                                                                                            $DOY,
                                                                                            $decimalTime
                );
                $calculatedFractionOfDirectSolarRadiation = 1. - $calculatedFractionOfDiffuseSolarRadiation;
                $diffuseDifference = abs($calculatedFractionOfDiffuseSolarRadiation - $fractionOfDiffuseSolarRadiation);
                $this->assertTrue(  $diffuseDifference < $acceptedLimit,
                                    sprintf('The fraction of diffuse solar radiation is off by %f Watts per m².',
                                            $diffuseDifference - $acceptedLimit));
                $directDifference = abs($calculatedFractionOfDirectSolarRadiation - $fractionOfDirectSolarRadiation);
                $this->assertTrue(  $directDifference < $acceptedLimit,
                                    sprintf('The fraction of diffuse solar radiation is off by %f Watts per m².',
                                            $directDifference - $acceptedLimit));
            }
            else {
                $calculatedFractionOfDiffuseSolarRadiation = null;
                $calculatedFractionOfDirectSolarRadiation = null;
            }

            // Cosine of zenith angle
            if ($DOY !== null and
                $decimalTime !== null and
                $cosineOfZenithAngle !== null) {

                $calculatedCosineOfZenithAngle = $this->PETService->sin_solar_elev( $latitude,
                                                                                    $longitude,
                                                                                    $DOY,
                                                                                    $decimalTime);
            $czaDifference = abs($calculatedCosineOfZenithAngle - $cosineOfZenithAngle);
            $this->assertTrue(  $czaDifference < $acceptedLimit,
                                sprintf('The cosine of the zenith angle is off by %f.',
                                        $czaDifference - $acceptedLimit));
            }
            else {
                $calculatedCosineOfZenithAngle = null;
            }

            // Globe temperature
            // Currently no urban correction applied
            if ($airTemperature !== null and
                $humidity !== null and
                $windSpeed !== null and
                $screenedSolarRadiation !== null and
                $calculatedFractionOfDirectSolarRadiation !== null and
                $calculatedCosineOfZenithAngle !== null and
                $globeTemperature !== null) {

                $urbanFactor = 1.;
                $calculatedGlobeTemperature = $this->PETService->calc_Tglobe(   $airTemperature,
                                                                                $humidity,
                                                                                $urbanFactor * $windSpeed,
                                                                                $screenedSolarRadiation,
                                                                                $calculatedFractionOfDirectSolarRadiation,
                                                                                $calculatedCosineOfZenithAngle);
                $globeTemperatureDifference = abs($calculatedGlobeTemperature - $globeTemperature);
                $this->assertTrue(  $globeTemperatureDifference < $acceptedLimit,
                                    sprintf('The globe temperature is off by %f degrees.',
                                            $globeTemperatureDifference - $acceptedLimit));
            }
            else {
                $calculatedGlobeTemperature = null;
            }

            // Median radiant temperature
            if ($calculatedGlobeTemperature !== null and
                $airTemperature !== null and
                $windSpeed !== null and
                $meanRadiantTemperature !== null) {

                $calculatedMeanRadiantTemperature = $this->PETService->Tmrt($calculatedGlobeTemperature,
                                                                            $airTemperature,
                                                                            $windSpeed);
                $meanRadiantTemperatureDifference = abs($calculatedMeanRadiantTemperature - $meanRadiantTemperature);
                $this->assertTrue(  $meanRadiantTemperatureDifference < $acceptedLimit,
                                    sprintf('The mean radiant temperature is off by %f degrees.',
                                            $meanRadiantTemperatureDifference - $acceptedLimit));
            }
            else {
                $calculatedMeanRadiantTemperature = null;
            }

            // System temperatures
            if ($airTemperature !== null and
                $calculatedMeanRadiantTemperature !== null and
                $humidity !== null and
                $windSpeed !== null and
                $coreTemperature !== null and
                $temperatureOfSkin !== null and
                $temperatureOfClothes !== null and
                $evaporationOfSweat !== null) {

                $systemOutput = $this->PETService->system(  $airTemperature,
                                                            $calculatedMeanRadiantTemperature,
                                                            $humidity,
                                                            $windSpeed);
                $calculatedCoreTemperature = $systemOutput[0];
                $calculatedTemperatureOfSkin = $systemOutput[1];
                $calculatedTemperatureOfClothes = $systemOutput[2];
                $calculatedEvaporationOfSweat = $systemOutput[3];
                $coreTemperatureDifference = abs($calculatedCoreTemperature - $coreTemperature);
                $this->assertTrue(  $coreTemperatureDifference < $acceptedLimit,
                                    sprintf('The core temperature is off by %f degrees.',
                                            $coreTemperatureDifference - $acceptedLimit));
                $skinTemperatureDifference = abs($calculatedTemperatureOfSkin - $temperatureOfSkin);
                $this->assertTrue(  $skinTemperatureDifference < $acceptedLimit,
                                    sprintf('The skin temperature is off by %f degrees.',
                                            $skinTemperatureDifference - $acceptedLimit));
                $clothesTemperatureDifference = abs($calculatedTemperatureOfClothes - $temperatureOfClothes);
                $this->assertTrue(  $clothesTemperatureDifference < $acceptedLimit,
                                    sprintf('The clothes temperature is off by %f degrees.',
                                            $clothesTemperatureDifference - $acceptedLimit));
                $evaporationOfSweatDifference = abs($calculatedEvaporationOfSweat - $evaporationOfSweat);
                $this->assertTrue(  $evaporationOfSweatDifference < $acceptedLimit,
                                    sprintf('The evaporation of sweat is off by %f.',
                                            $evaporationOfSweatDifference - $acceptedLimit));
            }
            else {
                $calculatedCoreTemperature = null;
                $calculatedTemperatureOfSkin = null;
                $calculatedTemperatureOfClothes = null;
                $calculatedEvaporationOfSweat = null;
            }

            // Calculate PET value with pet method
            if ($calculatedCoreTemperature !== null and
                $calculatedTemperatureOfSkin !== null and
                $calculatedTemperatureOfClothes !== null and
                $airTemperature !== null and
                $calculatedEvaporationOfSweat !== null and
                $PET !== null) {

                $calculatedPET = $this->PETService->pet($calculatedCoreTemperature,
                                                        $calculatedTemperatureOfSkin,
                                                        $calculatedTemperatureOfClothes,
                                                        $airTemperature,
                                                        $calculatedEvaporationOfSweat);

                // Check if calculated PET value is within limits
                $calculatedPETDifference = abs($calculatedPET - $PET);
                $this->assertTrue(  $calculatedPETDifference < $acceptedLimit,
                                    sprintf('The calculated physiologically equivalent temperature is off by %f degrees.',
                                            $calculatedPETDifference - $acceptedLimit));
            }

            // Calculate PET value with compute method
            if ($createdDateTimeString !== null and
                $airTemperature !== null and
                $screenedSolarRadiation !== null and
                $humidity !== null and
                $windSpeed !== null and
                $PET !== null) {

                $computedPET = $this->PETService->computePETFromMeasurement($createdDateTimeString,
                                                                            $airTemperature,
                                                                            $screenedSolarRadiation,
                                                                            $humidity,
                                                                            $windSpeed,
                                                                            $latitude,
                                                                            $longitude);

                // Check if the computed PET value is within acceptable limits
                $computedPETDifference = abs($computedPET - $PET);
                $this->assertTrue(  $computedPETDifference < $acceptedLimit,
                                    sprintf('The computed physiologically equivalent temperature is off by %f degrees.',
                                            $computedPETDifference - $acceptedLimit));
            }
        }
    }
}
