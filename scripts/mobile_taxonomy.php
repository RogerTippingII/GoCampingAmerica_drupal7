<?php

/* ********************************************** */
/* This script displays search filters for the    */
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

$vocabs = array("affiliation", "amenities", "credit-cards", "recreation", "lifestyles", "site-options", "services");
$terms = getTaxonomies($vocabs);

echo serialize($terms);

function getTaxonomies($vocabs) {
  foreach ($vocabs as $vocab) {
    switch ($vocab) {
      case "affiliation":
        $x = 1;
        break;
      case "amenities":
        $x = 2;
        break;
      case "credit-cards":
        $x = 3;
        break;
      case "lifestyles":
        $x = 6;
        break;
      case "site-options":
        $x = 18;
        break;
      case "recreation":
        $x = 5;
        break;
      case "services":
        $x = 17;
        break;
      default:
        $x = 0;
        break;
    }
    $query = db_query("SELECT * from {term_data} WHERE vid = %d", $x);
    while ($row = db_fetch_object($query)) {
	  $numParks = checkForParks($row->tid);
	  if ($numParks == 1) {
        $terms[$x][$row->tid][tid] = $row->tid;
        $terms[$x][$row->tid][name] = $row->name;
      }
	}
    //$x++;
  }
  return $terms;
}

function checkForParks($tid) {
  $query = db_query("SELECT DISTINCT nid FROM {term_node} WHERE tid = %d", $tid);
  $result = 0;
  while ($row = db_fetch_array($query)) {
    $result = 1;
  }
  return $result;
}

?>