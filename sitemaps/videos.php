<?php
header('Content-Type: application/xml; charset=utf-8');

require_once("../settings.php");
require_once("../functions/functions.inc.php");


$urls = [];

$result = mysqli_query($con, "SELECT id, title, thumbnails, tags, duration, datetime FROM videos");
while ($row = mysqli_fetch_assoc($result)) {
    $date = new DateTime($row['datetime']);

    $date = $date->format('Y-m-d');
    $title =  htmlspecialchars($url['video_title'], ENT_XML1);
    $urls[] = [
        'loc' => $_SITE["url"] . "videos/" . createSlug(htmlspecialchars(str_replace('"', '', $row['title']))) . '-' . $row['id'] . "/",
        'lastmod' => '' . $date . '',
        'changefreq' => 'daily',
        'priority' => '0.5',
        'thumbnail' => $_SITE['url'] . explode(',', $row['thumbnails'])[0],
        'video_title' =>  $title,
        'video_desc' => 'Discover ' . implode(', ', array_slice(explode(',', $row['tags']), 0, 3)) . '. Watch ' . $title . '',
        'video_duration' => $row['duration'],
        'video_tags' => explode(',', $row['tags']),

    ];
}

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
              xmlns:video="http://www.google.com/schemas/sitemap-video/1.1">';
echo '<url>';
echo '<loc>' . $_SITE['url'] . '</loc>';
echo '<lastmod>' . date("Y-m-d") . '</lastmod>';
echo '<changefreq>hourly</changefreq>';
echo '<priority>1</priority>';
echo '</url>';
foreach ($urls as $url) {
    echo '<url>';
    echo '<loc>' . htmlspecialchars($url['loc'], ENT_XML1) . '</loc>';
    echo '<lastmod>' . $url['lastmod'] . '</lastmod>';
    echo '<changefreq>' . $url['changefreq'] . '</changefreq>';
    echo '<priority>' . $url['priority'] . '</priority>';
    echo '<video:video>
    <video:thumbnail_loc>' . $url['thumbnail'] . '</video:thumbnail_loc>
    <video:title>' . $url['video_title'] . '</video:title>
    <video:description>' . $url['video_desc'] . '</video:description>
    <video:duration>' . $url['video_duration'] . '</video:duration>
    <video:publication_date>' . $url['lastmod'] . '</video:publication_date>
    <video:family_friendly>no</video:family_friendly>
    <video:category>' . $url['video_tags'][0] . '</video:category>
';
    foreach ($url['video_tags'] as $tag) {
        echo '<video:tag>' . trim(htmlspecialchars($tag, ENT_XML1)) . '</video:tag>';
    }
    echo '
  </video:video>';
    echo '</url>';
}
echo '</urlset>';
