<?php

require_once 'settings-functions.php';

if (isset($_GET['code'])) {
    $code = $_GET['code'];
    getOauthToken($code);
}

// now let's see if we can read in our user
$userData = file_get_contents('fake_database.txt');

var_dump($userData["ig_user_id"]);

getMedia($userData["ig_user_id"], $userData["ig_access_token"]);


