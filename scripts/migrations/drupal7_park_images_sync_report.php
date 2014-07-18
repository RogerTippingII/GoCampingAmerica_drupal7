<?php
/**
 * Created by IntelliJ IDEA.
 * User: patricepaquette
 * Date: 2014-06-05
 * Time: 10:22 AM
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
  WHERE node.changed > 1396678265 AND node.type = 'camp'
  "
);

$legacy_nids = array();

foreach($legacy_rs as $legacy_row){
  $legacy_nids[] = $legacy_row->nid;
}

foreach($legacy_nids as $nid){
  echo "begin<br/>";
  db_set_active('legacy');

  $to_add = array();
  $to_delete = array();

  $legacy_images = db_query("
    SELECT *
    FROM node
    JOIN content_field_camp_slideshow cs ON cs.vid = node.vid
    JOIN files ON files.fid = cs.field_camp_slideshow_fid
    WHERE node.nid = :nid",
    array(":nid" => $nid)
  );

  db_set_active();

  $dev_images = db_query("
    SELECT *
    FROM node
    JOIN content_field_camp_slideshow cs ON cs.vid = node.vid
    JOIN files ON files.fid = cs.field_camp_slideshow_fid
    WHERE node.nid = :nid",
    array(":nid" => $nid)
  );

  $legacy_images = $legacy_images->fetchAllAssoc("filename");
  $dev_images = $dev_images->fetchAllAssoc("filename");

//  var_dump($legacy_images);
//  echo "<br/>";
//  var_dump($dev_images);

//  var_dump($legacy_images);
  echo "<br/>";

  foreach($legacy_images as $legacy_key => &$legacy_option){
    foreach($dev_images as $dev_key => &$dev_option){
      //echo $legacy_key . "<br/>";
      //echo $dev_key . "<br/><br/>";
      if($legacy_key == $dev_key){
        unset($legacy_images[$legacy_key]);
        unset($dev_images[$dev_key]);
      }
    }
  }

//  var_dump($legacy_images);
  echo "<br/>";

  if(count($legacy_images) > 0 || count($dev_images) > 0){
    echo $nid . "<br/>";
    echo "to add : ";
    foreach($legacy_images as $image){
      echo $image->filename . ", ";
    }
    echo "<br/><br/>";
    echo "to delete : ";
    foreach($dev_images as $image){
      echo $image->filename . ", ";
    }


    echo "<br/><br/>";
  }

  unset($legacy_images);
  unset($dev_images);
}
