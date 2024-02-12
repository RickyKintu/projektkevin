<?php
require_once("settings.php");



if (isset($_GET['category'])) {
    $category = str_replace('-', ' ', $_GET['category']);
    $categoryby = 'title';

    $seo_description = "Discover $category and more at " . $_SITE['name'] . ". Watch $category videos";

    $GLOBALS['title'] = "category " . $category;

    $where = "WHERE title LIKE '%$category%' OR tags LIKE '%$category%'";
    $query = "SELECT * FROM videos $where";
    $result = mysqli_query($con, $query);
    require_once("header.php");

    echo "<div class='head_sub'>";
    echo "<h1>$category</h1>";
    echo "<h2><span>~</span> Watch $category and more at " . $_SITE['name'] . " <span>~</span></h2>";
    echo "</div>";


    if (mysqli_num_rows($result) > 0) {


        displayVideos($con, $where);
        displayPagination($con, $where, $category);
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