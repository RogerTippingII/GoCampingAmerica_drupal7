<?php
/**
 * Created by IntelliJ IDEA.
 * User: patricepaquette
 * Date: 1/19/2014
 * Time: 12:50 AM
 */


chdir($_SERVER['DOCUMENT_ROOT']);
global $base_url;
$base_url = 'http://'.$_SERVER['HTTP_HOST'];
include_once './includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

require_once('./scripts/PHPExcel.php');


$outputReport = array();

$rs_cc_import_park = db_query("
    SELECT
        cc.name,
        cc.mail,
        cc.title,
        cc.field_camp_website_url,
        cc.field_camp_lagacy_id_value,
        cc.field_camp_email_email,
        cc.field_camp_tollfree_phone_number,
        cc.street,
        cc.additional,
        cc.city,
        cc.province,
        cc.postal_code,
        cc.latitude,
        cc.longitude,
        cc.field_camp_phone_number
    FROM cc_unmatched_parks_update cc"
);

$counter = 0;
while($row = db_fetch_array($rs_cc_import_park)){
  $rs_modify_user = db_query("
        UPDATE users
        JOIN node ON node.uid = users.uid
        SET
          users.mail = '%s'
        WHERE users.name = '%s'",
        $row["mail"],
        $row["name"]
  );

  if(!rs_modify_user){
    echo "user modification failed";
  }

  $rs_modify_node = db_query("
    UPDATE node
    JOIN users ON users.uid = node.uid
    SET node.title = '%s'
    WHERE users.name = '%s'",
    $row["title"],
    $row["name"]
  );

  if(!rs_modify_node){
    echo "could not modify node";
  }

  $rs_modify_camp_info = db_query("
        UPDATE content_type_camp c
        JOIN node ON node.vid = c.vid
        JOIN users ON users.uid = node.uid
        SET
          c.field_camp_website_url = '%s',
          c.field_camp_lagacy_id_value = %d,
          c.field_camp_email_email = '%s',
          c.field_park_official_optin_value = '%s',
          c.field_park_tier_value = %d,
          c.field_park_guest_reviews_optin_value = '%s',
          c.field_camp_tollfree_phone_number = '%s'
        WHERE users.name = '%s'",
        $row["field_camp_website_url"],
        $row["field_camp_lagacy_id_value"],
        $row["field_camp_email_email"],
        $row["field_park_official_optin_value"],
        $row["field_park_tier_value"],
        $row["field_park_guest_reviews_optin_value"],
        $row["field_camp_tollfree_phone_number"],
        $row["name"]
  );

  if(!$rs_modify_camp_info){
    echo "camp modification failed";
  }

  $rs_location_modification = db_query("
    UPDATE location l
    JOIN location_instance li ON li.lid = l.lid
    JOIN node ON node.vid = li.vid
    JOIN users ON users.uid = node.uid
    SET
      l.province = '%s',
      l.city = '%s',
      l.street = '%s',
      l.additional = '%s',
      l.postal_code = '%s',
      l.latitude = %d,
      l.longitude = %d
    WHERE users.name = '%s'",
    $row["province"],
    $row["city"],
    $row["street"],
    $row["additional"],
    $row["postal_code"],
    $row["latitude"],
    $row["longitude"],
    $row["name"]
  );

  if(!$rs_location_modification){
    echo "location modification failed\n";
  }

  $rs_phone_modification = db_query("
    UPDATE content_field_camp_phone cfcp
    JOIN node ON node.vid = cfcp.vid
    JOIN users ON users.uid = node.uid
    SET cfcp.field_camp_phone_number = '%s'
    WHERE users.name = '%s';",
    $row["field_camp_phone_number"],
    $row["name"]
  );

  if(!$rs_phone_modification){
    echo "phone modification failed\n";
  }

  $counter++;
}

echo "Modified " . $counter ." parks";

function array_to_csv_download($array, $filename = "export.csv", $delimiter=",") {
  // open raw memory as file so no temp files needed, you might run out of memory though
  $f = fopen('php://memory', 'w');


  $headers = array_keys($array[0]);

  fputcsv($f, $headers, $delimiter);

  // loop over the input array
  foreach ($array as $line) {
    // generate csv lines from the inner arrays
    $line = array_map(function($value){
      return $value;
    }, $line);
    fputcsv($f, $line, $delimiter);
  }
  // rewrind the "file" with the csv lines
  fseek($f, 0);
  // tell the browser it's going to be a csv file
  header('Content-Type: application/csv');
  // tell the browser we want to save it instead of displaying it
  header('Content-Disposition: attachement; filename="'.$filename.'"');
  // make php send the generated csv lines to the browser
  fpassthru($f);
}