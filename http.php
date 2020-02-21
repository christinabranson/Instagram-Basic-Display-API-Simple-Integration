<?php

require_once 'settings-functions.php';

/**
 * Class http
 *
 * Helper class for curl functions
 *
 */
class http {

    /**
     * Makes a GET request with URL variables
     *
     * @param string $url
     * @param array $data
     * @param array $headers
     * @return string
     */
    public static function get($url, $data, $headers = []) {
        // build the url with the data
        if (!empty($data)) {
            $url .= "?" . http_build_query($data);
        }

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        if (!empty($headers)) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        }
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);

        if (isJson($response)) {
            $response = json_decode($response, true);
        }

        return $response;
    }

    /**
     * Makes a GET request with URL variables
     *
     * @param string $url
     * @param array $data
     * @param array $headers
     * @return string
     */
    public static function post($url, $data, $headers = []) {
        try {
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_FAILONERROR, false);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_POST, true);
            if (!empty($data)) {
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            }
            $response = curl_exec($curl);

            $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            if ($status != 200) {
                die(curl_error($curl));
            }
            curl_close($curl);

            $response = json_decode($response, true);

            return $response;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
}