<?php
chdir($_SERVER['DOCUMENT_ROOT']);
global $base_url;
$base_url = 'http://'.$_SERVER['HTTP_HOST'];
include_once './includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

include_once __DIR__ . '/../../sites/all/gca-search/lib/Util.php';

$rs = db_query("
  SELECT *
  FROM import_parks_10_1_13 i
      LEFT JOIN users ON users.name = i.Profile_ID
      LEFT JOIN node ON node.uid = users.uid");

$counter = 0;

while($row = db_fetch_array($rs)){
  $user = false;
  $node = false;

  $user = user_load(array("name" => $row["Profile_ID"]));

  $geo_data = Util::GetGeoMSDN($row["Address1"] .", ". $row["City"] .", ". $row["State"] .", ". $row["Postal_Code"]);

  if(!$user){
    $user = user_save(NULL, array(
      "name" => $row["Profile_ID"],
      "pass" => "arvc",
      "init" => $row["Email"],
      "status" => 1,
      "mail" => $row["Email"]
    ));

    if($user){
      echo "Inserted new user\n";
    }
  }

  $camp_fields = array( "vid", "nid", "field_camp_website_url", "field_camp_status_value", "field_camp_lagacy_id_value",
    "field_camp_email_email", "field_camp_stateassn_user_uid",
    "field_park_official_optin_value", "field_park_tier_value", "field_park_guest_reviews_optin_value",
    "field_camp_tollfree_phone_number");
  $location_fields = array( "street", "additional", "city", "province", "postal_code", "country", "latitude", "longitude");

  $campEmail = $row["Email"];

  if($row["nid"] == NULL){ //DOES NOT EXIST
    $rs_vid = db_query("SELECT AUTO_INCREMENT FROM information_schema.tables WHERE table_name = 'node_revisions' AND table_schema = DATABASE()");
    $vid = db_result($rs_vid);

    echo $vid ."\n";

    $rs_insert_node = db_query("
      INSERT INTO node(vid, title, type, uid, comment)
      VALUES(%d, '%s', 'camp', %d, 2)",
      $vid, $row["Profile_Name"], $user->uid);

    $nid = db_last_insert_id("node", "nid");
    $rs_insert_node_revision = db_query("INSERT INTO node_revisions(nid, uid, title) VALUES(%d, %d, '%s')", $nid, $user->uid, $row["Profile_Name"]);

    //insert new camp info
    $query = "INSERT INTO content_type_camp(". implode(',', $camp_fields) .")
                              VALUES(%d, %d ,'%s', 'active', %d, '%s', %d, 'no', 1, 'no', '%s')";

    $rs_insert_camp = db_query($query ,
      $vid, $nid, $row["Website"], $row["Profile_ID"], $campEmail, $user->uid,
      $row["Tol_Free_Phone"]);



    $rs_insert_location = db_query("
      INSERT INTO location(". implode(',', $location_fields) .")
      VALUES('%s', '%s', '%s', '%s', '%s', '%s', %f, %f)",
      $row["Address1"], "", $row["City"],
      $row["State"], $row["Postal_Code"], "us",
      $geo_data[0], $geo_data[1]);

    $rs_insert_park_coordinates = db_query("INSERT INTO park_coordinates( nid,
                                                                          lat,
                                                                          lng)
                                            VALUES(%d, %f, %f)",
      $nid, $geo_data[0], $geo_data[1]);

    $lid = db_last_insert_id("location", "lid");

    $rs_insert_location_phone = db_query("INSERT INTO content_field_camp_phone( vid,
                                                                                nid,
                                                                                field_camp_phone_number
                                                                              )
                                        VALUES(%d, %d, %d)",
      $vid, $nid, $row["Work_Phone"]);

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
                                    field_camp_status_value = '%s',
                                    field_camp_lagacy_id_value = %d,
                                    field_camp_email_email = '%s',
                                    field_camp_tollfree_phone_number = '%s'
                                WHERE nid = %d AND vid = %d",
      $row["Website"], 'active', $row["Profile_ID"], $campEmail, $row["Toll_Free_Phone"], $nid, $vid
    );

    $rs_update_location = db_query("UPDATE location
                                    JOIN location_instance li ON li.lid = location.lid
                                    SET street = '%s',
                                        additional = '%s',
                                        city = '%s',
                                        province = '%s',
                                        postal_code = '%s',
                                        country = '%s',
                                        latitude = %f,
                                        longitude = %f
                                    WHERE li.nid = %d AND li.vid = %d",
      $row["Address1"], "", $row["City"],
      $row["State"], $row["Postal_Code"], "us",
      $geo_data[0], $geo_data[1], $nid, $vid
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
                                              SET lat = %f,
                                                  lng = %f,
                                              WHERE nid = %d",
        $geo_data[0], $geo_data[1], $nid);
    }
    else{
      $rs_insert_park_coordinates = db_query("INSERT INTO park_coordinates( nid,
                                                                          lat,
                                                                          lng)
                                            VALUES(%d, %f, %f)",
        $nid, $geo_data[0], $geo_data[1]);
    }

    $rs_update_phone = db_query(" UPDATE content_field_camp_phone
                                  SET field_camp_phone_number = '%s'
                                  WHERE nid = %d AND vid = %d",
      $row["Work_Phone"], $nid, $vid
    );
  }

}
  /*
  $node = node_load(array(
    "uid" => $user->uid,
    "type" => "camp"
  ));

  if(!node){
    $node = (object) NULL;
    $node->type = "camp";
  }

  if($row["Profile_Name"] != ""){
    $node->title = $row["Profile_Name"];
  }
  f($row["Website"] != ""){
    $node->field_camp_website[0]->url = $row["Website"];
  }
  if($row["Profile_ID"] != ""){
    $node->field_camp_lagacy_id[0]->value = $row["Profile_ID"];
  }
  if($row["Email"] != ""){
    $node->field_camp_email[0]->email = $row["Email"];
  }
  if($row["Toll_Free_Phone"] != ""){
    $node->field_camp_tollfree_phone[0]->number = $row["Toll_Free_Phone"];
  }
  if($row["Address1"] != ""){
    $node->field_location[0]->street = $row["Address1"];
  }
  if($row["City"] != ""){
    $node->field_location[0]->city = $row["City"];
  }
  if($row["State"] != ""){
    $node->field_location[0]->province = $row["State"];
  }
  if($row["Postal_Code"] != ""){
    $node->field_location[0]->postal_code = $row["Postal_Code"];
  }
  if($row["Fax"] != ""){
    $node->field_location[0]->fax = $row["Fax"];
  }
  if($row["Work_Phone"] != ""){
    $node->field_location[0]->phone = $row["Work_Phone"];
  }
  if($row["Work_Phone"] != ""){
    $node->field_camp_phone[0]->number = $row["Work_Phone"];
  }

  node_save($node);
  */
// Terminate if an error occured during user_save().
?>