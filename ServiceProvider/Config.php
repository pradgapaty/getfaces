<?php

namespace ServiceProvider;

use Symfony\Component\Yaml\Yaml;
use Exception;

class Config
{

    private static $config;

    public static function getParsingSettings(): array
    {
        $settings = [];
        self::getConfig();

        if (!empty(self::$config["parsingSettings"])) {
            $settings = self::$config["parsingSettings"];
        } else {
            throw new Exception("Error loading [parsingSettings] config params");
        }

        return $settings;
    }

    public static function getPathSettings(): array
    {
        $settings = [];
        self::getConfig();

        if (!empty(self::$config["pathSettings"])) {
            $settings = self::$config["pathSettings"];
        } else {
            throw new Exception("Error loading [pathSettings] config params");
        }

        return $settings;
    }

    public static function getPhotoSettings(): array
    {
        $settings = [];
        self::getConfig();

        if (!empty(self::$config["photoSettings"])) {
            $settings = self::$config["photoSettings"];
        } else {
            throw new Exception("Error loading [photoSettings] config params");
        }

        return $settings;
    }

    public static function getAnalysisSettings(): array
    {
        $settings = [];
        self::getConfig();

        if (!empty(self::$config["analysisSettings"])) {
            $settings = self::$config["analysisSettings"];
        } else {
            throw new Exception("Error loading [analysisSettings] config params");
        }

        return $settings;
    }

    private static function getConfig(): void
    {
        if (empty(self::$config)) {
            $path = dirname(dirname(__FILE__));
            $filePath = $path . '/configs/general.yaml';
            $config = Yaml::parseFile($filePath);
    
            if (empty($config) || !is_array($config)) {
                throw new Exception("Error loading config");
            }

            self::$config = $config;
        }
    }
}


