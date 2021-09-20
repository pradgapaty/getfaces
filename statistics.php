<?php

declare(strict_types = 1);

require_once 'configs/loader.php';

use DataProvider\Statistics;
use Helpers\Template;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Stats
{

    private $statisticsProvider;

    public function __construct()
    {
        if (is_null($this->statisticsProvider)) {
            $this->statisticsProvider = new Statistics();
        }
    }

    public function initStatistics(): void
    {
        $loader = new FilesystemLoader(__DIR__ . '/templates');
        $twig = new Environment($loader);

        $templateData = [
        "totalPhotos" => $this->statisticsProvider->getCountTotalPhotos(),
        "neededAnalyze" => $this->statisticsProvider->getCountNeededAnalyzePhotos(),
        "analyzed" => $this->statisticsProvider->getCountAnalyzedPhotos(),
        "activeAnalyzators" => $this->statisticsProvider->getActiveAnalyzatorsCount(),
        "currentDate" => date("Y"),
        "menuList" => Template::menuList(),
        "page" => "stats",
        ];

        echo $twig->render('/html/statistics.html', $templateData);
    }
}

$classInst = new Stats();
$classInst->initStatistics();




