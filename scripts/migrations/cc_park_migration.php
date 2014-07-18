<?php
chdir($_SERVER['DOCUMENT_ROOT']);
global $base_url;
$base_url = 'http://'.$_SERVER['HTTP_HOST'];
include_once './includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);




$rs = db_query("SELECT users.uid, users.name, node.nid, node.vid, c.nid camp_nid, c.vid camp_vid, pi.*
                FROM cc_park_import pi
                LEFT JOIN node ON node.title = pi.POCU_NAME
                LEFT JOIN users ON node.uid = users.uid
                LEFT JOIN content_type_camp c ON c.vid = node.vid
                WHERE pi.PRIMARY = 'y';");
while($row = db_fetch_array($rs)){
  //todo update if user/park already exists
  $account = NULL;

  if($row["uid"] == NULL){
    $account = user_save(NULL, array(
      "name" => $row["CU_ID"],
      "pass" => "arvc",
      "mail" => $row["POCU_CONSUMER_EMAIL_ADDRESS"],
      "status" => 1,
      "init" => $row["POCU_CONSUMER_EMAIL_ADDRESS"]
    ));
  }else{
    $account = user_load(array("uid" => $row["uid"]));
    $account = user_save($account, array(
      "name" => $row["CU_ID"],
      "mail" => $row["POCU_CONSUMER_EMAIL_ADDRESS"]
    ));
  }

  $camp_fields = array( "vid", "nid", "field_camp_website_url", "field_camp_status_value", "field_camp_lagacy_id_value",
                        "field_camp_email_email", "field_camp_stateassn_user_uid",
                        "field_park_official_optin_value", "field_park_tier_value", "field_park_guest_reviews_optin_value",
                        "field_camp_tollfree_phone_number");
  $location_fields = array( "street", "additional", "city", "province", "postal_code", "country", "latitude", "longitude");

  $campEmail = ($row["POCU_SUPPORT_EMAIL_ADDRESS"])?$row["POCU_SUPPORT_EMAIL_ADDRESS"]:$account->mail;

  if($row["nid"] == NULL){ //DOES NOT EXIST
    $rs_vid = db_query("SELECT AUTO_INCREMENT FROM information_schema.tables WHERE table_name = 'node_revisions' AND table_schema = DATABASE()");
    $vid = db_result($rs_vid);

    echo $vid ."\n";

    $rs_insert_node = db_query("INSERT INTO node(vid, title, type, uid, comment) VALUES(%d, '%s', 'camp', %d, 2)", $vid, $account->name, $account->uid);

    $nid = db_last_insert_id("node", "nid");
    $rs_insert_node_revision = db_query("INSERT INTO node_revisions(nid, uid, title) VALUES(%d, %d, '%s')", $nid, $account->uid, $account->name);

    //insert new camp info

    $query = "INSERT INTO content_type_camp(". implode(',', $camp_fields) .")
                              VALUES(%d, %d ,'%s', 'inactive', %d, '%s', %d, 'no', 1, 'no', '%s')";

    $rs_insert_camp = db_query($query ,
      $vid, $nid, $row["POCU_HOSTED_WEB_URL"], $row["CU_ID"], $campEmail, $account->uid,
      $row["POCU_TOLL_FREE_NUMBER"]);

    $rs_insert_location = db_query("INSERT INTO location(". implode(',', $location_fields) .")
                                  VALUES('%s', '%s', '%s', '%s', '%s', '%s', %d, %d)",
      $row["POCU_PHYSICAL_ADDRESS1"], $row["POCU_PHYSICAL_ADDRESS2"], $row["POCU_PHYSICAL_CITY"],
      $row["POCU_PHYSICAL_STATE_PROVINCE"], $row["POCU_PHYSICAL_POSTAL_CODE"], $row["POCU_PHYSICAL_COUNTRY"],
      $row["POCU_LATITUDE"], $row["POCU_LONGITUDE"]);

    $rs_insert_park_coordinates = db_query("INSERT INTO park_coordinates( nid,
                                                                          lat,
                                                                          lng)
                                            VALUES(%d, %d, %d)",
      $nid, $row["POCU_LATITUDE"], $row["POCU_LONGITUDE"]);

    $lid = db_last_insert_id("location", "lid");

    $rs_insert_location_phone = db_query("INSERT INTO content_field_camp_phone( vid,
                                                                                nid,
                                                                                field_camp_phone_number
                                                                              )
                                        VALUES(%d, %d, %d)",
      $vid, $nid, $row["POCU_PHONE_NUMBER"]);

    $rs_insert_location_instance = db_query("INSERT INTO location_instance( vid,
                                                                            nid,
                                                                            lid
                                                                          )
                                           VALUES(%d, %d, %d)",
      $vid, $nid, $lid);

    $rs_insert_field_location = db_query("INSERT INTO content_field_location( vid,
                                                                              nid,
                                                                              lid
                                                                            )
                                        VALUES(%d, %d, %d)",
      $vid, $nid, $lid);


  }
  else{ //EXISTS
    $nid = $row["nid"];
    $vid = $row["vid"];

    $rs_update_camp = db_query("UPDATE content_type_camp
                                SET field_camp_website_url = '%s',
                                    field_camp_lagacy_id_value = %d,
                                    field_camp_email_email = '%s',
                                    field_camp_tollfree_phone_number = '%s'
                                WHERE nid = %d AND vid = %d",
                                $row["POCU_HOSTED_WEB_URL"], $row["CU_ID"], $campEmail, $row["POCU_TOLL_FREE_NUMBER"], $nid, $vid
    );

    $rs_update_location = db_query("UPDATE location
                                    JOIN location_instance li ON li.lid = location.lid
                                    SET street = '%s',
                                        additional = '%s',
                                        city = '%s',
                                        province = '%s',
                                        postal_code = '%s',
                                        country = '%s',
                                        latitude = %d,
                                        longitude = %d
                                    WHERE li.nid = %d AND li.vid = %d",
      $row["POCU_PHYSICAL_ADDRESS1"], $row["POCU_PHYSICAL_ADDRESS2"], $row["POCU_PHYSICAL_CITY"],
      $row["POCU_PHYSICAL_STATE_PROVINCE"], $row["POCU_PHYSICAL_POSTAL_CODE"], $row["POCU_PHYSICAL_COUNTRY"],
      $row["POCU_LATITUDE"], $row["POCU_LONGITUDE"], $nid, $vid
    );

    $rs_check_park_coordinates_exists = db_query("SELECT COUNT(nid) FROM park_coordinates WHERE nid = %d", $nid);
    if($rs_check_park_coordinates_exists){
      $count = db_result($rs_check_park_coordinates_exists);
    }
    else{
      $count = 0;
    }

    if(count > 0){
      $rs_update_park_coordinates = db_query("UPDATE park_coordinates
                                              SET lat = %d,
                                                  lng = %d,
                                              WHERE nid = %d",
        $row["POCU_LATITUDE"], $row["POCU_LONGITUDE"], $nid);
    }
    else{
      $rs_insert_park_coordinates = db_query("INSERT INTO park_coordinates( nid,
                                                                          lat,
                                                                          lng)
                                            VALUES(%d, %d, %d)",
        $nid, $row["POCU_LATITUDE"], $row["POCU_LONGITUDE"]);
    }

    $rs_update_phone = db_query(" UPDATE content_field_camp_phone
                                  SET field_camp_phone_number = '%s'
                                  WHERE nid = %d AND vid = %d",
                                  $row["POCU_PHONE_NUMBER"], $nid, $vid
    );
  }

  //PARK AMENITIES AND AFFILIATIONS
  add_park_options($nid, $vid, $row["CU_ID"]);

  //PARK AFFILIATIONS
  add_park_affiliations($nid, $vid, $row["CU_ID"]);
}

function add_park_options($nid, $vid, $CU_ID){
  include __DIR__ .'/cc_gca_options_mapping_var.php';

  $rs_delete_current_amenities = db_query("DELETE FROM park_terms WHERE nid = %d", $nid);
  //todo delete in term_node only the terms that are associated with park options

  $rs_options = db_query("  SELECT *
                              FROM cc_park_amenities_import c
                              WHERE c.CU_ID = %d", $CU_ID);

  while($option = db_fetch_array($rs_options)){
    $gca_option_name = $option["VAM_NAME"];
    $category = "Park Amenities";

    //get mapping and category
    foreach($cc_gca_options_mapping as $mapping_info){

      if($mapping_info["Camp-Cal"] == $option["VAM_NAME"]){
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
      $rs_voc_id = db_query(" SELECT vid FROM vocabulary WHERE name = '%s'", $category);
      $vid = db_result($rs_voc_id);

      $rs_insert_term = db_query("  INSERT INTO term_data(vid, name) VALUES(%d, '%s')", $vid, $option["VAM_NAME"]);

      $tid = db_last_insert_id("term_data", "tid");
    }

    //add the term to the associated park
    $rs_insert_park_term = db_query(" INSERT INTO park_terms(nid, term) VALUES(%d, %d)", $nid, $tid);
    $rs_insert_term_node = db_query(" INSERT INTO term_node(nid, vid, tid) VALUES(%d, %d, %d)", $nid, $vid, $tid);

  }
}

function add_park_affiliations($nid, $vid, $CU_ID){
  $rs_affiliations = db_query("  SELECT *
                              FROM cc_park_affiliations_import c
                              WHERE c.CU_ID = %d", $CU_ID);

  while($affiliation = db_fetch_array($rs_affiliations)){
    $gca_affiliation_name = $affiliation["VAF_NAME"];
    $category = "Park Affiliation";


    $rs_affiliation_select = db_query(" SELECT tid FROM term_data WHERE name = '%s'", $gca_affiliation_name);
    $tid = db_result($rs_affiliation_select);

    //var_dump($tid);
    //insert term into database if it doesn't exist
    if(!$tid){
      $rs_voc_id = db_query(" SELECT vid FROM vocabulary WHERE name = '%s'", $category);
      $vid = db_result($rs_voc_id);

      $rs_insert_term = db_query("  INSERT INTO term_data(vid, name) VALUES(%d, '%s')", $vid, $affiliation["VAF_NAME"]);

      $tid = db_last_insert_id("term_data", "tid");
    }

    //add the term to the associated park
    $rs_insert_park_term = db_query(" INSERT INTO park_terms(nid, term) VALUES(%d, %d)", $nid, $tid);
    $rs_insert_term_node = db_query(" INSERT INTO term_node(nid, vid, tid) VALUES(%d, %d, %d)", $nid, $vid, $tid);

  }
}


// Terminate if an error occured during user_save().
?>