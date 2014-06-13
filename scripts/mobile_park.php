<?php

/* ********************************************** */
/* This script displays park information for the  */
/* mobile site.                                   */
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

if (isset($_REQUEST["n"])) {
  $n = $_REQUEST["n"];
  showParkInfo($n);
} else {
  echo "No park was specified.";
}

function showParkInfo($n) {
  $node = node_load($n);

  $rs = db_query("SELECT ti.tid FROM taxonomy_index ti WHERE ti.nid = :nid", array(":nid" => $node->nid));
  $node->taxonomy = array();
  while($row = $rs->fetchAssoc()){
    $term = taxonomy_term_load($row["tid"]);
    $node->taxonomy[$term->tid] = $term;
  }

  echo serialize($node);
}

?>