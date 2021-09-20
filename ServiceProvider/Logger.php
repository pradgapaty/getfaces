<?php

namespace ServiceProvider;

class Logger
{
    public static function displayDebug(string $type, string $message)
    {
        if (!empty($type) && !empty($message)) {
            print_r($type . "! " . $message . ". \n");
        }
    }
}