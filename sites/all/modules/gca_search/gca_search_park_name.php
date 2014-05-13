<?php
if (isset($_REQUEST['p'])) {
  // $tempTarget = strtolower(preg_replace("/[^a-zA-Z0-9\s]/", "", $_REQUEST['p']));
  $tempTarget = str_replace('"', "", $_REQUEST['p']);
  $target = explode(" ", $tempTarget);
  print_r($target);
  $targetCount = 0;
}

//echo "<pre>";
//echo "target : ";
//print_r($target);
//echo "</pre>";

$x = 0;
foreach($target as $keyword) {
  if(trim($keyword) != ''){
    if (isset($_REQUEST['sid'])) {
      $queryString = 'SELECT distinct nr.nid FROM {node_revision} nr, {node} n, {location} l, {location_instance} li WHERE n.nid=nr.nid AND n.nid=li.nid AND l.lid=li.lid AND n.type="camp" AND li.genid LIKE "%%field_location%%" nr.title LIKE "%%%s%%" and l.province = "' . $_REQUEST['sid'] . '"';
      //echo $queryString;
    } else {
      $queryString = "SELECT distinct nr.nid FROM {node_revision} nr, {node} n WHERE n.type='camp' AND nr.title LIKE :keyword";
    }
    //echo "query: " . $queryString;

    $query = db_query($queryString, array("keyword" => '%%' . $keyword . '%%'));
    $results = array();
    while ($row = $query->fetchObject()) {
      $results[$x][] = $row->nid;
    }
//    echo "<pre>";
//    echo "results: ";
//    print_r($results);
//    echo "</pre>";
    $x++;
    $targetCount++;
  }
}

$intersected = $results[0];
if ($targetCount > 1) {
  for ($i = 1; $i < $targetCount; $i++) {
    $intersected = array_intersect($intersected, $results[$i]);
  }
}
//echo "Intersected: ";
//print_r($intersected);
//echo "<br />";


$parks = getInfo($intersected);

//echo "Parks: ";
//print_r($parks);
//echo "<br />";

echo "<div id='search-results'>\n";

if (count($parks)) {
  foreach ($parks as $park) {
    if (checkActive($park->nid)) {
      $oldParkURL = 'node/' . $park->nid;
      $newParkURL = getParkAlias($oldParkURL);
      echo "<div class='search-result-item'><h3><a href='/" . $newParkURL . "'>" . $park->title . "</a></h3>\n";
      echo $park->field_location[LANGUAGE_NONE][0]["city"] . ", " . $park->field_location[LANGUAGE_NONE][0]["province"];
      echo "</div>\n";
    }
  }
} else {
  echo "No parks matched your search criteria.";
}
echo "</div>";


function getParkAlias($src) {
  $query = db_query("SELECT alias FROM {url_alias} WHERE source = :src LIMIT 1", array('src' => $src));
  while ($row = $query->fetchAssoc()) {
    $result = $row["alias"];
  }
  return $result;
}

function getInfo($nids) {
  $x = 0;
//  foreach ($nids as $nid) {
//    $query = db_query("SELECT n.title, l.city, l.province FROM {node} n, {content_type_camp} ctc, {location} l, {location_instance} li WHERE n.nid = ctc.nid AND n.nid = li.nid AND li.lid = l.lid AND n.nid = :nid", array("nid" => $nid));
//    while ($row = $query->fetchObject()) {
//      $results[$x][nid] = $nid;
//      $results[$x][title] = $row->title;
//      $results[$x][city] = $row->city;
//      $results[$x][province] = $row->province;
//    }
//    $x++;
//  }

  $results = entity_load('node', $nids);
  return $results;
}

function checkActive($nid) {
  $result = 0;
  $query = db_query("SELECT field_camp_status_value FROM {content_type_camp} where nid = :nid ORDER BY vid DESC LIMIT 1", array("nid" => $nid));
  while ($row = $query->fetchAssoc()) {
    //echo "checkActive row found.<br />";
    //echo "checkActive status: " . $row["field_camp_status_value"] . "<br />";
    if ($row["field_camp_status_value"] == "Active" || $row["field_camp_status_value"] == "active") {
      $result = 1;
    }
  }
  //echo "checkActive result: " . $result . "<br />";
  return $result;
}
?>