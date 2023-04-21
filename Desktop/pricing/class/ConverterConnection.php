<?php

/**
 * CONTROLLER
 * Class for connecting to the currency transfer service
 * Class ConverterConnection
 */
class ConverterConnection
{
    /**
     * @return array
     * Initialization of connection to the site
     */
    public static function initArray(): array
    {
        $url = "https://www.cbr-xml-daily.ru/daily_json.js";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $data = json_decode(curl_exec($ch), $assoc = true);
        curl_close($ch);

        return $data;
    }
}
