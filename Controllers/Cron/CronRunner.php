<?php

declare(strict_types = 1);

require_once 'configs/loader.php';

use ServiceProvider\Config;
use ServiceProvider\Logger;
use DataProvider\Photo;
use DataProvider\Analysis;
use DataProvider\SyncLocalData;

if (!empty($argv[1])) {
    $action = $argv[1];
} else {
    $action = "";
}
$runner = new Runner();

switch ($action) {
case "downloadPhoto":
    $runner->downloadPhoto();        
    break;
case "analysisPhoto":
    $runner->analysisPhoto();
    break;
case "syncLocalData":
    $runner->syncLocalData();
    break;
default:
    Logger::displayDebug("Error", "Incorrect argument");
    break;
}

class Runner
{

    /**
     * Run get photo method
     *
     * @return void
     */
    public function downloadPhoto(): void
    {
        $settings = Config::getParsingSettings();

        if ($settings["enableParsing"]) {
            $photoProvider = new Photo();

            for ($i = 0; $i < $settings["parsingLimit"]; $i++) {
                $photoProvider->downloadPhoto();
                sleep($settings["parsingDelay"]);
                print_r("[" . $i . "] \n");
            }
        } else {
            Logger::displayDebug("Info", "Parsing disable in the settings");
        }
    }

    /**
     * Run analysis photo method
     *
     * @return void
     */
    public function analysisPhoto(): void
    {
        $settings = Config::getAnalysisSettings();

        if ($settings["enableAnalysis"]) {
            $analysisProvider = new Analysis();
            $analysisProvider->analysisPhoto();
        } else {
            Logger::displayDebug("Info", "Analysis disable in the settings");
        }
    }

    /**
     * Run sync local data
     *
     * @return void
     */
    public function syncLocalData(): void
    {
        $sync = new SyncLocalData();
        $sync->syncFilesDb();
        $sync->syncDbFiles();
    }
}

