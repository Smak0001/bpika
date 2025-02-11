<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use Carbon\Carbon;
use File;
class ExportVisualCrossingDataFromLastSevenDays extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:exportVisualCrossingDataFromLastSevenDays';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create copy of table VisualCrossingDataFromLastSevenDays from existing database.';
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $filename = "visual_crossing_data_from_last_seven_days.sql";
        // Create backup folder and set permission if not exist
        $databaseAt = str_replace('\\', '/', database_path() . "/table_exports/");
        if(!File::exists($databaseAt)) {
            File::makeDirectory($databaseAt, 0755, true, true);
        } else if (File::exists($databaseAt . $filename)) {
            File::delete($databaseAt . '/' . $filename);
        }
        $path = $databaseAt . $filename;
        $command = str_replace('|', '"', "C:/xampp/mysql/bin/mysqldump -u " . env('DB_USERNAME') . " -h " . env('DB_HOST') . " " . env('DB_DATABASE') . " visual_crossing_data_from_last_seven_days > " . "|${path}|");
        $returnVar = NULL;
        $output = NULL;
        exec($command, $output, $returnVar);
    }
}
