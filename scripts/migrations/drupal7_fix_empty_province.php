<?php
/**
 * Created by IntelliJ IDEA.
 * User: patricepaquette
 * Date: 2014-06-14
 * Time: 11:36 AM
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

db_set_active();

$query = "SELECT node.nid FROM node JOIN location_instance li ON li.vid = node.vid JOIN location l ON l.lid = li.lid WHERE l.province = ''";

$results = db_query($query);

while($row = $results->fetchAssoc()){
  $node = node_load($row["nid"]);

  db_set_active('legacy');

  $province = db_query("SELECT l.province FROM node JOIN location_instance li ON li.vid = node.vid JOIN location l ON l.lid = li.lid WHERE node.nid = :nid", array(":nid" => $row["nid"]))->fetchField();

  db_set_active();

  echo $province . "<br/>";
  if($province){
    $node->field_location[LANGUAGE_NONE][0]["province"] = $province;
  }
  node_save($node);
}
