<?php

$slideData = getSlideData();

$x = 0;
foreach ($slideData as $slide) {
  $slideInfo = node_load($slide);
  if ($slideInfo->field_slide_youtube[LANGUAGE_NONE][0][url]) {
    $youtube = str_replace("http://www.youtube.com/watch?v=", "", $slideInfo->field_slide_youtube[LANGUAGE_NONE][0][url]);
  }
  ?>
  <div id="slide<?php echo $x ?>" class="slideshow<?php
  if ($x != 0) {
    echo " hide";
  } ?>">
    <div id="slideshow-left">
      <div id="slideshow-text">
        <div id="slideshow-headline">
          <?php if ($youtube) { ?>
            <a rel="<?php echo $youtube; ?>" class="activate-youtube" onClick="_gaq.push(['_trackEvent', 'Sliders', 'Clicked','Homepage Slider',, false]);"><?php echo $slideInfo->title; ?></a>
          <?php } else { ?>
            <a href="<?php echo $slideInfo->field_slide_link[LANGUAGE_NONE][0][url]; ?>" onClick="_gaq.push(['_trackEvent', 'Sliders', 'Clicked','Homepage Slider',, false]);"><?php echo $slideInfo->title; ?></a>
          <?php } ?>
        </div> <!-- /#slideshow-headline -->
        <div id="slideshow-body">
          <?php echo $slideInfo->field_slide_teaser[LANGUAGE_NONE][0][value]; ?>
        </div> <!-- /#slideshow-body -->
      </div> <!-- /#slideshow-text -->
      <div id="slideshow-pagination">
        <?php
        for ($i = 0; $i < 4; $i++) {
          echo "<img src='/sites/all/themes/gca_new/images/ss_" . ($i + 1) . "_";
          if ($i == $x) {
            echo "on";
          } else {
            echo "off";
          }
          echo ".png' class='slideshow-change' rel='#slide" . $i . "' /></a>";
        }
        ?>
      </div> <!-- /#slideshow-pagination -->
    </div> <!-- /#slideshow-left -->
    <div id="slideshow-right">
      <?php if ($youtube) { ?>
        <a rel="<?php echo $youtube; ?>" class="activate-youtube" onClick="_gaq.push(['_trackEvent', 'Sliders', 'Clicked','Homepage Slider',, false]);"><img src="http://img.youtube.com/vi/<?php echo $youtube; ?>/0.jpg" width="458" height="335" alt="<?php echo $slideInfo->title; ?>" /></a>
        <div class="video-play"><a href="#youtube-video" rel="<?php echo $youtube; ?>" class="activate-youtube" onClick="_gaq.push(['_trackEvent', 'Sliders', 'Clicked','Homepage Slider',, false]);"><img src="/img/icons/btn-play.png" width="470" height="380"></div>
      <?php } else { ?>
        <a href="<?php echo $slideInfo->field_slide_link[LANGUAGE_NONE][0][url]; ?>" onClick="_gaq.push(['_trackEvent', 'Sliders', 'Clicked','Homepage Slider',, false]);">
          <img src="<?php echo file_create_url($slideInfo->field_slide_image[LANGUAGE_NONE][0][uri]); ?>" width="458" height="335" alt="<?php echo $slideInfo->title; ?>" /></a>
      <?php } ?>
    </div>
  </div> <!-- /#slideshow -->
  <?php
  $youtube = "";
  $x++;
} ?>

<?php
function getSlideData() {
  for ($i = 0; $i < 4; $i++) {
    //$query = db_query("SELECT n.nid FROM {node} n, {content_type_slide} s WHERE n.nid = s.nid AND n.type = 'slide' AND n.status = 1 ORDER BY s.field_slide_order_value ASC, n.created DESC LIMIT 4");
    $query = db_query("SELECT n.nid FROM {node} n, {content_type_slide} s WHERE n.nid = s.nid AND n.type = 'slide' AND n.status = 1 AND s.field_slide_order_value = :orderValue ORDER BY n.created DESC LIMIT 1", array("orderValue" => ($i + 1)));
    while ($row = $query->fetchObject()) {
      $slides[] = $row->nid;
    }
  }
  return $slides;
}
?>