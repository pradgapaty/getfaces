<?php

require_once("/var/www/html/ParsePhoto/Helpers/LoaderHelper.php");

class AnalyzePhoto
{
	private $analyzeDelay = 30;
	private $getPhotoClassInst = null;
	private $parseThispersonClassInst = null;
	private $parseAnalyzeClassInst = null;
	private $analyzeAccessSql = null;
	private $logClassInst = null;

	function __construct() {
		if (is_null($this->getPhotoClassInst)) {
			$this->getPhotoClassInst = new GetPhoto();
		}

		if (is_null($this->parseThispersonClassInst)) {
			$this->parseThispersonClassInst = new ParseThisperson();
		}

		if (is_null($this->parseAnalyzeClassInst)) {
			$this->parseAnalyzeClassInst = new ParseAnalyze();
		}

		if (is_null($this->logClassInst)) {
			$this->logClassInst = new LogHelper();
		}
	}

	public function photoAnalyze(): bool
	{
		$analyzeStatus = true;
		$idsForAnalyze = $this->parseThispersonClassInst->getPhotoDataForAnalyze();

		//check expire account and limits.
		//expired 0 - active account
		//expired 1 - inactive account
		$access = $this->checkLimit();

		if (empty($access["apiKey"]) || empty($access["apiSecret"])) {
			$this->getPhotoClass->displayDebug("Error", "All accounts have expired limit");
			exit;
		}

		foreach ($idsForAnalyze as $idsKey => $idsValue) {
			$outputData = [];
			$status = "";
			$gender = "";
			$message = "Unknown error";
			$age = "";
			$resetTime = "";
			$remaining = "";

			$pathPrefix = $this->getPhotoClassInst->getPathPrefix($idsValue);

			$apiUrl = "http://api.skybiometry.com/fc/faces/detect.json?api_key=" . $access["apiKey"] . "&api_secret=" . $access["apiSecret"] . "&urls=" . "https://getfaces.ml/photoStore/" . $pathPrefix . "/" . $idsValue . ".jpg&attributes=all";

			$res = $this->callCurl($apiUrl);
			$result = json_decode($res);

			if (isset($result->status)) {
				$status = $result->status;
			}

			if (isset($result->error_message)) {
				$message = $result->error_message;
			}
			if ($status !== "success") {
				$this->logClassInst->displayDebug("Error", "API returned FAIL result " . $message);
				$analyzeStatus += false;
				if (strpos($message, "quota") !== false) {
				    $this->logClassInst->displayDebug("Info", "API account limit reached, account will be marked {expired} for one week");
					$this->parseAnalyzeClassInst->setExpiredByApiKey($access["apiKey"]);
				}
				exit;
			} else {
				if (isset($result->photos[0]->tags[0]->attributes->gender->value)) {
					$gender = $result->photos[0]->tags[0]->attributes->gender->value;
				}

				if (isset($result->photos[0]->tags[0]->attributes->age_est->value)) {
					$age = $result->photos[0]->tags[0]->attributes->age_est->value;
				}

				if (isset($result->usage->reset_time)) {
					$resetTime = $result->usage->reset_time;
				}

				if (isset($result->usage->remaining)) {
					$remaining = $result->usage->remaining;
				}

				$outputData = [
					"photoId" => $idsValue,
					"gender" => $gender,
					"age" => $age,
					"resetTime" => $result->usage->reset_time,
					"remaining" => $result->usage->remaining,
				];

				if (!empty($outputData["gender"])) {
					if ($this->parseThispersonClassInst->updatePhotoDataAfterAnalyze($outputData)) {
						$this->logClassInst->displayDebug("Info", "Successfully update data in the DB [" . $outputData["photoId"] . "]");
						$this->logClassInst->displayDebug("Info", "Remaining API requests [" . $outputData["remaining"] . "]");
					} else {
						$this->logClassInst->displayDebug("Error", "Cannot update data in the DB [" . $outputData["photoId"] . "]");
					}
				} else {
					//add photo to queue for delete
					$this->logClassInst->displayDebug("Info", "Cannot get gender [" . $outputData["photoId"] . "]");
					$this->parseThispersonClassInst->updatePhotoStatus($outputData["photoId"], 3);
				}

				if ($outputData["remaining"] < 2) {
					$this->logClassInst->displayDebug("Info", "Hourly API account limit reached");
					exit;
				}
			}

			sleep($this->analyzeDelay);
		}

		return $analyzeStatus;
	}

	private function callCurl(string $url)
	{
		$curl = curl_init();

		curl_setopt_array($curl, [
		    CURLOPT_RETURNTRANSFER => 1,
		    CURLOPT_URL => $url,
		]);

		$res = curl_exec($curl);

		return $res;
	}

	private function checkLimit() : array
	{
		$returnData = [];
		$accessData = $this->parseAnalyzeClassInst->getAccessData();

		if (!empty($accessData)) {
			foreach ($accessData as $accessKey => $accessValue) {
				if ($accessValue["expired"] == 1) {
					if ($accessValue["expireDate"] < time() - 7 * 24 * 3600 && !empty($accessValue["email"])) {
						$unlock = $this->parseAnalyzeClassInst->setExpireStatusByEmail(0, $accessValue["email"]);
						if ($unlock) {
							$this->logClassInst->displayDebug("Info", "Unlock account [" . $accessValue["email"] . "]");
						}
					}
					continue;
				} else {
					$url = "http://api.skybiometry.com/fc/account/limits.xml?api_key=" . $accessValue["apiKey"] . "&api_secret=" . $accessValue["apiSecret"];
					$res = simplexml_load_string($this->callCurl($url));
					if ($res->status == "success") {
						$json = json_encode($res);
						$array = json_decode($json, TRUE);
						$remaining = (int) $array["usage"]["remaining"];

						if ($remaining > 1) {
							$this->logClassInst->displayDebug("Info", "Using account for analyze [" . $accessValue["email"] . "]");
							$returnData = [
								"apiKey" => $accessValue["apiKey"],
								"apiSecret" => $accessValue["apiSecret"],
							];
							break;
						}
					}
				}
			}
		}

		return $returnData;
	}
}
