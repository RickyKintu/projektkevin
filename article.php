<?php
require_once("settings.php");

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;




//Fetch the article
$query = "SELECT * FROM article WHERE id = $id";
$result = mysqli_query($con, $query);
$article = mysqli_fetch_assoc($result);

$title = htmlspecialchars($article['title']);
$tags = htmlspecialchars($article['tags']);
$seo_description = substr(htmlspecialchars(strip_tags($article['content'])), 0, 160);

$tags = explode(',', $article['tags']);
$GLOBALS['title'] = $title;
$GLOBALS['tags'] = $tags;
$GLOBALS['seo_description'] = htmlspecialchars($seo_description);

require_once("header.php");


if (!$article) {
    echo "<p>Article not found.</p>";
} else {
?>
    <div class="article-detail">
        <h1 class="article-title"><?php echo $title; ?></h1>
        <p class="article-date"><?php echo htmlspecialchars($article['created_at']); ?></p>
        <div class="article-content">
            <?php echo nl2br(htmlspecialchars($article['content'])); ?>
        </div>
        <!-- Display tags -->
        <div class="article-tags">
            <?php
            $tags = explode(',', $article['tags']);
            foreach ($tags as $tag) {
                echo "<span class='tag'>" . htmlspecialchars(trim($tag)) . "</span> ";
            }
            ?>
        </div>
    </div>
<?php
}

require_once("footer.php");
?>