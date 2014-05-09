<?php
$nodeData = node_load(arg(1));
$header_image = file_create_url($nodeData->field_top_photo[LANGUAGE_NONE][0][uri]);
$header_headline = $nodeData->field_header_headline[LANGUAGE_NONE][0][value];
$header_body = $nodeData->field_header_body[LANGUAGE_NONE][0][value];
$pageURI = $_SERVER["REQUEST_URI"];
// /sites/all/themes/gca_new_interior/images
?>
<div id="header_promo_left">
  <div id="header_promo_title">
    <?php
    if (substr($pageURI, 0, 5) == "/user") {
      echo "Connect Your Park";
    } elseif ($header_headline) {
      echo $header_headline;
    } else {
      echo "Where Do You Want to Go?";
    }
    ?>
  </div>
  <div id="header_promo_body">
    <?php
    if (substr($pageURI, 0, 5) == "/user") {
      echo "Go Camping America provides an audience size in the thousands each month.";
    } elseif ($header_body) {
      echo $header_body;
    } else {
      echo "Escape the rat race, reconnect with the outdoors and spend some quality time with family. Find the park that best fits your needs!";
    }
    ?>
  </div>
</div> <!-- /#header_promo_left -->
<div id="header_promo_right">
  <?php
  if (substr($pageURI, 0, 5) == "/user") {
    echo "<img src='/sites/all/themes/gca_new_interior/images/KidsNightExpandable.jpg' />";
  } elseif ($nodeData->field_top_photo[LANGUAGE_NONE][0][uri]) {
    echo "<img src=" . $header_image . " />";
  } else {
    echo "<img src='/sites/default/files/top_photos/yorkies_xlg.jpg' />";
  }
  ?>
</div> <!-- /#header_promo_right -->