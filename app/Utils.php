<?php

namespace App; 

class Utils
{
    public const UPLOADS_DIR = 'uploads';
    public const PROFILE_IMG_DIR = 'profile'; 

    public static function curlPostRequest($url, $params, $body, $headers)
    {

        

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url . '?' . $params);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = json_decode(curl_exec($ch));
        curl_close($ch);

        return $server_output;
    }

    public static function curlPutRequest($url, $body, $headers)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_PUT, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = json_decode(curl_exec($ch));
        curl_close($ch);

        return $server_output;
    }

    public static function curlGetRequest($url, $params, $headers)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . '?' . $params);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = json_decode(curl_exec($ch));
        curl_close($ch);

        // $output =  curl_exec($ch);
        // curl_close ($ch);
        // $server_output = json_decode($output);

        return $server_output;
    }
}
