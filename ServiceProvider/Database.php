<?php

namespace ServiceProvider;

use Exception;
use Mysqli;
use Symfony\Component\Yaml\Yaml;

/**
 * Class for connect to MySQL
 */
class Database
{

    protected $mysqli; 

    function __construct(string $dbType)
    {
        $path = dirname(dirname(__FILE__));
        $filePath = $path . '/configs/database.yaml';
        $config = Yaml::parseFile($filePath);

        switch ($dbType) {
        case 'mysql':
            $this->getMysqlConnect($config["mysql"]);
            break;
        default:
            throw new Exception("Please correct database type");
        }
    }

    protected function getMysqlConnect(array $config): void
    {
        if (!empty($config["host"]) && !empty($config["user"])
            && !empty($config["password"]) && !empty($config["database"])
        ) {
            if (empty($this->mysqli)) {
                $this->mysqli = new Mysqli(
                    $config["host"],
                    $config["user"],
                    $config["password"],
                    $config["database"]
                );
            }

            if($this->mysqli->connect_error) {
                die('Connect Error (' . mysqli_connect_errno() . ') '. mysqli_connect_error());
            }
        }
    }
}
