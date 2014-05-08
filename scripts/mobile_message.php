<?php

/* ********************************************** */
/* This script receives gets the latest message 
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


$message = getMessage();
$msgInfo = node_load($message);
$result["title"] = $msgInfo->title;
$result["body"] = $msgInfo->body;
//$msgInfo = node_load(109102);

echo serialize($result);

function getMessage() {
  $query = db_query("SELECT nid FROM {node} WHERE type = 'mobile_message' ORDER BY created DESC LIMIT 1");
  while ($row = db_fetch_array($query)) {
    $result = $row["nid"];
  }
  if ($result) {
    return $result;
  }
  return;
}

?>