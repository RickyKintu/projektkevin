<?php
function format_time($seconds)
{
    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    $seconds = $seconds % 60;

    if ($hours > 0) {
        return sprintf("%d:%02d:%02d", $hours, $minutes, $seconds);
    } else {
        return sprintf("%d:%02d", $minutes, $seconds);
    }
}

function createSlug($string)
{
    $slug = strtolower($string);

    //Replace non-alphanumeric characters with hyphens
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);

    //Trim hyphens from the beginning and end
    $slug = trim($slug, '-');

    return $slug;
}

function generateVideoLink($id, $title)
{
    global $_SITE;
    $title = htmlspecialchars(str_replace('"', '', $title));
    $link = $_SITE['url'] . "videos/" . createSlug($title) . "-" . $id . "/";
    return $link;
}
function generateSearchLink($search)
{
    global $_SITE;
    $link = $_SITE['url'] . "search/" . createSlug($search) . "/";
    return $link;
}


function displayVideos($con, $where = "", $orderBy = "id DESC", $items_per_page = 15)
{
    global $_SITE;
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $page = max($page, 1); // Ensure $page is at least 1
    // Calculate the offset for the query
    $offset = ($page - 1) * $items_per_page;

    //Get videos for the current page
    $query = "SELECT * FROM videos $where ORDER BY $orderBy LIMIT $items_per_page OFFSET $offset";
    $result = mysqli_query($con, $query);

    echo "<div class='videos_container'>";

    while ($row = mysqli_fetch_assoc($result)) {
        $id = $row['id'];
        $title = $row['title'];
        $thumbnails = $row['thumbnails'];
        $duration = $row['duration'];
        $tags = explode(',', $row['tags']);
        $thumbnail = explode(",", $row['thumbnails'])[0];
        $link = generateVideoLink($id, $title);

        echo "<div class='box'>
            <a href='$link'>
            <div class='video'>
                <img src='" . $_SITE['url'] . $thumbnail . "' data-thumbnails='$thumbnails' alt='$title' class='slideshow'>
                <div class='duration'><i class='fas fa-clock'></i> " . format_time($duration) . "</div>
            </div>
            </a>
            <div class='title'>$title</div>
          ";

        //Display up to 3 tags
        echo "<div class='tags'>";
        $tags = explode(',', $row['tags']);
        $count = 0;
        foreach ($tags as $tag) {
            if ($count >= 4) break; //Stop after 3 tags
            echo "<a href='" . generateSearchLink(($tag)) . "' class='tag'>" . htmlspecialchars(trim($tag)) . "</a>";
            $count++;
        }
        echo "</div></div>";
    }

    echo "</div>";

    mysqli_free_result($result);
}


function displayPagination($con, $where = '', $search = '', $items_per_page = 15)
{
    //Determine the current page number from the URL
    $current_page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $current_page = max($current_page, 1); //Ensure $current_page is at least 1

    $total_query = "SELECT COUNT(*) FROM videos $where";
    $total_result = mysqli_query($con, $total_query);
    $total_row = mysqli_fetch_array($total_result);
    $total_videos = $total_row[0];
    $total_pages = ceil($total_videos / $items_per_page);

    echo '<div class="pagination">';

    for ($i = 1; $i <= $total_pages; $i++) {
        $link = $search ? generateSearchLink($search) . "page/$i" : "page/$i";
        if ($i == $current_page) {
            echo "<span class='page-number current-page'>$i</span> ";
        } else {
            echo "<a href='$link' class='page-number'>$i</a> ";
        }
    }

    echo '</div>';
}
