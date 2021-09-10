<?php

require_once("/var/www/html/ParsePhoto/Helpers/LoaderHelper.php");

$parseThispersonClassInst = new ParseThisperson();
$parseLikeClassInst = new ParseLike();

$photoId = $_POST["photoId"];
$action = $_POST["action"];
$ipAddress = $_POST["ip"];
$response = "";

if (!empty($photoId) && !empty($action)) {
	switch ($action) {
		case 'like':
			if (!$parseLikeClassInst->checkIsLiked($photoId, $ipAddress)) {
				$currentRatio = (int) $parseThispersonClassInst->getRatioByPhotoId($photoId);
				$currentRatio++;
				$parseThispersonClassInst->updateRatioByPhotoId($photoId, $currentRatio);
				$parseLikeClassInst->markIsLiked($photoId, $ipAddress);
			} else {
				$response = "You already voted this photo";
			}
			break;
		case 'dislike':
			if (!$parseLikeClassInst->checkIsLiked($photoId, $ipAddress)) {
				$currentRatio = (int) $likeSql->getRatioByPhotoId($photoId);
				$currentRatio--;
				$parseThispersonClassInst->updateRatioByPhotoId($photoId, $currentRatio);
				$parseLikeClassInst->markIsLiked($photoId, $ipAddress);
			} else {
				$response = "You already voted this photo";
			}
			break;		
		default:
			$response = "Ha-ha!";
			break;
	}
}

echo json_encode($response);