<?php

$path = dirname(dirname(__FILE__));
require_once $path . '/composer/vendor/autoload.php';

use DataProvider\Photo;
use DataProvider\Like;

$photoProvider = new Photo();
$likeProvider = new Like();

$photoId = $_POST["photoId"];
$action = $_POST["action"];
$ipAddress = $_POST["ip"];
$response = "";

if (!empty($photoId) && !empty($action)) {
    switch ($action) {
    case 'like':
        if (!$likeProvider->checkIsLiked($photoId, $ipAddress)) {
            $currentRatio = $photoProvider->getRatioByPhotoId($photoId);
            $currentRatio++;
            $photoProvider->updateRatioByPhotoId($photoId, $currentRatio);
            $likeProvider->markIsLiked($photoId, $ipAddress);
            $response = "Liked";
        } else {
            $response = "You already voted for this photo";
        }
        break;
    case 'dislike':
        if (!$likeProvider->checkIsLiked($photoId, $ipAddress)) {
            $currentRatio = $photoProvider->getRatioByPhotoId($photoId);
            $currentRatio--;
            $photoProvider->updateRatioByPhotoId($photoId, $currentRatio);
            $likeProvider->markIsLiked($photoId, $ipAddress);
            $response = "Disliked";
        } else {
            $response = "You already voted for this photo";
        }
        break;        
    default:
        $response = "Ha-ha!";
        break;
    }
}

echo json_encode($response);