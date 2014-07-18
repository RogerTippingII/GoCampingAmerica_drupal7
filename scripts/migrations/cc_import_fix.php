<?php
chdir($_SERVER['DOCUMENT_ROOT']);
global $base_url;
$base_url = 'http://'.$_SERVER['HTTP_HOST'];
include_once './includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);


if(user_access("administer nodes")){
  //delete all parks and users which were create as a result of an incorrect match
  $rs_duplicate_parks = db_query("
    SELECT node.nid, node.uid
    FROM cc_new_parks cc
      JOIN node ON node.title = cc.park_name;
    "
  );

  if($rs_duplicate_parks){
    while($row = db_fetch_array($rs_duplicate_parks)){
      node_delete($row["nid"]);

      if($row["uid"] != 0){
        user_delete(array(), $row["uid"]);
      }
    }

    //DELETE duplicate Holiday RV Park, Green Acres RV Park, and Skyline Ranch RV Park & Campground
    node_delete(91492);
    node_delete(94450);
  }

  //FIX fields for incorrect matches
  $rs_fix_incorrect_matches = db_query("
    SELECT *
    FROM cc_park_name_incorrect_matches
    "
  );

  if($rs_fix_incorrect_matches){
    while($row = db_fetch_array($rs_fix_incorrect_matches)){
      $rs_modify_user = db_query("
        UPDATE users
        JOIN node ON node.uid = users.uid
        SET
          users.name = '%s',
          users.mail = '%s',
          users.init = '%s'
        WHERE node.title = '%s'",
        $row["tentative_name"],
        $row["tentative_email"],
        $row["tentative_init_email"],
        $row["title"]
      );

      if(!rs_modify_user){
        echo "user modification failed";
      }

      $rs_modify_camp_info = db_query("
        UPDATE content_type_camp c
        JOIN node ON node.vid = c.vid
        SET
          c.field_camp_website_url = '%s',
          c.field_camp_lagacy_id_value = %d,
          c.field_camp_email_email = '%s',
          c.field_park_official_optin_value = '%s',
          c.field_park_tier_value = %d,
          c.field_park_guest_reviews_optin_value = '%s',
          c.field_camp_tollfree_phone_number = '%s'
        WHERE node.title = '%s'",
        $row["tentative_website"],
        $row["tentative_ARVCID"],
        $row["tentative_park_email"],
        $row["tentative_park_official_optin"],
        $row["tentative_park_tier"],
        $row["tentative_guest_review_optin"],
        $row["tentative_tollfree_phone_number"],
        $row["title"]
      );

      if(!$rs_modify_camp_info){
        echo "camp modification failed";
      }

      $rs_location_modification = db_query("
        UPDATE location l
        JOIN location_instance li ON li.lid = l.lid
        JOIN node ON node.vid = li.vid
        SET
          l.province = '%s',
          l.city = '%s',
          l.street = '%s',
          l.additional = '%s',
          l.postal_code = '%s',
          l.latitude = %d,
          l.longitude = %d
        WHERE node.title = '%s'",
        $row["tentative_state_province"],
        $row["tentative_city"],
        $row["tentative_address1"],
        $row["tentative_address2"],
        $row["tentative_postal_code"],
        $row["tentative_latitude"],
        $row["tentative_longitude"],
        $row["title"]
      );

      if(!$rs_location_modification){
        echo "location modification failed\n";
      }

      $rs_phone_modification = db_query("
        UPDATE content_field_camp_phone cfcp
        JOIN node ON node.vid = cfcp.vid
        SET cfcp.field_camp_phone_number = '%s'
        WHERE node.title = '%s';",
        $row["tentative_phone_number"],
        $row["title"]
      );

      if(!$rs_phone_modification){
        echo "phone modification failed\n";
      }
    }
  }

  //FIX arvc id for good matches
  $rs_fix_arvc_id = db_query("
    SELECT *
    FROM cc_park_name_matches_arvcid_fix cc
    "
  );

  if($rs_fix_arvc_id){
    $leftToDo = array();

    while($row = db_fetch_array($rs_fix_arvc_id)){
      if($row["tentative_username"]){
        $rs_modify_username = fix_username($row);

        if(!$rs_modify_username){
          $leftToDo[] = $row;
        }
        else{
          fix_arvcid($row);
        }
      }
    }

    foreach($leftToDo as $key => $row){
      $rs_modify_arvcid = fix_username($row);

      if($rs_modify_arvcid){
        fix_arvcid($row);
        unset($leftToDo[$key]);
      }
    }

    var_dump($leftToDo);
  }

  //FIX SIDE EFFECTS TO DESCRIPTIONS
  $rs_sideeffects_desc = db_query("
    SELECT *
    FROM cc_park_sideeffects_desc_fix"
  );

  if($rs_sideeffects_desc){
    while($row = db_fetch_array($rs_sideeffects_desc)){
      $rs_modify_desc = db_query("
        UPDATE content_type_camp c
        JOIN node ON node.vid = c.vid
        SET c.field_park_description_value = '%s'
        WHERE node.title = '%s'",
        $row["tentative_description"],
        $row["park_name"]
      );

      if(!$rs_modify_desc){
        echo "description modification failed\n";
      }
    }
  }
}





//functions
function fix_arvcid($row){
  return db_query("UPDATE content_type_camp c
        JOIN node ON node.vid = c.vid
        SET c.field_camp_lagacy_id_value = '%s'
        WHERE node.title = '%s';",
    $row["tentative_username"], $row["park_name"]
  );
}

function fix_username($row){
  return db_query("
        UPDATE users
        JOIN node ON node.uid = users.uid
        SET users.name = '%s'
        WHERE node.title = '%s';",
    $row["tentative_username"], $row["park_name"]
  );
}


