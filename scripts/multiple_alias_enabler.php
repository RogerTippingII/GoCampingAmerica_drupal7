<?php
/* ****************** */
/* SEARCH REPORTS     */
/* Created 08/26/2014 */
/* ****************** */


// DRUPAL BOOTSTRAP
chdir($_SERVER['DOCUMENT_ROOT']);
define('DRUPAL_ROOT', __DIR__ . "/..");
global $base_url;
$base_url = 'http://' . $_SERVER['HTTP_HOST'];
require_once './includes/bootstrap.inc';
require_once './includes/common.inc';
require_once './includes/module.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);
drupal_load('module', 'node');
module_invoke('node', 'boot');

require_once('scripts/PHPExcel.php');


ini_set('display_startup_errors',1);
ini_set('display_errors',1);
error_reporting(-1);
//ini_set('memory_limit','999M');

$query = new EntityFieldQuery();
$result = $query->entityCondition('entity_type', 'node')
  ->entityCondition('bundle', 'blog_post')
  ->execute();

//die ("<pre>".print_r($result['node'],true));  
$nodes = node_load_multiple(array_keys($result['node']));

foreach ($nodes as $node) {
  if ($node->path['pathauto'] == FALSE) {
    print "<br>Node {$node->nid} has pathauto: disabled";
  }
  
}

/*
// EXECUTE
  $node = node_load(17);
  node_save($node);
  pathauto_node_delete($node);
  node_load($node->nid);
*/
?>