<?php

/**
 * Class for connect to MySQL
 */
class MysqlConnect {

	protected $mysqli; 

	function __construct() { 
		$this->mysqli = new mysqli('localhost','root','','parse'); 
		if($this->mysqli->connect_error) {
			die('Connect Error (' . mysqli_connect_errno() . ') '. mysqli_connect_error());
		}  
	}
}