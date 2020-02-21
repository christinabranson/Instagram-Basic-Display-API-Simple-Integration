<?php
require_once 'settings-functions.php';

echo "<link href=\"https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css\" rel=\"stylesheet\" integrity=\"sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh\" crossorigin=\"anonymous\">";

// now let's see if we can read in our user
$userData = file_get_contents('fake_database.txt');

$userData = json_decode($userData, true);

// easier to just ensure that this exists
if (!isset($userData["posts"])) {
    $userData["posts"] = array();
}

$media = getMedia($userData["ig_access_token"], $userData["posts"]);

// now let's save our "user" to the database
$fake_user_data_array = array();
$fake_user_data_array["ig_user_id"] = $userData["ig_user_id"];
$fake_user_data_array["ig_access_token"] = $userData["ig_access_token"];
$fake_user_data_array["ig_access_token_last_updated"] = time();
$fake_user_data_array["posts"] = $media;
writeJsonToFile($fake_user_data_array);

foreach ($media as $media_item) {
    echo "
    
    <div class=\"card m-2\" style=\"width: 18rem;\">
  <img src=\"".$media_item["media_url"]."\" class=\"card-img-top\" alt=\"...\">
  <div class=\"card-body\">
    <p class=\"card-text\">".$media_item["caption"]."</p>
  </div>
</div>

    ";
}