<?php

/* ********************************************** */
/* This script receives gets the latest sponsor 
   for the mobile home page.                      */
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


$sponsors = getSponsors();
if ($sponsors) {
  $rand = rand(0, (count($sponsors) - 1));
  $nodeInfo = node_load($sponsors[$rand]);
  $sponsor["title"] = addslashes($nodeInfo->title);
  $sponsor["body"] = addslashes(str_replace(array("<p>", "</p>"), "", $nodeInfo->body));
  $sponsor["image"] = $nodeInfo->field_sponsor_image[0]["filepath"];
  $sponsor["link"] = $nodeInfo->field_sponsor_link[0]["value"];
  echo serialize($sponsor);
}
  
function getSponsors() {
  $timenow = date("Y-m-d", mktime());
  $timenow .= "T00:00:00";
  $query = db_query("SELECT DISTINCT nid FROM {content_type_mobile_sponsor} WHERE field_sponsor_run_value <= '%s' AND field_sponsor_run_value2 > '%s' ORDER BY vid DESC", $timenow, $timenow);
  while ($row = db_fetch_array($query)) {
    $result[] = $row["nid"];
  }
  if ($result) {
    return $result;
  }
  return;
}

?>