<?php

declare(strict_types = 1);

namespace DataProvider;

use Models\ParseLike;

class like
{
    private $likeModel;

    public function __construct()
    {
        if (is_null($this->likeModel)) {
            $this->likeModel = new ParseLike();
        }
    }

    public function checkIsLiked(string $photoId, string $ipAddress): bool
    {
        $result = false;

        if (!empty($photoId) && !empty($ipAddress)) {
            $result = $this->likeModel->checkIsLiked($photoId, $ipAddress);
        }

        return $result;
    }

    public function markIsLiked(string $photoId, string $ipAddress): bool
    {
        $result = false;

        if (!empty($photoId) && !empty($ipAddress)) {
            $result = $this->likeModel->markIsLiked($photoId, $ipAddress);
        }

        return $result;
    }
}