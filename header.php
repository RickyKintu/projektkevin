<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once("settings.php");
require_once("functions/functions.inc.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Google tag (gtag.js) -->
    <base href="<?php echo $_SITE['url'] ?>">
    <meta charset="UTF-8">
    <title><?php echo isset($GLOBALS['title']) ? $GLOBALS['title'] . ' - ' . $_SITE['name'] : $_SITE['title']; ?></title>
    <meta name="description" content="<?php echo isset($GLOBALS['seo_description']) ? $GLOBALS['seo_description'] : 'Default Description'; ?>">
    <meta name="keywords" content="<?php echo isset($GLOBALS['tags']) ?  implode(', ', $GLOBALS['tags']) : 'tags, tags, tags'; ?>">

    <meta property="og:title" content="<?php echo $_SITE['name'] ?>">
    <meta property="og:description" content="Beskrivning <?php echo $_SITE['name'] ?>.">
    <meta property="og:url" content="<?php echo $_SITE['url'] ?>">
    <meta property="og:image" content="<?php echo $_SITE['url'] ?>images/logo.png">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>
    <link rel="icon" href="<?php echo $_SITE['url']; ?>images/favicon.ico" type="image/x-icon">
    <link rel="shortcut icon" href="<?php echo $_SITE['url']; ?>images/favicon.ico" type="image/x-icon">


    <script src="https://kit.fontawesome.com/edd1c495b2.js" crossorigin="anonymous"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </base>
</head>

<body>

    <header>
        <div class="logo">
            <a href="<?php $_SITE['url'] ?>">
                <img src="images/logo.png" alt="logo" height="100px">
            </a>
        </div>
        <div class="search-box">
            <form id="searchForm">
                <input type="text" id="searchInput" placeholder="Search lesbian porn">
                <button type="submit"><i class="fa fa-search"></i></button>
            </form>

            <script>
                document.getElementById('searchForm').onsubmit = function() {
                    var searchQuery = document.getElementById('searchInput').value;
                    var cleanQuery = searchQuery.trim().replace(/\s+/g, '-'); //Replace spaces with hyphens

                    window.location.href = '<?php $_SITE['url'] ?>search/' + encodeURIComponent(cleanQuery);
                    return false; //Prevent the default form submission
                };
            </script>

        </div>

        </div>
    </header>

    <nav>
        <ul>
            <li><a href="<?php echo $_SITE['url'] ?>">Videos</a></li>
            <li><a href="articles/">Articles</a></li>
            <li><a href="top-videos/">Popular</a></li>
        </ul>
    </nav>


    <div class="main-containter">