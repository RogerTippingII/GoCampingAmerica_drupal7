<?php
/**
 * Created by IntelliJ IDEA.
 * User: patricepaquette
 * Date: 1/21/2014
 * Time: 12:37 AM
 */

chdir($_SERVER['DOCUMENT_ROOT']);
global $base_url;
$base_url = 'http://'.$_SERVER['HTTP_HOST'];
include_once './includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

$rs_parks = db_query("
  SELECT DISTINCT cc2.ARVC_ID, cc.CU_ID, node.nid, node.vid
  FROM cc_park_amenities_import cc
      JOIN cc_park_import cc2 ON cc2.CU_ID = cc.CU_ID
      JOIN cc_unmatched_parks_update cc3 ON cc3.name = cc2.ARVC_ID
      JOIN users ON users.name = cc2.ARVC_ID
      JOIN node ON node.uid = users.uid;"
);

while($row = db_fetch_array($rs_parks)){
  add_park_options($row["nid"], $row["vid"], $row["CU_ID"]);
  add_park_affiliations($row["nid"], $row["vid"], $row["CU_ID"]);
}

function add_park_options($nid, $vid, $CU_ID){
  include __DIR__ .'/cc_gca_options_mapping_var.php';

  //$rs_delete_current_amenities = db_query("DELETE FROM park_terms WHERE nid = %d", $nid);
  //$rs_delete_term_node = db_query("DELETE FROM term_node WHERE vid = %d", $vid);
  //$rs_delete_features = db_query("DELETE FROM park_feature_instance pfi WHERE pfi.fk_node = %d", $nid);
  //todo delete in term_node only the terms that are associated with park options

  $rs_options = db_query("  SELECT *
                              FROM cc_park_amenities_import c
                              WHERE c.CU_ID = %d", $CU_ID);

  $reportFeatures = array();
  while($option = db_fetch_array($rs_options)){
    $gca_option_name = $option["VAM_NAME"];
    $category = "Park Amenities";


    //get mapping and category
    foreach($cc_gca_options_mapping as $mapping_info){

      if(strtolower($mapping_info["Camp-Cal"]) == strtolower($option["VAM_NAME"])){

        if($mapping_info["GCA"] != ""){
          $gca_option_name = $mapping_info["GCA"];
        }
        $category = $mapping_info["Category"];
      }
    }


    $rs_amenity_select = db_query(" SELECT tid FROM term_data WHERE name = '%s'", $gca_option_name);
    $tid = db_result($rs_amenity_select);

    //var_dump($tid);
    //insert term into database if it doesn't exist
    if(!$tid){
      $rs_fid = db_query("
        SELECT id FROM park_feature WHERE name = '%s'",
        strtolower($option["VAM_NAME"])
      );

      $fid = db_result($rs_fid);

      if($fid){
        $reportFeatures["Found"][] = array("fid" => $fid, "name" => $option["VAM_NAME"]);

        //db_query("INSERT INTO park_feature_instance(fk_feature, fk_node) VALUES(%d, %d)", $fid, $nid);
      }
      else{
        $reportFeatures["NotFound"][] = array("fid" => $fid, "name" => $option["VAM_NAME"]);
      }

    }
    else{
      if($gca_option_name == "Picnic Area"){
        echo "adding : " . $gca_option_name ."\n";
      }
      //add the term to the associated park
      //$rs_insert_park_term = db_query(" INSERT INTO park_terms(nid, term) VALUES(%d, %d)", $nid, $tid);
      //$rs_insert_term_node = db_query(" INSERT INTO term_node(nid, vid, tid) VALUES(%d, %d, %d)", $nid, $vid, $tid);
    }
  }

  //var_dump($reportFeatures);
}

function add_park_affiliations($nid, $vid, $CU_ID){
  /*$rs_affiliations = db_query("  SELECT *
                              FROM cc_park_affiliations_import c
                              WHERE c.CU_ID = %d", $CU_ID);

  while($affiliation = db_fetch_array($rs_affiliations)){
    $gca_affiliation_name = $affiliation["VAF_NAME"];
    $category = "Park Affiliation";


    $rs_affiliation_select = db_query(" SELECT tid FROM term_data WHERE name = '%s'", $gca_affiliation_name);
    $tid = db_result($rs_affiliation_select);

    //var_dump($tid);
    //insert term into database if it doesn't exist
    if($tid){
      //add the term to the associated park
      $rs_insert_park_term = db_query(" INSERT INTO park_terms(nid, term) VALUES(%d, %d)", $nid, $tid);
      $rs_insert_term_node = db_query(" INSERT INTO term_node(nid, vid, tid) VALUES(%d, %d, %d)", $nid, $vid, $tid);
    }
    else{
      $rs_fid = db_query("
        SELECT pf.id
        FROM park_feature pf
        WHERE pf.name = '%s'", $gca_affiliation_name);

      $fid = db_result($rs_fid);

      if($fid){
        echo "Added affiliation : " . $gca_affiliation_name ."\n";
        db_query("INSERT INTO park_feature_instance(fk_node, fk_feature) VALUES(%d, %d)", $nid, $fid);
      }
    }
  }*/
}