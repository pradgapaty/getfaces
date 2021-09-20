<?php

declare(strict_types = 1);

require_once 'configs/loader.php';

use DataProvider\Photo;
use Helpers\TemplateHelper;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class About
{

    public function initAbout(): void
    {
        $loader = new FilesystemLoader(__DIR__ . '/templates');
        $twig = new Environment($loader);
        $photoProvider = new Photo();

        $templateData = [
        "randomThumbUrl" => $photoProvider->getRandomThumb(),
        "currentDate" => date("Y"),
        "menuList" => TemplateHelper::menuList(),
        "page" => "about",
        ];

        echo $twig->render('/html/about.html', $templateData);
    }
}

$classInst = new About();
$classInst->initAbout();