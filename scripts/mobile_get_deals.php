<?php

/* ********************************************** */
/* This script displays deals for the  */
/* mobile site.                                   */
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

// First get all active deals

$deals = getDeals();
/*
echo "<pre>";
print_r($deals);
echo "</pre>";
*/

// Convert deals array into parks array

foreach ($deals as $deal) {
  foreach ($deal["parks"] as $dealPark) {
    $tempParks[$dealPark]["deal"][] = $deal["dnid"];
  }
}

foreach ($tempParks as $key => $value) {
  $parks[$key]["tier"] = getTier($key);
  if ($parks[$key]["tier"] == 4) {
    $parks[$key]["tier"] = 0; // This is for the aasort below to push listing to the top
  }
  $parks[$key]["nid"] = $key;
  $parks[$key]["vid"] = getVid($key);
  $parks[$key]["title"] = getTitle($key);
  $parks[$key]["location"] = getLocation($key, $parks[$key]["vid"]);
  $x = 0;
  foreach ($value["deal"] as $tempDeal) {
    $parks[$key]["deals"][$x]["dnid"] = $tempDeal;
	$parks[$key]["deals"][$x]["dtitle"] = getTitle($tempDeal);
	$x++;
  }
}

aasort($parks, "tier");

$serialized = serialize($parks);

echo $serialized;

/* ********* */
/* FUNCTIONS */
/* ********* */

function getLocation($nid, $vid) {
  $lid = getLid($nid, $vid); 
  $query = db_query("SELECT city, province FROM {location} WHERE lid = %d LIMIT 1", $lid);
  while ($row = db_fetch_array($query)) {
    $result["city"] = $row["city"];
	$result["province"] = $row["province"];
  }
  if ($result) {
    return $result;
  }
  return;
}

function getLid($nid, $vid) {
  $query = db_query("SELECT lid FROM {location_instance} WHERE nid = %d AND vid = %d LIMIT 1", $nid, $vid);
  while ($row = db_fetch_array($query)) {
    $result = $row["lid"];
  }
  if ($result) {
    return $result;
  }
  return;
}

function getTier($nid) {
  $query = db_query("SELECT field_park_tier_value FROM {content_type_camp} WHERE nid = %d ORDER BY vid DESC LIMIT 1", $nid);
  while ($row = db_fetch_array($query)) {
    $result = $row["field_park_tier_value"];
  }
  if ($result) {
    return $result;
  }
  return;
}

function getDeals() {
  $timenow = date("Y-m-d" . "T" . "G:i:s", mktime());
  $query = db_query("SELECT nid, field_deal_sid_value FROM {content_type_deal} WHERE field_deal_start_value < '%s' AND field_deal_end_value > '%s'", $timenow, $timenow);
  $x = 0;
  while ($row = db_fetch_array($query)) {
    if ($row["field_deal_sid_value"]) {
      $result[$x]["dnid"] = $row["nid"];
	  $result[$x]["dsid"] = $row["field_deal_sid_value"];
	  $result[$x]["parks"] = getParks($row["field_deal_sid_value"]);
	  $x++;
	}
  }
  if ($result) {
    return $result;
  }
  return;
}

function checkOptin($nid) {
  $query = db_query("SELECT field_park_state_assn_optin_value FROM {content_type_camp} WHERE nid = %d ORDER BY vid DESC LIMIT 1", $nid);
  while ($row = db_fetch_array($query)) {
    $result = $row["field_park_state_assn_optin_value"];
  }
  if ($result) {
    return $result;
  }
  return;
}

function getParks($said) {
  $query = db_query("SELECT nid FROM {content_type_camp} WHERE field_camp_state_assnid_value = %d", $said);
  $parkArray = array();
  while ($row = db_fetch_array($query)) {
    $optin = checkOptin($row["nid"]);
	if ($optin == "on") {
      if (!in_array($row["nid"], $parkArray)) {
        $parks[] = $row["nid"];
	    $parkArray[] = $row["nid"];
      }
	}
  }
  array_unique($parks);
  //$x = 0;
  foreach ($parks as $park) {
    if (getTitle($park)) {
      $vid = getVid($park);
	  $status = getStatus($park, $vid);
	  $drupalStatus = getDrupalStatus($park);
	  if (($status == "active" || $status == "Active") && $drupalStatus == 1) {
	    $result[] = $park;
        //$result[$x]["pnid"] = $park;
	    //$result[$x]["pvid"] = $vid;
	    //$result[$x]["title"] = getTitle($park);
	    //$x++;
	  }
	} 
  }
  if ($result) {
    return $result;
  }
  return;
}

function getVid($nid) {
  $query = db_query("SELECT vid FROM {content_type_camp} WHERE nid = %d ORDER BY vid DESC LIMIT 1", $nid);
  while ($row = db_fetch_array($query)) {
    $result = $row["vid"];
  }
  if ($result) {
    return $result;
  }
  return;
}

function getStatus($nid, $vid) {
  $query = db_query("SELECT field_camp_status_value FROM {content_type_camp} WHERE nid = %d AND vid = %d LIMIT 1", $nid, $vid);
  while ($row = db_fetch_array($query)) {
    $result = $row["field_camp_status_value"];
  }
  if ($result) {
    return $result;
  }
  return;
}

function getInfo($nid) {
  $query = db_query("SELECT vid FROM {content_type_park} WHERE nid = %d ORDER BY vid DESC LIMIT 1", $nid);
  while ($row = db_fetch_array($query)) {
    $result["vid"] = $row["vid"];
	$result["title"] = getTitle($nid, $row["vid"]);
  }
  if ($result) {
    return $result;
  }
  return;
}

function getTitle($nid) {
  $query = db_query("SELECT title FROM {node} WHERE nid = %d ORDER BY vid DESC LIMIT 1", $nid);
  while ($row = db_fetch_array($query)) {
    $result = $row["title"];
  }
  if ($result) {
    return $result;
  }
  return;
}

function getDrupalStatus($nid) {
  $query = db_query("SELECT status FROM {node} WHERE nid = %d ORDER BY vid DESC LIMIT 1", $nid);
  while ($row = db_fetch_array($query)) {
    $result = $row["status"];
  }
  if ($result) {
    return $result;
  }
  return;
}

function aasort (&$array, $key) {
    $sorter=array();
    $ret=array();
    reset($array);
    foreach ($array as $ii => $va) {
        $sorter[$ii]=$va[$key];
    }
    asort($sorter);
    foreach ($sorter as $ii => $va) {
        $ret[$ii]=$array[$ii];
    }
    $array=$ret;
}

?>