<?php

require_once("header.php");
require_once("settings.php");
global $_SITE;
$GLOBALS['title'] = "Latest articles";
$GLOBALS['seo_description'] = "Beskrivning.... " . $_SITE['name'] . "";



// Fetch the latest articles
$query = "SELECT * FROM article ORDER BY created_at DESC LIMIT 10";
$result = mysqli_query($con, $query);
?>

<div class="articles-container">
    <h1>Latest Articles</h1>
    <?php while ($row = mysqli_fetch_assoc($result)) : ?>
        <div class="article">
            <h2 class="article-title"><?php echo htmlspecialchars($row['title']); ?></h2>
            <p class="article-date"><?php echo htmlspecialchars($row['created_at']); ?></p>
            <p class="article-content"><?php echo substr(htmlspecialchars($row['content']), 0, 200) . '...'; ?></p>
            <a class="read-more" href="articles/<?php echo createSlug($row['title']) . '-' . $row['id']; ?>/">Read More</a>

        </div>
    <?php endwhile; ?>
</div>

<?php
require_once("footer.php");
?>