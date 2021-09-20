<?php

declare(strict_types = 1);

require_once 'configs/loader.php';

use Helpers\Template;
use DataProvider\Photo;
use DataProvider\Statistics;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Index
{

    private $photoProvider = null;
    private $statisticsProvider = null;

    public function __construct()
    {
        if (is_null($this->photoProvider)) {
            $this->photoProvider = new Photo();
        }

        if (is_null($this->statisticsProvider)) {
            $this->statisticsProvider = new Statistics();
        }
    }

    public function initIndex(): void
    {
        $ageArr = [];
        $loader = new FilesystemLoader(__DIR__ . '/templates');
        $twig = new Environment($loader);

        if (!empty($_POST["age"])) {
            $ageArr = Template::separateAge($_POST["age"]);
        }

        $templateData = [
        "totalPhotos" => $this->statisticsProvider->getCountTotalPhotos(),
        "filter" => $_POST,
        "genderList" => Template::getGenderList(),
        "ageList" => Template::getAgeList(),
        "limitList" =>Template::getLimitList(),
        "photoData" => $this->getPhotoData($_POST),
        "ageData" => $ageArr,
        "currentDate" => date("Y"),
        "menuList" => Template::menuList(),
        "page" => "home",
        "ipAddress" => $_SERVER["REMOTE_ADDR"],
        ];

        echo $twig->render('/html/index.html', $templateData);
    }

    private function getPhotoData(array $postParams): array
    {
        $photoData = [];

        if (!empty($postParams['submit'])) {

            $ageArr = Template::separateAge($postParams['age']);

            $filter = [
            'gender' => $postParams['gender'],
            'fromAge' => $ageArr["from"],
            'toAge' => $ageArr["to"],
            'limit' => $postParams['limit'],
            'random' => $postParams['random'],
            ];

            $res = $this->photoProvider->getPhotosByFilter($filter);

            if (!empty($res)) {
                foreach ($res as $resKey => $resValue) {
                    $photoData[$resKey]["photoId"] = $resValue;
                    $photoData[$resKey]["imgUrl"] = $this->photoProvider->generateImageUrl($resValue);
                    $photoData[$resKey]["imgThumbUrl"] = $this->photoProvider->generateImageThumbUrl($resValue);
                }
            }
        }

        return $photoData;
    }
}

$classInst = new Index();
$classInst->initIndex();
