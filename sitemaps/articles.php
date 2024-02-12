<?php
header('Content-Type: application/xml; charset=utf-8');

require_once("../settings.php");
require_once("../functions/functions.inc.php");


$urls = [];

$result = mysqli_query($con, "SELECT id, title, created_at FROM article");

while ($row = mysqli_fetch_assoc($result)) {
    $date = new DateTime($row['created_at']);

    $date = $date->format('Y-m-d');
    $urls[] = [
        'loc' => $_SITE["url"] . 'articles/' . createSlug($row['title']) . '-' . $row['id'] . "/",
        'lastmod' => $date,
        'changefreq' => 'hourly',
        'priority' => '0.5'
    ];
}

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
echo '<url>';
echo '<loc>' . $_SITE['url'] . 'articles/</loc>';
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
    echo '</url>';
}
echo '</urlset>';
