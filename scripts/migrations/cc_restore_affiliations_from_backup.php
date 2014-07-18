<?php
/**
 * Created by IntelliJ IDEA.
 * User: patricepaquette
 * Date: 1/20/2014
 * Time: 1:55 AM
 */

chdir($_SERVER['DOCUMENT_ROOT']);
global $base_url;
$base_url = 'http://'.$_SERVER['HTTP_HOST'];
include_once './includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

$rs_backup_affiliations = db_query("
  SELECT *
  FROM cc_affiliations_backup"
);

while($row = db_fetch_array($rs_backup_affiliations)){
  $rs_node = db_query("
    SELECT * FROM node WHERE node.nid = %d",
    $row["nid"]
  );
  $node = db_fetch_array($rs_node);

  $rs_insert_park_terms = db_query("
    INSERT INTO park_terms(nid, term) VALUES(%d, %d)",
    $row["nid"], $row["term"]
  );

  $rs_insert_node_term = db_query("
    INSERT INTO term_node(nid, vid, tid) VALUES(%d, %d, %d)",
    $node["nid"], $node["vid"], $row["term"]
  );
}