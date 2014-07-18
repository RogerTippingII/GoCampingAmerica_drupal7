<?php
/**
 * Created by IntelliJ IDEA.
 * User: patricepaquette
 * Date: 2014-06-07
 * Time: 9:02 PM
 */

chdir($_SERVER['DOCUMENT_ROOT']);
define('DRUPAL_ROOT', __DIR__ . "/../..");
global $base_url;
$base_url = 'http://' . $_SERVER['HTTP_HOST'];
require_once './includes/bootstrap.inc';
require_once './includes/common.inc';
require_once './includes/module.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);
drupal_load('module', 'node');
module_invoke('node', 'boot');

$dev_rs = array();

db_set_active();

$rs = db_query("
  SELECT node.nid, pc.region
  FROM node
  JOIN  park_coordinates pc ON pc.nid = node.nid
  WHERE region IS NOT NULL
  LIMIT 100
  OFFSET ". $_GET["offset"]
);

$results = $rs->fetchAllAssoc('nid');
$nids = array_keys($results);

$nodes = node_load_multiple($nids);

foreach($nodes as $node){
  $node->field_region[LANGUAGE_NONE][0]["value"] = $results[intval($node->nid)]->region;
  node_save($node);
}
