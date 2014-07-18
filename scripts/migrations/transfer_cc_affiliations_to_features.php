<?php
/**
 * Created by IntelliJ IDEA.
 * User: patricepaquette
 * Date: 1/20/2014
 * Time: 12:13 AM
 */

chdir($_SERVER['DOCUMENT_ROOT']);
global $base_url;
$base_url = 'http://'.$_SERVER['HTTP_HOST'];
include_once './includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

$affiliations = array("Privately Operated Parks", "Municipal Parks");

foreach($affiliations as $affiliation){
  $rs_tid = db_query("
    SELECT tid FROM term_data WHERE name = '%s'",
    $affiliation
  );
  $tid = db_result($rs_tid);

  $insert = db_query("
    INSERT INTO park_feature(name, fk_category) VALUES('%s', %d)",
    $affiliation, 6
  );

  if($insert && $tid){
    $aff_id = db_last_insert_id("park_feature", "id");

    $rs_nodes = db_query("
      SELECT node.nid
      FROM node
      JOIN park_terms pt ON pt.nid = node.nid
      JOIN term_data td ON td.tid = pt.term
      WHERE td.tid = %d",
      $tid
    );

    while($row_node = db_fetch_array($rs_nodes)){
      $insert_park_aff = db_query("
        INSERT INTO park_feature_instance(fk_feature, fk_node) VALUES(%d, %d)",
        $aff_id, $row_node["nid"]
      );
    }

    //delete from park_terms
    $rs_delete_park_terms = db_query("
    DELETE FROM park_terms WHERE term = %d",
      $tid
    );

    if(!$rs_delete_park_terms){
      echo "Could not delete park_term ". $tid;
    }

    $rs_delete_node_terms = db_query("
    DELETE FROM term_node WHERE tid = %d",
      $tid
    );

    if(!$rs_delete_node_terms){
      echo "Could not delete term_node ". $tid;
    }

    $rs_delete_term = db_query("
    DELETE FROM term_data WHERE tid = %d",
      $tid
    );

    if(!$rs_delete_term){
      echo "Could not delete term_data ". $tid;
    }
  }
}