<?php
/**
 * Created by IntelliJ IDEA.
 * User: patricepaquette
 * Date: 2014-07-09
 * Time: 5:34 PM
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

$results = db_query("
  SELECT *
  FROM cc_summary_directions cc
  JOIN users ON users.name = cc.arvcid
  JOIN node ON node.uid = users.uid
")->fetchAllAssoc("nid");

$node_ids = array_keys($results);

$nodes = node_load_multiple($node_ids);

foreach($nodes as $node){
  $save = false;

  if(count($node->field_park_description) == 0 || trim($node->field_park_description[LANGUAGE_NONE][0]["value"]) == ""){
    $node->field_park_description[LANGUAGE_NONE] = array(array("value" => $results[$node->nid]->summary));
    $save = true;
  }

  if(count($node->field_camp_directions) == 0 || trim($node->field_camp_directions[LANGUAGE_NONE][0]["value"]) == ""){
    $node->field_camp_directions[LANGUAGE_NONE] = array(array("value" => $results[$node->nid]->directions));
    $save = true;
  }

  if($save){
    node_save($node);
  }
}