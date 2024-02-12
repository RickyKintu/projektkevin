<?php
require_once("settings.php");



if (isset($_GET['search'])) {
    $search = str_replace('-', ' ', $_GET['search']);
    $searchby = 'title';

    $seo_description = "Discover $search and more at " . $_SITE['name'] . ". Watch $search ...";
    $GLOBALS['title'] = "Search " . $search;

    $where = "WHERE title LIKE '%$search%' OR id = '$search' OR tags LIKE '%$search%' OR cast LIKE '%$search%'";
    $query = "SELECT * FROM videos $where";
    $result = mysqli_query($con, $query);
    require_once("header.php");

    echo "<div class='head_sub'>";
    echo "<h1>Search: $search </h1>";
    echo "<h2><span>~</span> Watch $search more at " . $_SITE['name'] . " <span>~</span></h2>";
    echo "</div>";


    if (mysqli_num_rows($result) > 0) {


        displayVideos($con, $where);
        displayPagination($con, $where, $search);
    } else {
        echo "<div class='no_result'><h3>No results found.</h3><div>";
    }
} else {
    header("location: /");
}

?>
    
<?php
require_once("footer.php");
?>