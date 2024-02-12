<?php

require_once("settings.php");

$query = "SELECT * FROM videos ORDER BY view_count DESC LIMIT 15";

$seo_description = "SEO beskrivning";

$GLOBALS['title'] = "Most viewd videos";
$GLOBALS['tags'] = explode(',', "most viewd, watched, popular");
$GLOBALS['seo_description'] = $seo_description;

require_once("header.php");
echo "<h1>Most Viewed Videos</h1>";

echo displayVideos($con, "", $orderBy = "view_count DESC");

require_once("footer.php");
