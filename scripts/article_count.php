<?php

/* Update article_count */

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

if (isset($_REQUEST["n"])) {
  $nid = $_REQUEST["n"];
  $time["now"] = mktime();
  $time["start"] = $time["now"] - 86400;
  $ip = $_REQUEST["i"];
  
  /* Check whether user has visited the page in past 24 hours */
  $checkFlag = checkVisit($nid, $time, $ip);
  
  if ($checkFlag != 1) {
    recordData($nid, $time, $ip);
	echo "Data recorded.";
  } else {
    echo "Visitor repeated. Skipping.";
  }
  

} else {
  echo "Data invalid. Skipping.";
}

// Remove data older than 60 days
cleanUpTable();

function cleanUpTable() {
  $targetTime = mktime() - (86400 * 60);
  db_query("DELETE FROM {article_count} WHERE timestamp < %d", $targetTime);
}

function recordData($nid, $time, $ip) {
  db_query("INSERT INTO {article_count} (nid, user, timestamp) values (%d, '%s', %d)", $nid, $ip, $time["now"]);
}

function checkVisit($nid, $time, $ip) {
  $query = db_query("SELECT nid FROM {article_count} WHERE nid = %d AND timestamp > %d AND user = '%s' LIMIT 1", $nid, $time["start"], $ip);
  while ($row = db_fetch_array($query)) {
    $result = 1;
  }
  if ($result) {
    return 1;
  }
  return 0;
}

?>