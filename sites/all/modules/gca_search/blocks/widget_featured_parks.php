<?php
$parks = getFeaturedParks();

if ($parks) {
  shuffle($parks);
  echo "<div id='updated-parks' class='ui-corners-all'>";
  echo "<h3 style='margin-bottom:10px;'>Featured Parks</h3>";
  for ($i = 0; $i < 5; $i++) {
    if ($parks[$i]) {
      $nodeInfo = node_load($parks[$i]);
      $imageSize = getimagesize("http://www.gocampingamerica.com/sites/default/files/" . rawurlencode($nodeInfo->field_camp_slideshow[LANGUAGE_NONE][0]["filename"]));
      $width = 100;
      $height = round(($width * $imageSize[1]) / $imageSize[0]);
      echo "<table><tr>";
      echo "<td valign='top' style='width:110px;'><img src='/sites/default/files/" . rawurlencode($nodeInfo->field_camp_slideshow[LANGUAGE_NONE][0]["filename"]) . "' width='" . $width . "' height='". $height . "' style='margin-right:10px;' /></td>";
      echo "<td valign='top'><div style='line-height:1.3em;'><b><a href='/" . drupal_get_path_alias("node/" . $nodeInfo->nid) . "'>" . $nodeInfo->title . "</a></b></div>";
      echo "<div style='margin-top:7px;font-size:0.8em;'>" . $nodeInfo->field_location[LANGUAGE_NONE][0]["city"] . ", " . $nodeInfo->field_location[LANGUAGE_NONE][0]["province"] . "</div>";
      echo "</td></tr></table>";
    }
  }
  echo "<br clear='all' />";
  echo "</div>";
}

function getFeaturedParks() {
  $query = db_query("SELECT DISTINCT nid FROM {content_type_camp} WHERE field_park_tier_value = 4");
  while ($row = $query->fetchAssoc()) {
    $vid = getFeaturedVid($row["nid"]);
    $tier = checkFeaturedTier($row["nid"], $vid);
    if ($tier == 4) {
      $result[] = $row["nid"];
    }
  }
  if ($result) {
    return $result;
  }
  return;
}

function getFeaturedVid($nid) {
  $query = db_query("SELECT vid FROM {node} WHERE nid = :nid ORDER BY vid DESC LIMIT 1", array("nid" => $nid));
  while ($row = $query->fetchAssoc()) {
    $result = $row["vid"];
  }
  if ($result) {
    return $result;
  }
  return;
}

function checkFeaturedTier($nid, $vid) {
  $query = db_query("SELECT field_park_tier_value FROM {content_type_camp} WHERE nid = :nid AND vid = :vid LIMIT 1", array("nid" => $nid, "vid" => $vid));
  while ($row = $query->fetchAssoc()) {
    $result = $row["field_park_tier_value"];
  }
  if ($result) {
    return $result;
  }
  return;
}

?>