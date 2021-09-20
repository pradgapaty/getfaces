<?php

declare(strict_types = 1);

namespace DataProvider;

use Models\ParseThisperson;
use ServiceProvider\Config;
use ServiceProvider\Logger;
use Exception;
use Imagick;

class Photo
{
    private $photoThispersonModel;

    public function __construct()
    {
        if (is_null($this->photoThispersonModel)) {
            $this->photoThispersonModel = new ParseThisperson();
        }
    }

    public function getPhotosByFilter(array $filter): array
    {
        $photoData = [];

        if (!empty($filter)) {
            $photoData = $this->photoThispersonModel->getPhotosByFilter($filter);
        }

        return $photoData;        
    }

    public function getDataByPhotoId(string $photoId) : array
    {
        $photoData = [];

        if (!empty($photoId)) {
            $photoData = $this->photoThispersonModel->getDataByPhotoId($photoId);
        }

        return $photoData;
    }

    public function getPhotoPrefix(string $photoId): string
    {
        $pathPrefix = "";

        if (!empty($photoId)) {
            $pathPrefix = $photoId[0];
        }

        return $pathPrefix;
    }

    public function generateImageUrl(string $photoId): string
    {
        $imgUrl = "";

        if (!empty($photoId)) {
            $pathPrefix = $this->getPhotoPrefix($photoId);
            $imgUrl = "http://" . $_SERVER['HTTP_HOST']  . "/akr/photosContent/photoStore/" . $pathPrefix . "/" . $photoId . ".jpg";
        }

        return $imgUrl;
    }

    public function generateImageThumbUrl(string $photoId): string
    {
        $imgThumbUrl = "";

        if (!empty($photoId)) {
            $imgThumbUrl = "http://" . $_SERVER['HTTP_HOST'] ."/akr/photosContent/thumbStore/" . $photoId . "_thumb.jpg";
        }

        return $imgThumbUrl;
    }

    public function getRandomThumb(): string
    {
        return $this->generateImageThumbUrl($this->photoThispersonModel->getRandomPhoto());
    }

    public function downloadPhoto(): string
    {
        $settings = Config::getPathSettings();
        $imageFile = file_get_contents($settings["parsingUrl"]);
        $fileHash = "";

        if (!empty($imageFile)) {
            Logger::displayDebug("Info", "Successfully get photo from remote site");
            $time = time();
            $fileHash = md5($time . rand());
            $pathPrefix = $this->getPhotoPrefix($fileHash);

            if (!file_put_contents($settings["localPhotoPath"] . $pathPrefix . "/" . $fileHash . ".jpg", $imageFile)) {
                Logger::displayDebug("Error", "Cannot save photo locally");
            } else {
                Logger::displayDebug("Info", "Successfully saved photo locally [" . $fileHash . "]");

                if (!chmod($settings["localPhotoPath"] . $pathPrefix . "/" . $fileHash . ".jpg", 0777)) {
                    Logger::displayDebug("Error", "Cannot set permission for [" . $fileHash . "]");
                } else {
                    Logger::displayDebug("Info", "Successfully set permission for photo");

                    if ($this->generateThumbnail($fileHash)) {
                        Logger::displayDebug("Info", "Successfully create thumbnail");

                        $insertArr = [
                         "photoId" => $fileHash,
                         "photoHash" => md5($imageFile),
                         "photoStatus" => self::getPhotoStatuses()["needAnalyze"],
                         "date" => $time,
                        ];

                        if ($this->addPhotoData($insertArr)) {
                            Logger::displayDebug("Info", "Successfully add data to DB");
                        }
                    } else {
                        Logger::displayDebug("Error", "Cannot create thumbnail");
                    }
                }
            }

        } else {
            Logger::displayDebug("Error", "Cannot get photo content from the site");
        }

        return $fileHash;
    }

    public function addPhotoData(array $addData): bool
    {
        $result = false;

        if (!empty($addData)) {
            $result = (bool) $this->photoThispersonModel->addPhotoData($addData);
        }

        return $result;
    }

    public function generateThumbnail(string $fileHash): bool
    {
        $pathSettings = Config::getPathSettings();
        $photoSettings = Config::getPhotoSettings();
        $status = false;
        $pathPrefix = $this->getPhotoPrefix($fileHash);
        $img = $pathSettings["localPhotoPath"] . $pathPrefix . "/" . $fileHash . ".jpg";

        if (is_file($img)) {
            $imagick = new Imagick(realpath($img));
            $imagick->setImageFormat("jpeg");
            $imagick->setImageCompression(Imagick::COMPRESSION_JPEG);
            $imagick->setImageCompressionQuality($photoSettings["quality"]);
            $imagick->thumbnailImage($photoSettings["thumbWidth"], $photoSettings["thumbHeight"], false, false);

            if (file_put_contents($pathSettings["localPhotoThumbPath"] . $fileHash . "_thumb" . ".jpg", $imagick) === false) {
                throw new Exception("Cannot save thumbnail image");
            } else {
                $status = true;
            }
        }

        return $status;
    }

    public static function getPhotoStatuses(): array
    {
        return [
            "withoutStatus" => 0,
            "needAnalyze" => 1,
            "analyzed" => 2,
            "needDelete" => 3
        ];
    }

    public function getPhotoDataForAnalysis(): array
    {
        return $this->photoThispersonModel->getPhotoDataForAnalysis();
    }

    public function updatePhotoDataByPhotoId(array $updateData): bool
    {
        $result = false;

        if (!empty($updateData["photoId"])) {
            $result = $this->photoThispersonModel->updatePhotoDataByPhotoId($updateData);
        }

        return $result;
    }

    public function updateStatusByPhotoId(string $photoId, int $status): bool
    {
        $result = false;

        if (!empty($photoId)) {
            $result = $this->photoThispersonModel->updateStatusByPhotoId($photoId, $status);
        }

        return $result;
    }

    public function getAllPhotosIds(): array
    {
        return $this->photoThispersonModel->getAllPhotosIds();
    }

    public function deleteRecordByPhotoId(string $photoId): bool
    {
        $result = false;

        if (!empty($photoId)) {
            $result = $this->photoThispersonModel->deleteRecordByPhotoId($photoId);
        }

        return $result;
    }

    public function checkPhotoExist(string $photoId): int
    {
        $result = 0;

        if (!empty($photoId)) {
            $result = $this->photoThispersonModel->checkPhotoExist($photoId);
        }

        return $result;
    }

    public function clearDuplicate(string $photoId, int $limit) : bool
    {
        $result = false;

        if (!empty($photoId) && !empty($limit)) {
            $result = $this->photoThispersonModel->clearDuplicate($photoId, $limit);
        }

        return $result;
    }

    public function updateHashByPhotoId(string $photoId, string $hash): bool
    {
        $result = false;

        if (!empty($photoId) && !empty($hash)) {
            $result = $this->photoThispersonModel->updateHashByPhotoId($photoId, $hash);
        }

        return $result;
    }

    public function getRatioByPhotoId(string $photoId): int
    {
        $ratio = 0;

        if (!empty($photoId)) {
            $ratio = $this->photoThispersonModel->getRatioByPhotoId($photoId);
        }

        return $ratio;
    }

    public function updateRatioByPhotoId(string $photoId, int $ratio): bool
    {
        $result = false;

        if (!empty($photoId)) {
            $result = $this->photoThispersonModel->updateRatioByPhotoId($photoId, $ratio);
        }

        return $result;
    }
}