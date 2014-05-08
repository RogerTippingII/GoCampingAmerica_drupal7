<?php

error_reporting(-1);

/* *********************************** */
/* This script is run by cron and      */
/* resets tier packages to "1"         */
/* (standard) if package has expired.  */
/* *********************************** */

// Bootstrap
chdir($_SERVER['DOCUMENT_ROOT']);
global $base_url;
$base_url = 'http://'.$_SERVER['HTTP_HOST'];
require_once './includes/bootstrap.inc';
require_once './includes/common.inc';
require_once './includes/module.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

$parks = getParks();
$timenow = mktime();
foreach ($parks as $park) {
  if (strtotime($park["expiration"]) < $timenow) {
    echo $park["nid"] . " has expired.<br />";
	resetTier($park["nid"]);
  }
}
echo "<pre>";
print_r($parks);
echo "</pre>";

function resetTier($nid) {
  echo "resetTier called. <br />";
  $nodeInfo = node_load($nid);
  $nodeInfo->field_park_tier[0]["value"] = 1;
  node_save($nodeInfo);
}

function getParks() {
  $query = db_query("SELECT DISTINCT nid FROM {content_type_camp} WHERE field_camp_status_value = 'Active'");
  $x = 0;
  while ($row = db_fetch_array($query)) {
    $tier = getTier($row["nid"]);
	if ($tier != 1) {
      $result[$x]["nid"] = $row["nid"];
	  $result[$x]["vid"] = getVid($row["nid"]);
	  $result[$x]["tier"] = $tier;
	  $result[$x]["expiration"] = getExpiration($row["nid"], $result[$x]["vid"]);
	  $x++;
	}
  }
  return $result;
}

function getTier($nid) {
  $vid = getVid($nid);
  $query = db_query("SELECT field_park_tier_value FROM {content_type_camp} WHERE nid = %d AND vid = %d LIMIT 1", $nid, $vid);
  while ($row = db_fetch_array($query)) {
    $result = $row["field_park_tier_value"];
  }
  if ($result) {
    return $result;
  } else {
    return;
  }
}

function getVid($nid) {
  $query = db_query("SELECT vid FROM {content_type_camp} WHERE nid = %d ORDER BY vid DESC LIMIT 1", $nid);
  while ($row = db_fetch_array($query)) {
    $result = $row["vid"];
  }
  if ($result) {
    return $result;
  } else {
    return;
  }
}

function getExpiration($nid, $vid) {
  $query = db_query("SELECT field_park_tier_expiration_value FROM {content_type_camp} WHERE nid = %d AND vid = %d ORDER BY vid DESC", $nid, $vid);
  while ($row = db_fetch_array($query)) {
    $result = $row["field_park_tier_expiration_value"];
  }
  if ($result) {
    return $result;
  } else {
    return;
  }
}

?>