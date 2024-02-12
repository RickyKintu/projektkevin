<?php
header('Content-Type: application/xml; charset=utf-8');

require_once("../settings.php");

//Function to get the last modification date of a sitemap
function getLastModDate($con, $tableName)
{
    switch ($tableName) {
        case "videos":
            $last_update = "datetime";
            break;
        case "articles":
            $last_update = "created_at";
            $tableName = "article";
            break;
        default:
            $last_update = "datetime";
            break;
    }

    $query = "SELECT MAX($last_update) AS lastmod FROM $tableName";
    $result = mysqli_query($con, $query);
    if ($row = mysqli_fetch_assoc($result)) {
        //Convert the date to W3C format
        $date = new DateTime($row['lastmod']);
        return $date->format('c');
    }
    return date('Y-m-d'); //Fallback to current date if no data is found
}


$videoLastMod = getLastModDate($con, 'videos');
$articleLastMod = getLastModDate($con, 'articles');

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
echo '<sitemap>';
echo '<loc>' . $_SITE["url"] . 'sitemaps/videos.php</loc>';
echo '<lastmod>' . $videoLastMod . '</lastmod>';
echo '</sitemap>';
echo '<sitemap>';
echo '<loc>' . $_SITE["url"] . 'sitemaps/articles.php</loc>';
echo '<lastmod>' . $articleLastMod . '</lastmod>';
echo '</sitemap>';
echo '<sitemap>';
echo '<loc>' . $_SITE["url"] . 'sitemaps/competitors.xml</loc>';
echo '<lastmod>' . $articleLastMod . '</lastmod>';
echo '</sitemap>';
#Add more sitemap entries as needed
echo '</sitemapindex>';
