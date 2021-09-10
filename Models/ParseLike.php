<?php

require_once("/var/www/html/getfaces/Helpers/MysqlConnect.php");

class ParseLike extends MysqlConnect {
	
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

	/**
     * Check is photo liked from ip address
     *
     * @param string $photoId
     * @param string $ipAddr
     * @return bool
     */
	public function checkIsLiked(string $photoId, string $ipAddr) : bool
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
     * @param string $photoId
     * @param string $ipAddr
     * @return bool
     */
	public function markIsLiked(string $photoId, string $ipAddr) : bool
	{
		$insertStatus = false;

		if (!empty($photoId) && !empty($ipAddr)) {
			$query = "INSERT INTO parseLike (ipAddr, photoId) VALUES(INET_ATON('" . $ipAddr . "'), '" . $photoId . "')";
			$insertStatus = (bool) $this->mysqli->query($query);
		}
		
		return $insertStatus;
	}
}