<?php

/* ********************************************** */
/* This script displays search results by park    */
/* name for the mobile site.                      */
/* ********************************************** */

// Bootstrap
chdir($_SERVER['DOCUMENT_ROOT']);
define('DRUPAL_ROOT', __DIR__ . "/..");
global $base_url;
$base_url = 'http://' . $_SERVER['HTTP_HOST'];
require_once './includes/bootstrap.inc';
require_once './includes/common.inc';
require_once './includes/module.inc';
require_once './sites/all/gca-search/lib/Util.php';
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

if(count($target) > 0){
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

  //echo "Parks: ";
  //print_r($parks);
  //echo "<br />";

  echo "<div id='search-results'>\n";
  if (count($parks) > 0) {
    foreach ($parks as $park) {
      if ($park->field_camp_status[LANGUAGE_NONE][0]["value"] == "active") {
        $oldParkURL = 'node/' . $park->nid;
        $newParkURL = getParkAlias($oldParkURL);
        echo '<a href="/show_park.php?p=' . $park->nid . '" data-role="button" data-icon="arrow-r" data-theme="c" data-iconpos="right"><b>' . $park->title . '</b><br />';
      echo "<span class='results-city'>" . $park->field_location[LANGUAGE_NONE][0]["city"] . ", " . $park->field_location[LANGUAGE_NONE][0]["province"] . "</span>";
      echo '</a>';
      }
    }
  } else {
    echo "No parks matched your search criteria.";
  }
  echo "</div>";
}


function getParkAlias($src) {
  $query = db_query("SELECT alias FROM {url_alias} WHERE source = :src LIMIT 1", array(':src' => $src));
  while ($row = $query->fetchAssoc()) {
    $result = $row["alias"];
  }
  return $result;
}

?>