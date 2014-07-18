<?php
/**
 * Created by IntelliJ IDEA.
 * User: patricepaquette
 * Date: 1/20/2014
 * Time: 1:12 AM
 */

chdir($_SERVER['DOCUMENT_ROOT']);
global $base_url;
$base_url = 'http://'.$_SERVER['HTTP_HOST'];
include_once './includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

$rs_park_features = db_query("
  SELECT *
  FROM park_feature_instance
  ORDER BY fk_node, fk_feature"
);

$rowBuffer = array();

while($row = db_fetch_array($rs_park_features)){
  if($row["fk_feature"] == $rowBuffer["fk_feature"]){
    db_query("
      DELETE FROM park_feature_instance WHERE id = %d",
      $row["id"]
    );
  }

  $rowBuffer = $row;
}