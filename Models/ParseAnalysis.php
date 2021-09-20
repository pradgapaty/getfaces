<?php

namespace Models;

use ServiceProvider\Database;

class ParseAnalysis extends Database
{
    
    // CREATE TABLE `parseThisperson` (
    //   `photoId` varchar(32) NOT NULL,
    //   `photoHash` varchar(32) NOT NULL,
    //   `photoStatus` smallint(1) NOT NULL,
    //   `gender` varchar(10) NOT NULL,
    //   `age` smallint(3) NOT NULL,
    //   `date` int(11) NOT NULL DEFAULT '0'
    // ) ENGINE=MyISAM DEFAULT CHARSET=utf8

    function __construct()
    {
        parent::__construct("mysql");
    }

    public function getActiveAnalyzatorsCount() : int
    {
        $count = 0;

        $query = "SELECT count(*) as cnt FROM parseAnalyze WHERE expired = 0";
        $result = $this->mysqli->query($query);

        if ($result !== false) {
            $result = $result->fetch_all(MYSQLI_ASSOC);
            if (!empty($result[0]["cnt"])) {
                $count = (int) $result[0]["cnt"];
            }
        }

        return $count;
    }

    /**
     * Get apiKey and apiAccess with expired status 0
     *
     * @return void
     */
    public function getAccessData() : array
    {
        $accessData = [];

        $query = "SELECT * FROM parseAnalyze";
        $result = $this->mysqli->query($query);

        if ($result !== false) {
            $result = $result->fetch_all(MYSQLI_ASSOC);
            if (!empty($result)) {
                $accessData = $result;
            }
        }

        return $accessData;
    }

    /**
     * Set expired account by API key for one week
     *
     * @param  string $apiKey
     * @return bool
     */
    public function setExpiredByApiKey(string $apiKey) : bool
    {
        $updateStatus = false;
        $expireDate = time() + 7 * 24 * 3600;

        $query = "UPDATE parseAnalyze SET expired = 1, expireDate = " . $expireDate . " WHERE apiKey = '" . $apiKey . "'";
        $updateStatus = (bool) $this->mysqli->query($query);

        return $updateStatus;
    }

    /**
     * Set expire status by email
     *
     * @param  int    $status
     * @param  string $email
     * @return bool
     */
    public function setExpireStatusByEmail(int $status, string $email) : bool
    {
        $updateStatus = false;

        if (!empty($email)) {
            $query = "UPDATE parseAnalyze SET expired = " . $status . " WHERE email = '" . $email . "'";
            $updateStatus = (bool) $this->mysqli->query($query);
        }

        return $updateStatus;
    }
}