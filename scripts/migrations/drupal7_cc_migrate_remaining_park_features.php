<?php
/**
 * Created by IntelliJ IDEA.
 * User: patricepaquette
 * Date: 2014-07-17
 * Time: 1:27 PM
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

$query = db_query("
  SELECT node.nid, cc.name, cc.tid, cc.vid
  FROM node
  JOIN users ON users.uid = node.uid
  JOIN cc_remaining_features cc ON cc.arvcid = users.name
  ORDER BY node.nid ASC, cc.name ASC");

$parks_features = array();
foreach($query as $feature){
  if(!isset($parks_features[$feature->nid])){
    $parks_features[$feature->nid] = array();
  }

  $parks_features[$feature->nid][] = $feature;
}

$counter = 0;

foreach($parks_features as $park_nid => $features){
  $edited = false;
  $node = node_load($park_nid, null, true);

  foreach($features as $feature){
    $exists = false;
    $voc_field = "taxonomy_vocabulary_" . $feature->vid;
//    var_dump($node->{$voc_field});

    foreach($node->{$voc_field}[LANGUAGE_NONE] as $node_feature){
//      var_dump($node_feature);
//      var_dump($feature);
      if($node_feature["tid"] == $feature->tid){
        $exists = true;
        break;
      }
    }

    if(!$exists){
      $edited = true;

      echo "before";
      var_dump($node->{$voc_field}[LANGUAGE_NONE]);
      $node->{$voc_field}[LANGUAGE_NONE][] = array("tid" => $feature->tid);
      echo "after";
      var_dump($node->{$voc_field}[LANGUAGE_NONE]);
    }
  }

  if($edited){
    node_save($node);
    $counter++;
  }
//
//  var_dump($features);
}

echo $counter . " saved";

//var_dump($parks_features);