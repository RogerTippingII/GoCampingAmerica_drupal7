<?php
/**
 * Created by IntelliJ IDEA.
 * User: patricepaquette
 * Date: 12/28/2013
 * Time: 4:38 PM
 */

chdir($_SERVER['DOCUMENT_ROOT']);
global $base_url;
$base_url = 'http://'.$_SERVER['HTTP_HOST'];
include_once './includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

$rs_select_park_options  = db_query("
  SELECT *, pf.id feature_id
  FROM node
  JOIN park_terms pt ON pt.nid = node.nid
  JOIN term_data td ON td.tid = pt.term
  JOIN location_instance li ON li.vid = node.vid
  JOIN location l ON li.lid = l.lid
  JOIN park_feature pf ON td.name = pf.name
  WHERE l.province = 'CA'");

while($row_park_option = db_fetch_array($rs_select_park_options)){
  db_query("
    INSERT INTO park_feature_instance(fk_feature, fk_node) VALUES(%d, %d)",
    $row_park_option["feature_id"],
    $row_park_option["nid"]
  );
}