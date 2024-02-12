<?php
require_once("header.php");

echo "<div class='head_sub'>";
echo "<h1>Welcome to " . $_SITE['name'] . " </h1>";
echo "<h2><span>~</span> Subtitle text  <span>~</span></h2>";
echo "</div>";


?>
<?php


displayVideos($con);
displayPagination($con);



?>



<?php
require_once("footer.php");
?>