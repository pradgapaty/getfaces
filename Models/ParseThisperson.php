<?php

declare(strict_types = 1);

namespace Models;

use ServiceProvider\Database;

class ParseThisperson extends Database
{
    
    // CREATE TABLE `parseThisperson` (
    //     `id` int NOT NULL AUTO_INCREMENT,
    //     `photoId` varchar(32) NOT NULL,
    //     `photoHash` varchar(32) NOT NULL,
    //     `photoStatus` smallint NOT NULL,
    //     `photoRatio` smallint NOT NULL DEFAULT '100',
    //     `gender` varchar(6) DEFAULT '0',
    //     `age` smallint DEFAULT '0',
    //     `date` int DEFAULT '0',
    //     PRIMARY KEY (`id`),
    //     UNIQUE KEY `photoId` (`photoId`)
    //   ) ENGINE=InnoDB AUTO_INCREMENT=199886 DEFAULT CHARSET=utf8;

    private $fields = [
        "id" => "i",
        "photoId" => "s",
        "photoHash" => "s",
        "photoStatus" => "i",
        "photoRatio" => "i",
        "gender" => "s",
        "age" => "i",
        "date" => "i",
    ];
    function __construct()
    {
        parent::__construct("mysql");
    }

    public function addPhotoData(array $insertArr): bool
    {
        $insertStatus = false;

        if (!empty($insertArr)) {
            $query = "INSERT INTO parseThisperson (photoId, photoHash, photoStatus, date) VALUES('" . $insertArr['photoId'] . "', '" . $insertArr['photoHash'] . "', " . $insertArr['photoStatus'] . ", " . $insertArr['date'] . ")";
            $insertStatus = (bool)$this->mysqli->query($query);
        }
        
        return $insertStatus;
    }

    public function checkPhotoExist(string $photoId): int
    {
        $rows = 0;

        if (!empty($photoId)) {
            $query = "SELECT photoId FROM parseThisperson WHERE photoId = '" . $photoId . "'";
            $result = $this->mysqli->query($query);
            if (isset($result->num_rows)) {
                $rows = $result->num_rows;
            }
        }

        return $rows;
    }

    public function getDataByPhotoId(string $photoId) : array
    {
        $photoData = [];

        if (!empty($photoId)) {
            $query = "SELECT * FROM parseThisperson WHERE photoId = '" . $photoId . "'";
            $res = $this->mysqli->query($query);

            if ($res !== false) {
                $result = $res->fetch_all(MYSQLI_ASSOC);
            }

            if (!empty($result[0])) {
                $photoData = $result[0];
            }
        }

        return $photoData;
    }

    /**
     * Get random photo
     *
     * @return array
     */
    public function getRandomPhoto() : string
    {
        $photoId = "";

        $queryMax = "SELECT max(id) as id FROM parseThisperson";
        $res = $this->mysqli->query($queryMax);
        $maxValue = $res->fetch_all(MYSQLI_ASSOC);

        if (!empty($maxValue[0]["id"])) {
            $maxValue = $maxValue[0]["id"];
            $randId = rand(1, $maxValue);
        }

        if ($randId > 0) {
            $query = "SELECT photoId FROM parseThisperson WHERE id > " . $randId . " LIMIT 1";
            $res = $this->mysqli->query($query);

            if ($res !== false) {
                $result = $res->fetch_all(MYSQLI_ASSOC);
            }

            if (!empty($result[0]["photoId"])) {
                $photoId = $result[0]["photoId"];
            }
        }

        return $photoId;
    }

    public function clearDuplicate(string $photoId, int $limit) : bool
    {
        $deleteStatus = false;

        if (!empty($photoId) && !empty($limit)) {
            $query = "DELETE FROM parseThisperson WHERE photoId = '" . $photoId . "' LIMIT " . $limit;
            $deleteStatus = (bool) $this->mysqli->query($query);
        }

        return $deleteStatus;
    }


    public function deleteRecordByPhotoId(string $photoId): bool
    {
        $deleteStatus = false;

        if (!empty($photoId)) {
            $query = "DELETE FROM parseThisperson WHERE photoId = '" . $photoId . "'";
            $deleteStatus = (bool) $this->mysqli->query($query);
        }

        return $deleteStatus;
    }

    public function getAllPhotosIds() : array
    {
        $photoIds = [];

        $query = "SELECT photoId FROM parseThisperson";
        $res = $this->mysqli->query($query);

        if ($res !== false) {
            $result = $res->fetch_all(MYSQLI_ASSOC);
        }

        if (!empty($result)) {
            $photoIds = array_column($result, "photoId");
        }

        return $photoIds;
    }

    public function getPhotosByFilter(array $filter) : array
    {
        $data = [];

        if (!empty($filter)) {
            $query = "SELECT photoId FROM parseThisperson WHERE gender = '" . $filter["gender"] . "' AND age BETWEEN '" . $filter["fromAge"] . "' AND '" . $filter["toAge"] . "'";

            if (!empty($filter["random"])) {
                $query .= " ORDER BY RAND()";
            }

            if (!empty($filter["limit"])) {
                $query .= " LIMIT " . $filter["limit"];
            }

            $result = $this->mysqli->query($query);
            

            if ($result !== false) {
                $result = $result->fetch_all(MYSQLI_ASSOC);
            }

            if (!empty($result) && !is_null($result)) {
                foreach ($result as $key => $value) {
                    array_push($data, $value['photoId']);
                }
            }
        }

        return $data;
    }

    public function getCountTotalPhotos(): int
    {
        $query = "SELECT count(*) as cnt FROM parseThisperson";
        $result = $this->mysqli->query($query);

        return (int) $result->fetch_assoc()['cnt'];
    }

    public function getPhotoDataForAnalysis(): array
    {
        $query = "SELECT photoId FROM parseThisperson WHERE photoStatus = 1 LIMIT 100";
        $result = $this->mysqli->query($query);

        $data=[];

        if ($result !== false) {
            $result = $result->fetch_all(MYSQLI_ASSOC);
        }

        if (!empty($result) && !is_null($result)) {
            foreach ($result as $key => $value) {
                array_push($data, $value['photoId']);
            }
        }

        return $data;
    }

    public function getTotalRecords() : int
    {
        $count = 0;

        $query = "SELECT count(*) as cnt FROM parseThisperson";
        $result = $this->mysqli->query($query);

        if ($result !== false) {
            $result = $result->fetch_all(MYSQLI_ASSOC);
            if (!empty($result[0]["cnt"])) {
                $count = (int) $result[0]["cnt"];
            }
        }

        return $count;
    }

    public function getCountByStatus(int $status): int
    {
        $count = 0;

        if (!empty($status)) {
            $query = "SELECT count(*) as cnt FROM parseThisperson WHERE photoStatus = " . $status;
            $result = $this->mysqli->query($query);
    
            if ($result !== false) {
                $result = $result->fetch_all(MYSQLI_ASSOC);
                if (!empty($result[0]["cnt"])) {
                    $count = (int) $result[0]["cnt"];
                }
            }
        }

        return $count;
    }

    public function getRatioByPhotoId(string $photoId): int
    {
        $ratio = 0;

        if (!empty($photoId)) {
            $query = "SELECT photoRatio FROM parseThisperson WHERE photoId = '" . $photoId . "'";
            $result = $this->mysqli->query($query);
            $ratio  = (int) $result->fetch_assoc()['photoRatio'];
        }

        return $ratio;
    }

    public function updatePhotoDataByPhotoId(array $updateData): bool
    {
        $result = false;

        if (!empty($updateData["photoId"])) {
                $query = "UPDATE parseThisperson SET gender = '" . $updateData["gender"] . "', age = " . $updateData["age"] . " WHERE photoId = '" . $updateData["photoId"] . "'";
                $result = (bool) $this->mysqli->query($query);
        }

        return $result;
    }

    public function updateStatusByPhotoId(string $photoId, int $status): bool
    {
        $result = false;

        if (!empty($photoId)) {
            $query = "UPDATE parseThisperson SET photoStatus = " . $status . " WHERE photoId = '" . $photoId . "'";
            $result = (bool) $this->mysqli->query($query);
        }

        return $result;
    }

    public function updateHashByPhotoId(string $photoId, string $hash): bool
    {
        $result = false;

        if (!empty($photoId) && !empty($hash)) {
            $query = "UPDATE parseThisperson SET photoHash = '" . $hash . "' WHERE photoId = '" . $photoId . "'";
            $result = (bool) $this->mysqli->query($query);
        }

        return $result;
    }

    public function updateRatioByPhotoId(string $photoId, int $ratio): bool
    {
        $result = false;

        if (!empty($photoId)) {
            $query = "UPDATE parseThisperson SET photoRatio = " . $ratio . " WHERE photoId = '" . $photoId . "'";
            $result = (bool) $this->mysqli->query($query);
        }

        return $result;
    }
    
}