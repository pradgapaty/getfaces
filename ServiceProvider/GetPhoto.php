<?php

require_once("/var/www/html/ParsePhoto/Helpers/LoaderHelper.php");

class GetPhoto
{
	private $logClassInst;
	private $siteUrl = "https://thispersondoesnotexist.com/image";
	private $localPhotoPath = "/home/pradgapaty/photosContent/photoStore/";
	private $localPhotoThumbPath = "/home/pradgapaty/photosContent/thumbStore/";

	public function __construct()
	{
		$this->logClassInst = new LogHelper();
		$this->parseThispersonClassInst = new ParseThisperson();
	}

	public function getPhoto(): string
	{
		$imageFile = file_get_contents($this->siteUrl);
		$fileHash = "";

		if (!empty($imageFile)) {
			$this->logClassInst->displayDebug("Info", "Successfully get photo from remote site");
			$time = time();
			$fileHash = md5($time . rand());
			$pathPrefix = $this->getPathPrefix($fileHash);

			if (!file_put_contents($this->localPhotoPath . $pathPrefix . "/" . $fileHash . ".jpg", $imageFile)) {
				$this->logClassInst->displayDebug("Error", "Cannot save photo locally");
			} else {
				$this->logClassInst->displayDebug("Info", "Successfully saved photo locally [" . $fileHash . "]");

				if (!chmod($this->localPhotoPath . $pathPrefix . "/" . $fileHash . ".jpg", 0777)) {
					$this->logClassInst->displayDebug("Error", "Cannot set permission for [" . $fileHash . "]");
				} else {
					$this->logClassInst->displayDebug("Info", "Successfully set permission for photo");
					$thumbResult = $this->generateThumbnail($this->localPhotoPath, $fileHash, $this->localPhotoThumbPath, 250, 250);

					if ($thumbResult) {
						$this->logClassInst->displayDebug("Info", "Successfully create thumbnail");

						$insertArr = [
							"photoId" => $fileHash,
							"photoHash" => md5($imageFile),
							"photoStatus" => 1,
							"date" => $time,
						];

						if ($this->parseThispersonClassInst->addPhotoData($insertArr)) {
							$this->logClassInst->displayDebug("Info", "Successfully add data to DB");
						}
					} else {
						$this->logClassInst->displayDebug("Error", "Cannot create thumbnail");
					}
				}
			}

		} else {
			$this->logClassInst->displayDebug("Error", "Cannot get photo content from the site");
		}

		return $fileHash;
	}

	public function generateThumbnail($localPhotoPath, $fileHash, $localPhotoThumbPath, $width, $height, $quality = 90) : bool {
		$status = false;
		$pathPrefix = $this->getPathPrefix($fileHash);
		$img = $localPhotoPath . $pathPrefix . "/" . $fileHash . ".jpg";

	    if (is_file($img)) {
	        $imagick = new Imagick(realpath($img));
	        $imagick->setImageFormat("jpeg");
	        $imagick->setImageCompression(Imagick::COMPRESSION_JPEG);
	        $imagick->setImageCompressionQuality($quality);
	        $imagick->thumbnailImage($width, $height, false, false);
	        if (file_put_contents($localPhotoThumbPath . $fileHash . "_thumb" . ".jpg", $imagick) === false) {
	            throw new Exception("Could not put contents.");
	        } else {
	        	$status = true;
	        }
	    }

	    return $status;
	}

	public function getPathPrefix(string $fileHash): string
	{
		$pathPrefix = "";

		if (!empty($fileHash)) {
			$pathPrefix = $fileHash[0];
		}

		return $pathPrefix;
	}
}
