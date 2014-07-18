<?php
/**
 * Created by IntelliJ IDEA.
 * User: patricepaquette
 * Date: 2014-06-14
 * Time: 5:14 PM
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

$query = "SELECT node.nid FROM node JOIN field_data_field_camp_status f1 ON f1.entity_id = node.nid WHERE f1.field_camp_status_value = ''";

$results = db_query($query);

while($row = $results->fetchAssoc()){
  var_dump($row);

  $node = node_load($row["nid"]);

  db_set_active('legacy');

  $status = db_query("SELECT c.field_camp_status_value FROM node JOIN content_type_camp c ON c.vid = node.vid WHERE node.title = :title", array(':title' => $node->title))->fetchField();

  db_set_active();


  if($status){
    var_dump($status);
    $node->field_camp_status[LANGUAGE_NONE][0]["value"] = $status;
    node_save($node);
  }

}