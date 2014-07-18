<?php
/**
 * Created by IntelliJ IDEA.
 * User: patricepaquette
 * Date: 2014-06-04
 * Time: 10:33 PM
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
  SELECT
    u.name arvcid,
    n.nid,
    n.title,
    cc.*,
    l.street,
    l.city,
    l.province,
    l.postal_code,
    l.country,
    l.latitude,
    l.longitude
  FROM node n
  JOIN users u ON u.uid = n.uid
  JOIN content_type_camp cc ON cc.vid = (SELECT vid FROM content_type_camp WHERE nid = n.nid ORDER BY vid LIMIT 1)
  JOIN location_instance li ON li.vid = n.vid
  JOIN location l ON l.lid = li.lid
  WHERE n.changed > 1396678265
  ORDER BY n.nid DESC"
);

//$legacy_nids = array();
//
//foreach($legacy_rs as $legacy_row){
//  $legacy_nids[] = $legacy_row->nid;
//}

db_set_active();


$counter = 1;
while($legacy_row = $legacy_rs->fetchAssoc()){
  $rs = db_query("
    SELECT
        u.name arvcid,
        n.nid,
        n.title,
        cc.*,
        l.street,
        l.city,
        l.province,
        l.postal_code,
        l.country,
        l.latitude,
        l.longitude
    FROM node n
    JOIN users u ON u.uid = n.uid
    JOIN content_type_camp cc ON cc.vid = (SELECT vid FROM content_type_camp WHERE nid = n.nid ORDER BY vid LIMIT 1)
    JOIN location_instance li ON li.vid = n.vid
    JOIN location l ON l.lid = li.lid
    WHERE n.nid = :nid",
    array(":nid" => $legacy_row["nid"])
  );



  if($rs->rowCount() == 0){
    echo "<strong>" . $counter++ . ". " . $legacy_row["nid"] . " - " . $legacy_row["title"] . "</strong>" . "<br/>";
    echo "NEW<br/>";
    foreach($legacy_row as $key => $val){
      echo $key . ": " . $val . "<br/>";
    }
    echo "<br/>";
  }
  else{
    $echo = false;
    $toecho = "<strong>" . $counter . ". " . $legacy_row["nid"] . " - " . $legacy_row["title"] . "</strong>" . "<br/>";
    while($row = $rs->fetchAssoc()){
      $keys = array_keys($legacy_row);

      foreach($keys as $key){
        if($legacy_row[$key] != $row[$key] && $key != "field_camp_lagacy_id_value"){
          $echo = true;
          $toecho .= $key . ":<br/>" . $row[$key] . " <br/> -> <br/> " . $legacy_row[$key] . "<br/><br/>";
        }
      }
    }

    if($echo){
      ++$counter;
      echo $toecho;
      echo "<br/>";
    }
  }


}