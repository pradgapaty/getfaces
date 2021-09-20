<?php

declare(strict_types = 1);

namespace DataProvider;

use ServiceProvider\Logger;
use ServiceProvider\Curl;
use Models\ParseAnalysis;
use Symfony\Component\Yaml\Yaml;
use Exception;

class Analysis
{
    private $parseAnalysisModel;
    private $curlServiceProvider;
    private $photoProvider;
    private $configAnalysis;

    function __construct()
    {
        $path = dirname(dirname(__FILE__));
        $filePath = $path . '/configs/general.yaml';
        $config = Yaml::parseFile($filePath);

        if (!empty($config["analysisSettings"])) {
            $this->configAnalysis = $config["analysisSettings"];
        } else {
            throw new Exception("Error loading [analysisSettings] config params");
        }

        if (is_null($this->photoProvider)) {
            $this->photoProvider = new Photo();
        }

        if (is_null($this->parseAnalysisModel)) {
            $this->parseAnalysisModel = new ParseAnalysis();
        }

        if (is_null($this->curlServiceProvider)) {
            $this->curlServiceProvider = new Curl();
        }
    }

    public function analysisPhoto(): bool
    {
        $analyzeStatus = true;
        $idsForAnalysis = $this->photoProvider->getPhotoDataForAnalysis();

        if (!empty($idsForAnalysis)) {
            Logger::displayDebug("Info", "Founded [" . count($idsForAnalysis) . "] photos for analyzed");
            $access = $this->checkAnalysisServiceLimit();

            if (empty($access["apiKey"]) || empty($access["apiSecret"])) {
                Logger::displayDebug("Error", "All accounts have expired limit");
                exit;
            }

            foreach ($idsForAnalysis as $idKey => $idValue) {
                $outputData = [];
                $status = "";
                $gender = "";
                $message = "Unknown error";
                $age = "";
                $resetTime = "";
                $remaining = "";

                $pathPrefix = $this->photoProvider->getPhotoPrefix($idValue);
                $apiUrl = $this->configAnalysis["apiUrl"] . $access["apiKey"] . "&api_secret=" . $access["apiSecret"] . "&urls=" . $this->configAnalysis["serverUrl"] . $pathPrefix . "/" . $idValue . ".jpg&attributes=all";
                $res = $this->curlServiceProvider->callCurl($apiUrl);
                $result = json_decode($res);

                if (isset($result->status)) {
                    $status = $result->status;
                }

                if (isset($result->error_message)) {
                    $message = $result->error_message;
                }
                if ($status !== "success") {
                    Logger::displayDebug("Error", "API returned FAIL result " . $message);
                    $analyzeStatus += false;
                    if (strpos($message, "quota") !== false) {
                        Logger::displayDebug("Info", "API account limit reached, account will be marked {expired} for one week");
                        $this->parseAnalysisModel->setExpiredByApiKey($access["apiKey"]);
                    }
                    exit;
                } else {
                    if (isset($result->photos[0]->tags[0]->attributes->gender->value)) {
                        $gender = $result->photos[0]->tags[0]->attributes->gender->value;
                    }

                    if (isset($result->photos[0]->tags[0]->attributes->age_est->value)) {
                        $age = $result->photos[0]->tags[0]->attributes->age_est->value;
                    }

                    if (isset($result->usage->remaining)) {
                        $remaining = $result->usage->remaining;
                    }

                    $updateData = [
                        "photoId" => $idValue,
                        "gender" => $gender,
                        "age" => $age,
                    ];

                    if (!empty($updateData["gender"])) {
                        if ($this->photoProvider->updatePhotoDataByPhotoId($updateData)
                            && $this->photoProvider->updateStatusByPhotoId($idValue, Photo::getPhotoStatuses()["analyzed"])
                        ) {
                            Logger::displayDebug("Info", "Successfully update data in the DB [" . $idValue . "]");
                            Logger::displayDebug("Info", "Remaining API requests [" . $remaining . "]");
                        } else {
                            Logger::displayDebug("Error", "Cannot update data in the DB [" . $idValue . "]");
                        }
                    } else {
                        //add photo to queue for delete
                        Logger::displayDebug("Info", "Cannot get gender [" . $idValue . "]");
                        $this->photoProvider->updateStatusByPhotoId($idValue, Photo::getPhotoStatuses()["needDelete"]);
                    }

                    if ($remaining < 2) {
                        Logger::displayDebug("Info", "Hourly API account limit reached");
                        exit;
                    }
                }

                sleep($this->configAnalysis["analysisDelay"]);
            }
        }

        return $analyzeStatus;
    }

    private function checkAnalysisServiceLimit(): array
    {
        $serviceProfileData = [];
        $accessData = $this->parseAnalysisModel->getAccessData();

        if (!empty($accessData)) {
            foreach ($accessData as $accessKey => $accessValue) {
                if ($accessValue["expired"] == 1) {
                    if ($accessValue["expireDate"] < time() - 7 * 24 * 3600 && !empty($accessValue["email"])) {
                        $unlock = $this->parseAnalysisModel->setExpireStatusByEmail(0, $accessValue["email"]);
                        if ($unlock) {
                            Logger::displayDebug("Info", "Unlock account [" . $accessValue["email"] . "]");
                        }
                    }
                    continue;
                } else {
                    $url = $this->configAnalysis["serviceLimitsUrl"] . $accessValue["apiKey"] . "&api_secret=" . $accessValue["apiSecret"];
                    $res = simplexml_load_string($this->curlServiceProvider->callCurl($url));

                    if ($res->status == "success") {
                        $json = json_encode($res);

                        if (is_string($json)) {
                            $array = json_decode($json, true);
                            $remaining = (int) $array["usage"]["remaining"];
    
                            if ($remaining > 1) {
                                Logger::displayDebug("Info", "Using account for analyze [" . $accessValue["email"] . "]");
                                $serviceProfileData = [
                                    "apiKey" => $accessValue["apiKey"],
                                    "apiSecret" => $accessValue["apiSecret"],
                                ];
                                break;
                            }
                        }
                    }
                }
            }
        }

        return $serviceProfileData;
    }
}
