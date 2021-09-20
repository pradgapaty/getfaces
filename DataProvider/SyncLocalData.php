<?php

declare(strict_types = 1);

namespace DataProvider;

use DataProvider\Photo;
use ServiceProvider\Config;
use ServiceProvider\Logger;

class SyncLocalData
{
    private $configParsingPath;
    private $photoProvider;

    private $prefixes = [
        "0",
        "1",
        "2",
        "3",
        "4",
        "5",
        "6",
        "7",
        "8",
        "9",
        "a",
        "b",
        "c",
        "d",
        "e",
        "f"
    ];

    function __construct()
    {
        if (is_null($this->photoProvider)) {
            $this->photoProvider = new Photo();
        }
    }

    public function syncFilesDb(): void
    {
        $pathSettings = Config::getPathSettings();

        foreach ($this->prefixes as $key => $pathPrefix) {
            $images = glob($pathSettings["localPhotoPath"] . $pathPrefix . "/*.jpg");

            if (!empty($images) && is_array($images)) {
                $time = time();
                $count = count($images);
    
                foreach($images as $image)
                {
                    Logger::displayDebug("Info", "Remaining [" . $pathPrefix . "]: " . $count);
                    $imageName = explode("/", $image)[6];
                    $fileHash = explode(".", $imageName)[0];
        
                    //Check and delete empty file
                    if (filesize($image) == 0) {
                        unlink($image);
                        $this->photoProvider->updateStatusByPhotoId($fileHash, Photo::getPhotoStatuses()["needDelete"]);
                        Logger::displayDebug("Info", "Empty image, will be deleted [" . $image . "]");
                        $count--;
                        continue;
                    } else {
                        $chmod = substr(sprintf("%o", fileperms($image)), -4);
    
                        if ((int)$chmod < 644) {
                            if (chmod($image, 0644)) {
                                Logger::displayDebug("Info", "Success change chmod [" . $image . "]");
                            } else {
                                Logger::displayDebug("Error", "Cannot change chmod [" . $image . "]");
                            }
                        }
                    }
    
                    //Check thumb exist
                    if (!file_exists($pathSettings["localPhotoThumbPath"] . $fileHash . "_thumb.jpg")) {
                        Logger::displayDebug("Info", "Missing thumb file [" . $fileHash . "]");
                        $thumbResult = $this->photoProvider->generateThumbnail($fileHash);
    
                        if ($thumbResult) {
                            Logger::displayDebug("Info", "Successfully create thumbnail");
                        } else {
                            Logger::displayDebug("Error", "Cannot create thumbnail [" . $fileHash . "]");
                        }
                    }
    
                    $rows = $this->photoProvider->checkPhotoExist($fileHash);
    
                    //Check and delete duplicate in the DB
                    if ($rows > 1) {
                        Logger::displayDebug("Info", "Duplicate detected [". $fileHash . "]");
                        $limit = $rows - 1;
                        if ($this->photoProvider->clearDuplicate($fileHash, $limit)) {
                            Logger::displayDebug("Info", "Duplicate deleted [". $fileHash . "]");
                        }
                    }
        
                    //Check for missing record
                    if ($rows === 0) {
                        $imageFile = file_get_contents($image);
                        $insertArr = [
                            "photoId" => $fileHash,
                            "photoHash" => md5($imageFile),
                            "photoStatus" => Photo::getPhotoStatuses()["needAnalyze"],
                            "date" => $time,
                        ];
    
                        if ($this->photoProvider->addPhotoData($insertArr)) {
                            Logger::displayDebug("Info", "Successfully add data to DB [" . $fileHash . "]");
                        }
                    }
        
                    //Check actual data
                    if ($rows === 1) {
                        $photoData = $this->photoProvider->getDataByPhotoId($fileHash);
        
                        //Delete marked photos (status 3)
                        if ($photoData["photoStatus"] == Photo::getPhotoStatuses()["needDelete"]) {
                            unlink($image);
                            $this->photoProvider->deleteRecordByPhotoId($fileHash);
                            Logger::displayDebug("Info", "Successfully delete image by status {3[needDelete]} [" . $fileHash . "]");
                        }
        
                        //Check photo hash
                        if (empty($photoData["photoHash"])) {
                            $imageFile = file_get_contents($image);
                            $photoHash = md5($imageFile);
        
                            if ($this->photoProvider->updateHashByPhotoId($fileHash, $photoHash)) {
                                Logger::displayDebug("Info", "Successfully update image hash [" . $fileHash . "]");
                            }
                        }
        
                        //Check is photo analyzed
                        if (empty($photoData["gender"]) || empty($photoData["age"])) {
                            if ($photoData["gender"] == 0 || $photoData["age"] == 0) {
                                $count--;
                                continue;
                            }
                            if ($this->photoProvider->updateStatusByPhotoId($fileHash, Photo::getPhotoStatuses()["needAnalyze"])) {
                                Logger::displayDebug("Info", "Set status {1} for analyze [" . $fileHash . "]");
                            }                        
                        }
        
                        //Update analyzed photo {status 2}
                        if (empty($photoData["photoStatus"]) && !empty($photoData["gender"]) && !empty($photoData["age"])) {
                            if ($this->photoProvider->updateStatusByPhotoId($fileHash, Photo::getPhotoStatuses()["analyzed"])) {
                                Logger::displayDebug("Info", "Successfully update status {2} [" . $fileHash . "]");
                            }                        
                        }
                    }
                    $count--;
                }
            }
 
        }

        Logger::displayDebug("Info", "Processed local photos. " . count($images) . " files");
    }

    public function syncDbFiles()
    {
        $pathSettings = Config::getPathSettings();
        $ids = $this->photoProvider->getAllPhotosIds();

        foreach ($ids as $idsKey => $id) {
            $pathPrefix = $this->photoProvider->getPhotoPrefix($id);

            if (!file_exists($pathSettings["localPhotoPath"] . $pathPrefix . "/" . $id . ".jpg")) {

                if ($this->photoProvider->deleteRecordByPhotoId($id)) {
                    Logger::displayDebug("Info", "Successfully delete record  [" . $id . "]");
                } else {
                    Logger::displayDebug("Error", "Cannot delete record from DB [" . $id . "]");
                }
            }
        }
    }
}
