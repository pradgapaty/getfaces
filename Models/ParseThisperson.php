<?php

require_once("/var/www/html/ParsePhoto/Helpers/MysqlConnect.php");

class ParseThisperson extends MysqlConnect {
	
	// CREATE TABLE `parseThisperson` (
	//   `photoId` varchar(32) NOT NULL,
	//   `photoHash` varchar(32) NOT NULL,
	//   `photoStatus` smallint(1) NOT NULL,
	//   `gender` varchar(10) NOT NULL,
	//   `age` smallint(3) NOT NULL,
	//   `date` int(11) NOT NULL DEFAULT '0'
	// ) ENGINE=MyISAM DEFAULT CHARSET=utf8

	function __construct() {
		parent::__construct();
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

	public function checkPhotoExist(string $photoId) : int
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

			if ($res !== FALSE){
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
	public function getRandomPhoto() : array
	{
		$photoData = [];

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

			if ($res !== FALSE){
				$result = $res->fetch_all(MYSQLI_ASSOC);
			}

			foreach ($result as $resultKey => $resultValue) {
				if (!empty($resultValue["photoId"])) {
					$photoData[] = $resultValue["photoId"];
				}
			}
		}

		return $photoData;
	}

	public function clearDuplicate(string $photoId, int $limit) : bool
	{
		$deleteStatus = false;

		if (!empty($photoId) && !empty($limit)) {
			$query = "DELETE FROM parseThisperson WHERE photoId = '" . $photoId . "' LIMIT " . $limit;
			$result = $this->mysqli->query($query);
			$deleteStatus = true;
		}

		return $deleteStatus;
	}

	public function updatePhotoHash(string $photoId, string $photoHash) : bool
	{
		$updateStatus = false;

		if (!empty($photoId) && !empty($photoHash)) {
			$query = "UPDATE parseThisperson SET photoHash = '" . $photoHash . "' WHERE photoId = '" . $photoId . "'";
			$updateStatus = (bool) $this->mysqli->query($query);
		}

		return $updateStatus;
	}

	public function updatePhotoStatus(string $photoId, int $photoStatus) : bool
	{
		$updateStatus = false;

		if (!empty($photoId)) {
			$query = "UPDATE parseThisperson SET photoStatus = '" . $photoStatus . "' WHERE photoId = '" . $photoId . "'";
			$updateStatus = (bool) $this->mysqli->query($query);
		}

		return $updateStatus;
	}

	public function deleteRecordByPhotoId(string $photoId) : bool
	{
		$deleteStatus = false;

		if (!empty($photoId)) {
			$query = "DELETE FROM parseThisperson WHERE photoId = '" . $photoId . "'";
			$result = $this->mysqli->query($query);
			$deleteStatus = true;
		}

		return $deleteStatus;
	}

	public function getAllPhotoIds() : array
	{
		$photoIds = [];

			$query = "SELECT photoId FROM parseThisperson";
			$res = $this->mysqli->query($query);

			if ($res !== FALSE){
				$result = $res->fetch_all(MYSQLI_ASSOC);
			}

			if (!empty($result)) {
				$photoIds = array_column($result, "photoId");
			}

		return $photoIds;
	}

	public function getPhotoData(array $filter) : array
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
			

			if ($result !== FALSE){
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

	public function getPhotoCount() {
		$query = "SELECT count(*) as cnt FROM parseThisperson WHERE photoStatus = '2'";
		$result = $this->mysqli->query($query);
		return $result->fetch_assoc()['cnt'];
	}

	public function getPhotoDataForAnalyze()
	{
		$query = "SELECT photoId FROM parseThisperson WHERE photoStatus = 1 LIMIT 100";
		$result = $this->mysqli->query($query);

		$data=[];

		if ($result !== FALSE){
			$result = $result->fetch_all(MYSQLI_ASSOC);
		}

		if (!empty($result) && !is_null($result)) {
			foreach ($result as $key => $value) {
				array_push($data, $value['photoId']);
			}
		}

		return $data;
	}

	public function updatePhotoDataAfterAnalyze(array $updateArray) : bool
	{
		$updateStatus = false;

		$query = "UPDATE parseThisperson SET gender = '" . $updateArray["gender"] . "', age = '" . $updateArray["age"] . "', photoStatus = 2  WHERE photoId = '" . $updateArray["photoId"] . "'";
		$updateStatus = (bool) $this->mysqli->query($query);

		return $updateStatus;
	}

	public function getTotalRecords() : int
	{
		$count = 0;

		$query = "SELECT count(*) as cnt FROM parseThisperson";
		$result = $this->mysqli->query($query);

		if ($result !== FALSE){
			$result = $result->fetch_all(MYSQLI_ASSOC);
			if (!empty($result[0]["cnt"])) {
				$count = (int) $result[0]["cnt"];
			}
		}

		return $count;
	}

	public function getParsedRecords() : int
	{
		$count = 0;

		$query = "SELECT count(*) as cnt FROM parseThisperson WHERE photoStatus = 2";
		$result = $this->mysqli->query($query);

		if ($result !== FALSE){
			$result = $result->fetch_all(MYSQLI_ASSOC);
			if (!empty($result[0]["cnt"])) {
				$count = (int) $result[0]["cnt"];
			}
		}

		return $count;
	}

	public function getReadyRecords() : int
	{
		$count = 0;

		$query = "SELECT count(*) as cnt FROM parseThisperson WHERE photoStatus = 1";
		$result = $this->mysqli->query($query);

		if ($result !== FALSE){
			$result = $result->fetch_all(MYSQLI_ASSOC);
			if (!empty($result[0]["cnt"])) {
				$count = (int) $result[0]["cnt"];
			}
		}

		return $count;
	}

	public function getRatioByPhotoId(string $photoId) : int
	{
		$ratio = 0;

		if (!empty($photoId)) {
			$query = "SELECT photoRatio FROM parseThisperson WHERE photoId = '" . $photoId . "'";
			$result = $this->mysqli->query($query);
			$ratio  = $result->fetch_assoc()['photoRatio'];
		}

		return $ratio;
	}

	/**
     * Update photoRatio by photoId
     *
     * @param string $photoId
     * @param int $ratio
     * @return bool
     */
	public function updateRatioByPhotoId(string $photoId, int $ratio) : bool
	{
		$updateStatus = false;

		if (!empty($photoId)) {
			$query = "UPDATE parseThisperson SET photoRatio = " . $ratio . " WHERE photoId = '" . $photoId . "'";
			$updateStatus = (bool) $this->mysqli->query($query);
		}

		return $updateStatus;
	}
}