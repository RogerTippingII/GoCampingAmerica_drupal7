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

$c = $_REQUEST["c"];
$cat = taxonomy_vocabulary_load($c);
echo $cat->name;

?>