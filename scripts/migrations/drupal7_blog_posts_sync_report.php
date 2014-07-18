<?php
/**
 * Created by IntelliJ IDEA.
 * User: patricepaquette
 * Date: 2014-06-05
 * Time: 10:13 AM
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

db_set_active('legacy');

$legacy_rs = db_query("
  SELECT * FROM node
  JOIN content_type_blog_post cc ON cc.vid = node.vid
  WHERE node.changed > 1396678265"
);

//$legacy_nids = array();
//
//foreach($legacy_rs as $legacy_row){
//  $legacy_nids[] = $legacy_row->nid;
//}

db_set_active();


$counter = 0;
while($legacy_row = $legacy_rs->fetchAssoc()){
  $rs = db_query("
    SELECT * FROM node
    JOIN content_type_blog_post cc ON cc.vid = node.vid
    WHERE node.nid = :nid",
    array(":nid" => $legacy_row["nid"])
  );

  echo ++$counter . ". " . $legacy_row["nid"] . "<br/>";

  if($rs->rowCount() == 0){
    echo "NEW<br/>";
    foreach($legacy_row as $key => $val){
      echo $key . ":" . $val . "<br/>";
    }
  }
  else{
    while($row = $rs->fetchAssoc()){
      $keys = array_keys($legacy_row);

      foreach($keys as $key){
        if($legacy_row[$key] != $row[$key]){
          echo $key . ":" . $row[$key] . " -> " . $legacy_row[$key] . "<br/>";
        }
      }
    }
  }

  echo "<br/>";
}