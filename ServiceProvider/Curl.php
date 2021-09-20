<?php

namespace ServiceProvider;

class Curl
{
    public function callCurl(string $url)
    {
        $curl = curl_init();

        curl_setopt_array(
            $curl, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url,
            ]
        );

        $res = curl_exec($curl);

        return $res;
    }
}
