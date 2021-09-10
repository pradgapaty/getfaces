<?php

require_once("/var/www/html/ParsePhoto/Helpers/LoaderHelper.php");

class SyncLocalData
{
	private $getPhotoClassInst = null;
	private $logClassInst = null;
	private $localPhotoPath = "/home/pradgapaty/photosContent/photoStore/";
	private $localPhotoThumbPath = "/home/pradgapaty/photosContent/thumbStore/";

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

	function __construct() {
		$this->getPhotoClassInst = new GetPhoto();
		$this->logClassInst = new LogHelper();
		$this->parseThispersonClassInst = new ParseThisperson();
	}

	public function syncFilesDb()
	{
		foreach ($this->prefixes as $key => $pathPrefix) {
			$images = glob($this->localPhotoPath . $pathPrefix . "/*.jpg");
	
			$time = time();
			$count = count($images);

			foreach($images as $image)
			{
				$this->logClassInst->displayDebug("Info", "Remaining: " . $count);
				$imageName = explode("/", $image)[6];

				$fileHash = explode(".", $imageName)[0];
	
				//Check and delete empty file
				if (filesize($image) == 0) {
					unlink($image);
					$this->parseThispersonClassInst->updatePhotoStatus($fileHash, 3);
					$this->logClassInst->displayDebug("Info", "Empty image, will be deleted [" . $image . "]");
					$count--;
					continue;
				} else {
					$chmod = substr(sprintf("%o",fileperms($image)),-4);

					if ((int)$chmod < 644) {
						if (chmod($image, 0644)) {
							$this->logClassInst->displayDebug("Info", "Success change chmod [" . $image . "]");
						} else {
							$this->logClassInst->displayDebug("Error", "Cannot change chmod [" . $image . "]");
						}
					}
				}

				//Check thumb exist
				if (!file_exists($this->localPhotoThumbPath . $fileHash . "_thumb.jpg")) {
					$this->logClassInst->displayDebug("Info", "Missing thumb file [" . $fileHash . "]");
					$thumbResult = $this->getPhotoClassInst->generateThumbnail($this->localPhotoPath, $fileHash, $this->localPhotoThumbPath, 250, 250);

					if ($thumbResult) {
						$this->logClassInst->displayDebug("Info", "Successfully create thumbnail");
					} else {
						$this->logClassInst->displayDebug("Error", "Cannot create thumbnail [" . $fileHash . "]");
					}
				}

				$rows = $this->parseThispersonClassInst->checkPhotoExist($fileHash);

				//Check and delete duplicate in the DB
				if ($rows > 1) {
					$this->logClassInst->displayDebug("Info", "Duplicate detected [". $fileHash . "]");
					$limit = $rows - 1;
					if ($this->parseThispersonClassInst->clearDuplicate($fileHash, $limit)) {
						$this->logClassInst->displayDebug("Info", "Duplicate deleted [". $fileHash . "]");
					}
				}
	
				//Check for missing record
				if ($rows === 0) {
					$imageFile = file_get_contents($image);
					$insertArr = [
						"photoId" => $fileHash,
						"photoHash" => md5($imageFile),
						"photoStatus" => 1,
						"date" => $time,
					];

					if ($this->parseThispersonClassInst->addPhotoData($insertArr)) {
						$this->logClassInst->displayDebug("Info", "Successfully add data to DB [" . $fileHash . "]");
					}
				}
	
				//Check actual data
				if ($rows === 1) {
					$photoData = $this->parseThispersonClassInst->getDataByPhotoId($fileHash);
	
					//Delete marked photos (status 3)
					if ($photoData["photoStatus"] == 3) {
						unlink($image);
						$this->parseThispersonClassInst->deleteRecordByPhotoId($fileHash);
						$this->logClassInst->displayDebug("Info", "Successfully delete image by status {3} [" . $fileHash . "]");
					}
	
					//Check photo hash
					if (empty($photoData["photoHash"])) {
						$imageFile = file_get_contents($image);
						$photoHash = md5($imageFile);
	
						if ($this->parseThispersonClassInst->updatePhotoHash($fileHash, $photoHash)) {
							$this->logClassInst->displayDebug("Info", "Successfully update image hash [" . $fileHash . "]");
						}
					}
	
					//Check is photo analyzed
					if (empty($photoData["gender"]) || empty($photoData["age"])) {
						if ($photoData["gender"] == 0 || $photoData["age"] == 0) {
							$count--;
							continue;
						}
						if ($this->parseThispersonClassInst->updatePhotoStatus($fileHash, 1)) {
							$this->logClassInst->displayDebug("Info", "Set status {1} for analyze [" . $fileHash . "]");
						}						
					}
	
					//Update analyzed photo {status 2}
					if (empty($photoData["photoStatus"]) && !empty($photoData["gender"]) && !empty($photoData["age"])) {
						if ($this->parseThispersonClassInst->updatePhotoStatus($fileHash, 2)) {
							$this->logClassInst->displayDebug("Info", "Successfully update status {2} [" . $fileHash . "]");
						}						
					}
				}

				$count--;
			}
		}


		$this->logClassInst->displayDebug("Info", "Processed local photos. " . count($images) . " files");
	}

	public function syncDbFiles()
	{
		$ids = $this->parseThispersonClassInst->getAllPhotoIds();

		foreach ($ids as $idsKey => $id) {
			$pathPrefix = $this->getPhotoClassInst->getPathPrefix($id);

			if (!file_exists($this->localPhotoPath . $pathPrefix . "/" . $id . ".jpg")) {
				$this->parseThispersonClassInst->deleteRecordByPhotoId($id);
				$this->logClassInst->displayDebug("Info", "Successfully delete record  [" . $id . "]");
			}
		}
	}
}
