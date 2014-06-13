<style type="text/css">
  .showcase { margin-left:7px; }
</style>

<?php

$parks = getParks();
echo "<pre style='display:none;'>";
print_r($parks);
echo "</pre>";

$rand = rand(0, (count($parks) - 1));
$park = node_load($parks[$rand]);
echo "<div class='showcase'>";
if ($park->field_camp_slideshow[LANGUAGE_NONE][0]["uri"]) {
  echo "<div class='photo'>";
  echo "<a href='" . drupal_get_path_alias('node/'.$park->nid) . "' onClick=\"_gaq.push(['_trackEvent', 'Widgets', 'Homepage Featured Park Widget', 'Clicked',, false]);\"><img width='180' height='120' src='". file_create_url($park->field_camp_slideshow[LANGUAGE_NONE][0]["uri"]) . "' /></a>";
  echo "</div>";
}
echo "<h3 class='title'>";
echo "<a href='" . drupal_get_path_alias('node/'.$park->nid) . "' onClick=\"_gaq.push(['_trackEvent', 'Widgets', 'Homepage Featured Park Widget', 'Clicked',, false]);\">" . $park->title . "</a>";
echo "</h3>";
echo $park->field_location[LANGUAGE_NONE][0]["street"] . "<br />";
if ($park->field_location[LANGUAGE_NONE][0]["additional"]) {
  echo $park->field_location[LANGUAGE_NONE][0]["additional"] . "<br />";
}
echo $park->field_location[LANGUAGE_NONE][0]["city"] . ", " . $park->field_location[LANGUAGE_NONE][0]["province"] . " " . $park->field_location[0]["postal_code"] . "<br />";
echo "<div class='phone'>";
if ($park->field_camp_phone[LANGUAGE_NONE][0]["number"]) {
  echo format_phone($park->field_camp_phone[LANGUAGE_NONE][0]["number"]) . "<br />";
}
echo "</div>";
echo "<div class='view-link'>";
echo "<a href='" . drupal_get_path_alias('node/'.$park->nid) . "' onClick=\"_gaq.push(['_trackEvent', 'Widgets', 'Homepage Featured Park Widget', 'Clicked',, false]);\">View »</a>";
echo "</div>";
echo "</div> <!-- /showcase -->";


function format_phone($phone) {
  if (strlen($phone) == 10) {
    $result = "(" . substr($phone, 0, 3) . ") " . substr($phone, 3, 3) . "-" . substr($phone, 6, 4);
    return $result;
  } else {
    return $phone;
  }
}

function getParks() {
  // Parks must be Tier 4 (Ultimate), status 1 (published) and Active
  $query = db_query("SELECT DISTINCT n.nid, n.vid FROM {node} n, {content_type_camp} c WHERE n.nid = c.nid AND n.status = 1 AND c.field_park_tier_value = 4 AND c.field_camp_status_value = 'Active' ORDER BY c.vid DESC");
  while ($row = $query->fetchAssoc()) {
    // Previous iterations of this block did not take into account the VID of the camp. So we run a check now to make sure the latest VID of each camp is still Tier 4.
    if (checkLatestVid($row["nid"], $row["vid"]) == 1) {
      $result[] = $row["nid"];
    }
  }
  if ($result) {
    return $result;
  } else {
    return;
  }
}

function checkLatestVid($nid, $vid) {
  $query = db_query("SELECT field_park_tier_value FROM {content_type_camp} WHERE nid = :nid AND vid = :vid LIMIT 1", array("nid" => $nid, "vid" => $vid));
  while ($row = $query->fetchAssoc()) {
    $result = $row["field_park_tier_value"];
  }
  if ($result == 4) {
    return 1;
  }
  return 0;
}
?>