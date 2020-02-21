<?php

require_once 'settings-functions.php';

echo "<h1>Instagram Basic Display Test</h1>";

$login_url = getLoginURL();

echo "<a href=\"$login_url\">$login_url</a>";
