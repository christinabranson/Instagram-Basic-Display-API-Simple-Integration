<?php

require_once 'config.php';
require_once 'http.php';
define('IG_API_URL', 'https://graph.instagram.com/');
define('IG_OAUTH_URL', 'https://api.instagram.com/oauth/authorize');
define('IG_OAUTH_GET_TOKEN_URL', 'https://api.instagram.com/oauth/access_token');
define('IG_OAUTH_EXCHANGE_TOKEN_URL', 'https://graph.instagram.com/access_token');
define('IG_OAUTH_REFRESH_TOKEN_URL', 'https://api.instagram.com/refresh_access_token');

function confirmConfig() {
    if (!strlen(IG_APP_ID)) {
        die("IG_APP_ID must be defined");
    }

    if (!strlen(IG_APP_SECRET)) {
        die("IG_APP_SECRET must be defined");
    }

    if (!strlen(IG_APP_CALLBACK)) {
        die("IG_APP_CALLBACK must be defined");
    }
}

/**
 *      From https://developers.facebook.com/docs/instagram-basic-display-api/guides/getting-access-tokens-and-permissions
        https://api.instagram.com/oauth/authorize
        ?client_id={instagram-app-id}
        &redirect_uri={redirect-uri}
        &scope={scope}
        &response_type=code
        &state={state}        //Optional
 *
 * @return string
 */
function getLoginURL() {
    confirmConfig();
    $url = IG_OAUTH_URL;
    $url .= "?client_id=" . IG_APP_ID;
    $url .= "&redirect_uri=" . IG_APP_CALLBACK;
    $url .= "&scope=user_profile,user_media";
    $url .= "&response_type=code";
    return $url;
}

function getOauthToken($code) {
    $postData = array();
    $postData["client_id"] = IG_APP_ID;
    $postData["client_secret"] = IG_APP_SECRET;
    $postData["code"] = $code;
    $postData["grant_type"] = "authorization_code";
    $postData["redirect_uri"] = IG_APP_CALLBACK;

    $headers = array("Content-Type: multipart/form-data;");

    $url = IG_OAUTH_GET_TOKEN_URL;
    $response = http::post($url, $postData, $headers);

    if (isset($response["user_id"])) {
        // yay we got a response
        $ig_user_id = $response["user_id"];
        $ig_short_access_token = $response["access_token"];
        $ig_long_access_token = getLongOauthToken($ig_short_access_token);

        // now let's save our "user" to the database
        $fake_user_data_array = array();
        $fake_user_data_array["ig_user_id"] = $ig_user_id;
        $fake_user_data_array["ig_access_token"] = $ig_long_access_token;
        $fake_user_data_array["ig_access_token_last_updated"] = time();
        $fake_user_data_array["posts"] = array();
        writeJsonToFile($fake_user_data_array);
    }
}

function getLongOauthToken($short_access_token) {
    $getData = array();
    $getData["client_id"] = IG_APP_ID;
    $getData["client_secret"] = IG_APP_SECRET;
    $getData["access_token"] = $short_access_token;
    $getData["grant_type"] = "ig_exchange_token";

    $url = IG_OAUTH_EXCHANGE_TOKEN_URL;

    $response = http::get($url, $getData);

    if (isset($response["access_token"])) {
        return $response["access_token"];
    }
}

function getRefreshedOauthToken($log_access_token) {
    $getData = array();
    //$getData["client_id"] = IG_APP_ID;
    //$getData["client_secret"] = IG_APP_SECRET;
    $getData["access_token"] = $log_access_token;
    $getData["grant_type"] = "ig_refresh_token";

    $url = IG_OAUTH_REFRESH_TOKEN_URL;

    $response = http::get($url, $getData);

    if (isset($response["access_token"])) {
        return $response["access_token"];
    }
}

function getMedia($ig_access_token, $existing_data = []) {
    $media_array = $existing_data;  // start with existing data
    $url = IG_API_URL . "me/media?fields=id,caption&access_token=" . $ig_access_token;
    $response = http::get($url, []);
    if (!empty($response) && isset($response["data"])) {
        foreach ($response["data"] as $post_data) {

            $ig_media_id = $post_data["id"];
            if (isset($existing_data[$ig_media_id])) {
                continue;
            }

            $media = getMediaByID($ig_media_id, $ig_access_token);
            //$media["caption"] = $post_data["caption"];
            $media_array[$ig_media_id] = $media;
        }
    }

    // todo: should probably add a function to order by timestamp, but that can wait

    return $media_array;
}

function getMediaByID($ig_media_id, $ig_access_token) {
    $url = IG_API_URL . "$ig_media_id?fields=id,media_type,media_url,username,timestamp,caption&access_token=" . $ig_access_token;
    $response = http::get($url, []);
    return $response;
}


/**
 * Returns if valid JSON
 *
 * @param $string
 * @return bool
 */
function isJson($string) {
    json_decode($string);
    return (json_last_error() == JSON_ERROR_NONE);
}

function writeJsonToFile($jsonArray, $filename='fake_database.txt') {
    try {
        $fp = fopen($filename, 'w');
        fwrite($fp, json_encode($jsonArray, JSON_PRETTY_PRINT));
        fclose($fp);
    } catch (Exception $e) {
        die($e->getMessage());
    }
}