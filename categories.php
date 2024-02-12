<?php
require_once("settings.php");
require_once("functions/functions.inc.php");

$query = "SELECT id, tags, thumbnails FROM videos";
$result = mysqli_query($con, $query);

$tagsVideos = []; //Structure: [tag => [[videoId => thumbnail], ...]]
$videoUsageCount = []; //Structure: [videoId => count]

require_once("header.php");

while ($row = mysqli_fetch_assoc($result)) {
    $videoId = $row['id'];
    $thumbnails = explode(',', $row['thumbnails']);
    $primaryThumbnail = $thumbnails[0];
    $videoTags = explode(',', $row['tags']);

    foreach ($videoTags as $tag) {
        $tag = trim(strtoupper($tag));
        if (!isset($tagsVideos[$tag])) {
            $tagsVideos[$tag] = [];
        }
        $tagsVideos[$tag][$videoId] = $primaryThumbnail;

        if (!isset($videoUsageCount[$videoId])) {
            $videoUsageCount[$videoId] = 0;
        }
    }
}

$categoryData = [];
$usedBackgrounds = []; //Track used backgrounds to ensure uniqueness

foreach ($tagsVideos as $tag => $videos) {
    $selectedVideoId = null;
    $minUsage = PHP_INT_MAX;

    foreach ($videos as $videoId => $thumbnail) {
        if (!in_array($thumbnail, $usedBackgrounds)) {
            if ($videoUsageCount[$videoId] < $minUsage) {
                $selectedVideoId = $videoId;
                $minUsage = $videoUsageCount[$videoId];
            }
        }
    }

    //Fallback to the least used video if no unique background is found
    if ($selectedVideoId === null) {
        foreach ($videos as $videoId => $thumbnail) {
            if ($videoUsageCount[$videoId] < $minUsage) {
                $selectedVideoId = $videoId;
                $minUsage = $videoUsageCount[$videoId];
                //Break for first least used, or remove the break to get the absolute least used
                break;
            }
        }
    }

    if ($selectedVideoId !== null) {
        $backgroundImage = $videos[$selectedVideoId];
        $usedBackgrounds[] = $backgroundImage;
        $videoUsageCount[$selectedVideoId]++;
        $categoryLink = "<a href='category_page.php?category=" . urlencode($tag) . "' class='category-link'>";



        $categoryItem = $categoryLink . "<div class='category-item'>";
        $categoryItem .= "<div class='category-image' style='background-image: url($backgroundImage);'>";
        $categoryItem .= "<span class='category-name'>$tag</span>";
        $categoryItem .= "<span class='video-count'>" . count($videos) . " videos</span>";
        $categoryItem .= "</div>";
        $categoryItem .= "</div></a>";

        $categoryData[] = $categoryItem;
    }
}

foreach ($categoryData as $categoryItem) {
    echo $categoryItem;
}

require_once("footer.php");
