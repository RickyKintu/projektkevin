<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once("../settings.php");
require_once("../functions/functions.inc.php");

function checkVideoUrl($url)
{
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    echo "HTTP Status Code: " . $httpCode . "<br>";

    if ($httpCode === 200) {
        return true;
    } else {
        return false;
    }
}



//Function to update the database with the result of the URL check
function updateDatabase($con, $videoId, $link)
{
    $updateQuery = "UPDATE videos SET video4 = '$link' WHERE id = $videoId";
    mysqli_query($con, $updateQuery);
}


//Check if the connection was successful
if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}

$query = "SELECT id, title, video4 FROM videos WHERE video4 = ''";
$result = mysqli_query($con, $query);

//Loop Through Video URLs
while ($row = mysqli_fetch_assoc($result)) {
    $videoId = $row['id'];
    $server = $row['video4'];
    $link = "$videoId|https://127.0.0.1/uploads/" . substr(createSlug($row['title']), 0, 50) . ".mp4";
    echo $link . "<br>";
}

mysqli_close($con);
