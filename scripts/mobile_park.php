<?php

/* ********************************************** */
/* This script displays park information for the  */
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

if (isset($_REQUEST["n"])) {
  $n = $_REQUEST["n"];
  showParkInfo($n);
} else {
  echo "No park was specified.";
}

function showParkInfo($n) {
  $nodeInfo = serialize(node_load($n));
  echo $nodeInfo;
}

?>