<?php

/* ********************************************** */
/* This script displays search results by park    */
/* name for the mobile site.                      */
/* ********************************************** */

// Bootstrap
chdir($_SERVER['DOCUMENT_ROOT']);
global $base_url;
$base_url = 'http://'.$_SERVER['HTTP_HOST'];
require_once './includes/bootstrap.inc';
require_once './includes/common.inc';
require_once './includes/module.inc';
//drupal_bootstrap(DRUPAL_BOOTSTRAP_SESSION);
//drupal_bootstrap(DRUPAL_BOOTSTRAP_DATABASE);
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);
drupal_load('module', 'node');
module_invoke('node', 'boot');

if (isset($_REQUEST['p'])) {
  // $tempTarget = strtolower(preg_replace("/[^a-zA-Z0-9\s]/", "", $_REQUEST['p']));
  $tempTarget = str_replace('"', "", $_REQUEST['p']);
  $target = explode(" ", $tempTarget);
  //print_r($target);
  $targetCount = count($target);
}

//echo "<pre>";
//print_r($target);
//echo "</pre>";

$x = 0;
foreach($target as $keyword) {
  if (isset($_REQUEST['sid'])) {
    $queryString = 'SELECT distinct nr.nid FROM {node_revisions} nr, {node} n, {location} l, {location_instance} li WHERE n.nid=nr.nid AND n.nid=li.nid AND l.lid=li.lid AND n.type="camp" AND li.genid LIKE "%%field_location%%" nr.title LIKE "%%%s%%" and l.province = "' . $_REQUEST['sid'] . '"';
    //echo $queryString;
  } else {
    $queryString = 'SELECT distinct nr.nid FROM {node_revisions} nr, {node} n WHERE n.type="camp" AND nr.title LIKE "%%%s%%"';
  }
  //echo "query: " . $queryString;
  $query = db_query($queryString, $keyword);
  while ($row = db_fetch_object($query)) {
    $results[$x][] = $row->nid;
  }
  //echo "<pre>";
  //print_r($results);
  //echo "</pre>";
  $x++;
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
    if (checkActive($park["nid"])) {
      $oldParkURL = 'node/' . $park["nid"];
      $newParkURL = getParkAlias($oldParkURL);
      echo '<a href="/show_park.php?p=' . $park["nid"] . '" data-role="button" data-icon="arrow-r" data-theme="c" data-iconpos="right"><b>' . $park["title"] . '</b><br />';
	  echo "<span class='results-city'>" . $park["city"] . ", " . $park["province"] . "</span>";
	  echo '</a>';
    }
  }
} else {
  echo "No parks matched your search criteria.";
}
echo "</div>";


function getParkAlias($src) {
  $query = db_query("SELECT dst FROM {url_alias} WHERE src = '%s' LIMIT 1", $src);
  while ($row = db_fetch_array($query)) {
    $result = $row["dst"];
  }
  return $result;
}

function getInfo($nids) {
  $x = 0;
  foreach ($nids as $nid) {
    $query = db_query("SELECT n.title, l.city, l.province FROM {node} n, {content_type_camp} ctc, {location} l, {location_instance} li WHERE n.nid = ctc.nid AND n.nid = li.nid AND li.lid = l.lid AND li.genid LIKE '%%field_location%%' AND n.nid = %d", $nid);
    while ($row = db_fetch_object($query)) {
      $results[$x][nid] = $nid;
      $results[$x][title] = $row->title;
      $results[$x][city] = $row->city;
      $results[$x][province] = $row->province;
    }
  $x++;
  }
  return $results;
}

function checkActive($nid) {
  $result = 0;
  $query = db_query("SELECT field_camp_status_value FROM {content_type_camp} where nid = %d ORDER BY vid DESC LIMIT 1", $nid);
  while ($row = db_fetch_array($query)) {
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