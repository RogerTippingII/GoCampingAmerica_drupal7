<?php

chdir($_SERVER['DOCUMENT_ROOT']);
define('DRUPAL_ROOT', __DIR__ . "/..");
global $base_url;
$base_url = 'http://' . $_SERVER['HTTP_HOST'];
require_once './includes/bootstrap.inc';
require_once './includes/common.inc';
require_once './includes/module.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);
drupal_load('module', 'node');
module_invoke('node', 'boot');

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

$parks = array();
if(count(target) > 0){
  if (isset($state_id)) {
    $queryString = "SELECT distinct n.nid
                        FROM {node} n
                        JOIN {location_instance} li ON li.vid = n.vid
                        JOIN {location} l ON l.lid = li.lid
                        WHERE n.type = 'camp'
                        AND l.province = '" . $state_id . "'";
  } else {
    $queryString = "SELECT distinct n.nid
                        FROM {node} n
                        WHERE n.type='camp'";

  }

  foreach($target as $keyword) {
    $queryString .= " AND n.title LIKE '%%". $keyword ."%%'";
  }

  $queryString .= " ORDER BY n.title";
  $rs = db_query($queryString);

  $query_results = $rs->fetchAllAssoc('nid');
  $nids = array_keys($query_results);

  $parks = node_load_multiple($nids);
}

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

  if($result == '') return $src;
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
  $query = db_query("SELECT field_camp_status_value FROM {field_data_field_camp_status} where entity_id = :nid LIMIT 1", array("nid" => $nid));
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