<?php
if (isset($_GET['id']) && isset($_GET['title'])) {
  require_once("settings.php");
  require_once("functions/functions.inc.php");

  $id = mysqli_real_escape_string($con, $_GET['id']);
  $slug = createSlug(htmlspecialchars($_GET['title']));


  //Increment the view count
  $updateViews = "UPDATE `videos` SET `view_count` = `view_count` + 1 WHERE `id` = '$id'";
  mysqli_query($con, $updateViews);

  $query = "SELECT * FROM videos WHERE id = '$id'";
  $result = mysqli_query($con, $query);

  if ($row = mysqli_fetch_assoc($result)) {
    //Determine the video source order based on availability
    $source_order = array();
    if (!empty($row['video3'])) {
      $source_order[] = 'video3';
    }
    if (!empty($row['video4'])) {
      $source_order[] = 'video4';
    }

    //Include video1 and video2 only if video3 and video4 are not available
    if (empty($source_order)) {
      if (!empty($row['video1'])) {
        $source_order[] = 'video1';
      }
      if (!empty($row['video2'])) {
        $source_order[] = 'video2';
      }
    }

    //Default source is initially set to 'video3' if available
    $default_source = in_array('video3', $source_order) ? 'video3' : (in_array('video4', $source_order) ? 'video4' : (in_array('video2', $source_order) ? 'video2' : 'video1'));

    $source = isset($_GET['source']) && in_array($_GET['source'], $source_order) ? $_GET['source'] : $default_source;
    $title = $row['title'];
    $title = htmlspecialchars(str_replace('"', '', $title));

    if ($slug !== createSlug($title)) {
      $link = generateVideoLink($id, $title);
      echo $link;
      echo "<BR>" . $slug;
      header("Location: $link/");
    } else {
      $thumbnails = explode(',', $row['thumbnails']);
      $duration = $row['duration'];
      $video = $row[$source];
      $cast = explode(',', $row['cast']);
      $tags = explode(',', $row['tags']);

      if (!empty($row['cast'])) {
        $cast_d = "and watch " . $row['cast'];
      } else {
        $cast_d = "and more ";
      }

      $seo_description = "Discover " . implode(', ', array_slice($tags, 0, 3)) . " $cast_d in this video. Watch $title and explore diverse content.";

      $GLOBALS['title'] = "Watch " . $title;
      $GLOBALS['tags'] = $tags;
      $GLOBALS['seo_description'] = $seo_description;

      require_once("header.php"); //Include the header after setting global variables

      echo "<div class='video-holder'>
                <h1>$title</h1>";

      echo "<div class='video-cast' style='color: pink;'>";
      foreach ($cast as $actor) {
        $actor_trimmed = trim($actor);
        echo "<a href='" . generateSearchLink($actor_trimmed) . "' class='cast-link'>" . htmlspecialchars($actor_trimmed) . "</a>";
        if ($actor !== end($cast)) {
          echo ", "; //Add commas between names, but not after the last name
        }
      }
      echo "</div>";

      //Create a button for each available video source
      echo "<div class='video-source-switch'>";
      foreach ($source_order as $source_option) {
        $button_text = ucfirst($source_option);
        $source_type = ($source_option == 'video1' || $source_option == 'video2') ? "iframe" : "video";
        echo "<button onclick='switchVideoSource(\"" . $row[$source_option] . "\", \"" . $source_type . "\")'>$button_text</button>";
      }
      echo "</div>";

      if ($source == 'video3' || $source == 'video4') {
        $posterIndex = $source == 'video3' ? 0 : 1; //Use thumbnail 0 for video3 and thumbnail 1 for video4
        $poster = isset($thumbnails[$posterIndex]) ? $thumbnails[$posterIndex] : ''; //Fallback if the thumbnail index doesn't exist
        echo "<video id='my-video' class='videoplayer' controls poster='$poster'>
                <source src='$video' type='video/mp4'>
                Your browser does not support the video tag.
              </video>";
      } else {
        echo "<iframe class='videoframe' src='$video' scrolling='no' frameborder='0' allowfullscreen='true'></iframe>";
      }

      echo "<p>$seo_description</p> <!-- SEO-friendly description -->
          </div>";
    }
  } else {
    echo "No video found.";
  }
} else {
  require_once("header.php"); //include header here if id is not set
  echo "Something went wrong!";
}
?>
<script>
  function switchVideoSource(source, type) {
    if (type === 'video') {
      var videoPlayer = document.getElementById('my-video');
      if (videoPlayer) {
        videoPlayer.src = source;
      }
    } else if (type === 'iframe') {
      var iframe = document.querySelector('.videoframe');
      if (iframe) {
        iframe.src = source;
      }
    }
  }
</script>
​<script src="https://cdn.fluidplayer.com/v3/current/fluidplayer.min.js"></script>


<script type="application/javascript">
  var testVideo = fluidPlayer(
    "my-video", {
      vastOptions: {
        "adList": [{
            "roll": "preRoll",
            "vastTag": "https://s.magsrv.com/splash.php?idzone=5193268"
          },
          {
            "roll": "midRoll",
            "vastTag": "https://s.magsrv.com/splash.php?idzone=5193268",
            "timer": 8
          },
          {
            "roll": "midRoll",
            "vastTag": "https://s.magsrv.com/splash.php?idzone=5193268",
            "timer": 10
          },
          {
            "roll": "postRoll",
            "vastTag": "https://s.magsrv.com/splash.php?idzone=5193268"
          }
        ]
      }
    }
  );
</script>
<?php include_once("contents/comment_section.inc.php") ?>

​
<?php
require_once("footer.php");
?>