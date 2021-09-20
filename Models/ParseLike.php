<?php

declare(strict_types = 1);

namespace Models;

use ServiceProvider\Database;

class ParseLike extends Database
{
    // CREATE TABLE `parseAnalyze` (
    //     `email` varchar(50) NOT NULL,
    //     `apiKey` varchar(32) NOT NULL,
    //     `apiSecret` varchar(32) NOT NULL,
    //     `expireDate` int NOT NULL DEFAULT '0',
    //     `expired` smallint DEFAULT '0',
    //     `emailPassword` varchar(32) NOT NULL DEFAULT '0'
    //   ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    function __construct()
    {
        parent::__construct("mysql");
    }

    /**
     * Check is photo liked from ip address
     *
     * @param  string $photoId
     * @param  string $ipAddr
     * @return bool
     */
    public function checkIsLiked(string $photoId, string $ipAddr): bool
    {
        $status = false;

        if (!empty($photoId) && !empty($ipAddr)) {
            $query = "SELECT INET_NTOA(ipAddr) as ipAddr FROM parseLike WHERE photoId = '" . $photoId . "'";
            $result = $this->mysqli->query($query);
            $ip = $result->fetch_assoc()['ipAddr'];
            if ($ipAddr == $ip) {
                $status = true;
            }
        }

        return $status;
    }

    /**
     * Mark photo is liked from ip
     *
     * @param  string $photoId
     * @param  string $ipAddr
     * @return bool
     */
    public function markIsLiked(string $photoId, string $ipAddr): bool
    {
        $insertStatus = false;

        if (!empty($photoId) && !empty($ipAddr)) {
            $query = "INSERT INTO parseLike (ipAddr, photoId) VALUES(INET_ATON('" . $ipAddr . "'), '" . $photoId . "')";
            $insertStatus = (bool) $this->mysqli->query($query);
        }
        
        return $insertStatus;
    }
}