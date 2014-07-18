<?php
/**
 * Created by IntelliJ IDEA.
 * User: patricepaquette
 * Date: 1/19/2014
 * Time: 10:27 PM
 */

chdir($_SERVER['DOCUMENT_ROOT']);
global $base_url;
$base_url = 'http://'.$_SERVER['HTTP_HOST'];
include_once './includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

include __DIR__ .'/cc_gca_options_mapping_var.php';

$cc_gca_options_mapping = $cc_gca_options_mapping;


$rs_options_to_remove = db_query("
  SELECT DISTINCT td.tid, td.name, v.name category
  FROM term_data td
  JOIN vocabulary v ON v.vid = td.vid
  JOIN cc_park_amenities_import cc ON cc.VAM_NAME = td.name
  WHERE td.tid NOT IN
    (
        SELECT tid FROM cc_gca_common_options
    );"
);

while($row = db_fetch_array($rs_options_to_remove)){
  $rs_delete_park_terms = db_query("
    DELETE FROM park_terms WHERE term = %d",
    $row["tid"]
  );

  if(!$rs_delete_park_terms){
    echo "Could not delete park_term ". $row["tid"];
  }

  $rs_delete_node_terms = db_query("
    DELETE FROM term_node WHERE tid = %d",
    $row["tid"]
  );

  if(!$rs_delete_node_terms){
    echo "Could not delete term_node ". $row["tid"];
  }

  $rs_delete_term = db_query("
    DELETE FROM term_data WHERE tid = %d",
    $row["tid"]
  );

  if(!$rs_delete_term){
    echo "Could not delete term_data ". $row["tid"];
  }
}