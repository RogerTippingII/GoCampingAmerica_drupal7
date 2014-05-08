<?php
// DATA DUMP IMPORT SCRIPT

// Bootstrap
chdir($_SERVER['DOCUMENT_ROOT']);
global $base_url;
$base_url = 'http://'.$_SERVER['HTTP_HOST'];
require_once './includes/bootstrap.inc';
require_once './includes/common.inc';
require_once './includes/module.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_DATABASE);
drupal_bootstrap(DRUPAL_BOOTSTRAP_SESSION);
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);
drupal_load('module', 'node');
drupal_load('module', 'cck');
module_invoke('node', 'boot');

// Get dump file
$filePath = "/var/www/vhosts/gocampingamerica.com/httpdocs/sites/default/files/imports/20120722_gca_data_dump.csv";
$park = parseDumpFile($filePath);
$masterRow = $park[2];
//showMasterRow($masterRow);


// Show Park Associations
//showAssociations($park);

// Cycle through parks and update db
$new = 1;
for ($i = 2915; $i < 3000; $i++) {
  $park[$i]["drupal_user"] = getParkUID($park[$i][53]);
  $park[$i]["drupal_nid"] = getParkNID($park[$i]["drupal_user"]);
  if ($park[$i]["drupal_user"] && $park[$i]["drupal_nid"]) {
    echo "R" . $i . " | PN" . $park[$i][53] . " | PUID" . $park[$i]["drupal_user"] . " | PNID" . $park[$i]["drupal_nid"] . " | " . $park[$i][44] . "<br />";
  } elseif ($park[$i][44] && $park[$i][53]) {
    echo "<b>New Park: " . $new . " | " . $i . " | " . $park[$i][44] . " | " . $park[$i][53] . "</b><br />";
	$new++;
  }
  
  if ($park[$i]["drupal_user"] && $park[$i]["drupal_nid"] && intval($park[$i]["drupal_user"])) {
    $parkNode = node_load($park[$i]["drupal_nid"]);
    //echo "<pre>";
    //print_r($park[$i]);
    //echo "</pre>";

    // Update title, park description, directions, park website, email, reservation website, local interest, rules - DONE
	updateTitle($park[$i], $parkNode);
	
    // Update quantities - DONE
	updateQuantities($park[$i], $parkNode);
	
    // Update tags - DONE
	updateTags($park[$i], $parkNode);
	
    // Update location (business location and park location) - DONE
	updateLocations($park[$i], $parkNode);
	
    // Update contacts - DONE
	updateContacts($park[$i], $parkNode);
	
	// Update open, close, year round - DONE
	updateOpen($park[$i], $parkNode);
	
	// Update GuestRated ID - DONE
	updateReview($park[$i], $parkNode);
	
	// Update Discounts
	
	// Update Associations
	updateAssociations($park[$i], $parkNode);
	
	// Update fax number, phone number - DONE
	updateNumbers($park[$i], $parkNode);
	
	// Update status
	updateStatus($park[$i], $parkNode);
	
	unset($parkNode);
  }
}

function showMasterRow($masterRow) {
  echo "<pre>";
  print_r($masterRow);
  echo "</pre>";
}

function showAssociations($park) {
  $result = array();
  foreach ($park as $indiv) {
    $tempData = explode("\n", $indiv[7]);
	$result = array_merge($tempData, $result);
	$result = array_unique($result);
  }
  sort($result);
  echo "<pre>";
  print_r($result);
  echo "</pre>";
}

function updateStatus($park, $parkNode) {
  // status = 83
  $y = 0;
  
  if ($parkNode->field_camp_status[0]["value"] != strtolower($park[83])) {
    $old = $parkNode->field_camp_status[0]["value"];
    $parkNode->field_camp_status[0]["value"] = strtolower($park[83]);
    $y++;
  }
  if ($y) {
    node_save($parkNode);
	echo "Dump: " . strtolower($park[83]) . "<br />";
    echo "Site: " . $old . "<br />";
	echo $park["drupal_nid"] . " status updated.<br />";
  }
  
}

function updateAssociations($park, $parkNode) {
  // state association id = 32
  $y = 0;
  echo "Dump: " . $park[32] . "<br />";
  echo "Site: " . $parkNode->field_camp_state_assnid[0]["value"] . "<br />";
  if ($parkNode->field_camp_state_assnid[0]["value"] != $park[32]) {
    $parkNode->field_camp_state_assnid[0]["value"] = $park[32];
	$parkNode->field_camp_state_assnid[0]["view"] = $park[32];
	$y++;
  }
  if ($y) {
    node_save($parkNode);
	echo $park["drupal_nid"] . " state association updated.<br />";
  }
}

function updateLocations($park, $parkNode) {
  // PARK LOCATION:
  // street address = 39
  // street address line 2 = 40
  // city = 41
  // "province" (abbreviated) = 42
  // zip code = 43
  // country (abbreviated) = 18 (need to massage this)
  // latitude = 35
  // longitude = 36
  // locpick["user_latitude"] = 35
  // locpick["user_longitude"] = 36
  // province_name (full) (not in dump)
  // country_name (full) (not in dump)
  // BILLING ADDRESS:
  // street address = 0
  // street address line 2 = 1
  // city = 2
  // "province" (abbreviated) = 3
  // zip code = 4
  // country (abbreviated) = 18
  // province_name (full) (not in dump)
  // country_name (full) (not in dump)
  
  $countryArray = array("USA", "U.S.A.", "usa", "u.s.a", "United States", "US", "us", "United States of America", "united states", "united states of america", "U.S.", "u.s.");
  if (in_array(trim($park[18]), $countryArray)) {
    $park["country_short"] = "us";
	$park["country_full"] = "United States";
  }
  
  $y = 0;
  
  // Update park location
  if ($parkNode->field_location[0]["street"] != $park[39]) {
    if (substr($park[39], 0, 4) != "PO B") {
	  $parkNode->field_location[0]["street"] = $park[39];
      $y++;
	}
  }
  
  if ($parkNode->field_location[0]["additional"] != $park[40]) {
    $parkNode->field_location[0]["additional"] = $park[40];
    $y++;
  }
  
  if ($parkNode->field_location[0]["city"] != $park[41]) {
    $parkNode->field_location[0]["city"] = $park[41];
    $y++;
  }
  
  if ($parkNode->field_location[0]["province"] != $park[42]) {
    $parkNode->field_location[0]["province"] = $park[42];
    $y++;
  }
  
  if ($parkNode->field_location[0]["postal_code"] != $park[43]) {
    $parkNode->field_location[0]["postal_code"] = $park[43];
    $y++;
  }
  
  if ($parkNode->field_location[0]["country"] != $park["country_short"]) {
    if ($park["country_short"] != "") {
      $parkNode->field_location[0]["country"] = $park["country_short"];
      $y++;
	}
  }
  
  if ($parkNode->field_location[0]["latitude"] != $park[35]) {
    $parkNode->field_location[0]["latitude"] = $park[35];
	$parkNode->field_location[0]["locpick"]["user_latitude"] = $park[35];
    $y++;
  }
  
  if ($parkNode->field_location[0]["longitude"] != $park[36]) {
    $parkNode->field_location[0]["longitude"] = $park[36];
	$parkNode->field_location[0]["locpick"]["user_longitude"] = $park[36];
    $y++;
  }
  
  $state_list = array('AL'=>"Alabama", 'AK'=>"Alaska", 'AZ'=>"Arizona", 'AR'=>"Arkansas", 'CA'=>"California", 'CO'=>"Colorado", 'CT'=>"Connecticut", 'DE'=>"Delaware", 'DC'=>"District Of Columbia", 'FL'=>"Florida", 'GA'=>"Georgia", 'HI'=>"Hawaii", 'ID'=>"Idaho", 'IL'=>"Illinois", 'IN'=>"Indiana", 'IA'=>"Iowa", 'KS'=>"Kansas", 'KY'=>"Kentucky", 'LA'=>"Louisiana", 'ME'=>"Maine", 'MD'=>"Maryland", 'MA'=>"Massachusetts", 'MI'=>"Michigan", 'MN'=>"Minnesota", 'MS'=>"Mississippi", 'MO'=>"Missouri", 'MT'=>"Montana", 'NE'=>"Nebraska", 'NV'=>"Nevada", 'NH'=>"New Hampshire", 'NJ'=>"New Jersey", 'NM'=>"New Mexico", 'NY'=>"New York", 'NC'=>"North Carolina", 'ND'=>"North Dakota", 'OH'=>"Ohio", 'OK'=>"Oklahoma", 'OR'=>"Oregon", 'PA'=>"Pennsylvania", 'RI'=>"Rhode Island", 'SC'=>"South Carolina", 'SD'=>"South Dakota", 'TN'=>"Tennessee", 'TX'=>"Texas", 'UT'=>"Utah", 'VT'=>"Vermont", 'VA'=>"Virginia", 'WA'=>"Washington", 'WV'=>"West Virginia", 'WI'=>"Wisconsin", 'WY'=>"Wyoming", 'AB'=>"Alberta", 'BC'=>"British Columbia", 'MB'=>"Manitoba", 'NB'=>"New Brunswick", 'NL'=>"Newfoundland", 'NT'=>"Northwest Territories", 'NS'=>"Nova Scotia", 'NU'=>"Nunavut", 'ON'=>"Ontario", 'PE'=>"Prince Edward Island", 'QC'=>"Quebec", 'SK'=>"Saskatchewan", 'YT'=>"Yukon");
  
  if ($parkNode->field_location[0]["province_name"] != $state_list[$park[42]]) {
    echo "Old: " . $parkNode->field_location[0]["province_name"] . "<br />";
	echo "New: " . $state_list[$park[42]] . "<br />";
    $parkNode->field_location[0]["province_name"] = $state_list[$park[42]];
    $y++;
  }
  
  if ($parkNode->field_location[0]["country_name"] != $park["country_full"]) {
    if ($park["country_full"] != "") {
      $parkNode->field_location[0]["country_name"] = $park["country_full"];
      $y++;
	}
  }
  
  // Update billing address
  if ($parkNode->field_camp_business_address[0]["street"] != $park[0]) {
    $parkNode->field_camp_business_address[0]["street"] = $park[0];
    $y++;
  }
  
  if ($parkNode->field_camp_business_address[0]["additional"] != $park[1]) {
    $parkNode->field_camp_business_address[0]["additional"] = $park[1];
    $y++;
  }
  
  if ($parkNode->field_camp_business_address[0]["city"] != $park[2]) {
    $parkNode->field_camp_business_address[0]["city"] = $park[2];
    $y++;
  }
  
  if ($parkNode->field_camp_business_address[0]["province"] != $park[3]) {
    $parkNode->field_camp_business_address[0]["province"] = $park[3];
    $y++;
  }
  
  if ($parkNode->field_camp_business_address[0]["postal_code"] != $park[4]) {
    $parkNode->field_camp_business_address[0]["postal_code"] = $park[4];
    $y++;
  }
  
  if ($parkNode->field_camp_business_address[0]["country"] != $park["country_short"]) {
    if ($park["country_short"] != "") {
      $parkNode->field_camp_business_address[0]["country"] = $park["country_short"];
      $y++;
	}
  }
  
  if ($parkNode->field_camp_business_address[0]["province_name"] != $state_list[$park[3]]) {
    $parkNode->field_camp_business_address[0]["province_name"] = $state_list[$park[3]];
    $y++;
  }
  
  if ($parkNode->field_camp_business_address[0]["country_name"] != $park["country_full"]) {
    if ($park["country_full"]) {
      $parkNode->field_camp_business_address[0]["country_full"] = $park["country_full"];
      $y++;
    }
  }
  
  if ($y) {
    node_save($parkNode);
	echo $park["drupal_nid"] . " addresses updated.<br />";
  }
  
}

function updateNumbers($park, $parkNode) {
  // fax = 30
  // phone = 51
  
  $y = 0;
  
  // Update fax
  if ($parkNode->field_camp_fax[0]["number"] != preg_replace('/\D/', '', $park[30])) {
	$parkNode->field_camp_fax[0]["number"] = preg_replace('/\D/', '', $park[30]);
    $y++;
  }
  
  // Update phone
  if ($parkNode->field_camp_phone[0]["number"] != preg_replace('/\D/', '', $park[51])) {
	$parkNode->field_camp_phone[0]["number"] = preg_replace('/\D/', '', $park[51]);
    $y++;
  }
  
  if ($y) {
    node_save($parkNode);
	echo $park["drupal_nid"] . " fax/phone updated.<br />";
  }
  
  
}

function updateReview($park, $parkNode) {
  // guest rated id = 52
  
  $y = 0;
  
  if ($parkNode->field_camp_guestreview_id[0]["value"] != $park[52]) {
    $parkNode->field_camp_guestreview_id[0]["value"] = $park[52];
	$parkNode->field_camp_guestreview_id[0]["safe"] = $park[52];
    $y++;
  }
  
  if ($y) {
    node_save($parkNode);
    echo $park["drupal_nid"] . " review ID updated.<br />";
  }
}

function updateContacts($park, $parkNode) {
  // Park primary contact = 31
  // State assoc = 32
  
  $y = 0;
  
  // Update primary contact
  if ($parkNode->field_camp_primary_contactid[0]["value"] != $park[31]) {
    $parkNode->field_camp_primary_contactid[0]["value"] = $park[31];
	$y++;
  }
  
  // Update state association
  if ($parkNode->field_camp_state_assnid[0]["value"] != $park[32]) {
    $parkNode->field_camp_state_assnid[0]["value"] = $park[32];
	$y++;
  }
  
  if ($y) {
    node_save($parkNode);
    echo $park["drupal_nid"] . " contacts updated.<br />";
  }
}

function updateOpen($park, $parkNode) {
  // Operational dates = 48 (for "Open Year Round" or "Open All Year")
  // Opens = 23
  // Closes = 21
  $yearRound = array("open year round", "open all year", "all year", "year round", "open year round.", "year around", "open year around");
  $y = 0;
  if (in_array(strtolower($park[48]), $yearRound) && $parkNode->field_camp_open_year_round[0]["value"] != "on") {
    $parkNode->field_camp_open_year_round[0]["value"] = "on";
	$parkNode->field_camp_open_year_round[0]["safe"] = "on";
	$y++;
  } else {
    if ($park[23]) {
	  if ($parkNode->field_park_date_open[0]["value"] != date("F", strtotime($park[23]))) {
	    $parkNode->field_park_date_open[0]["value"] = date("F", strtotime($park[23]));
	    $parkNode->field_park_date_open[0]["safe"] = date("F", strtotime($park[23]));
		$y++;
	  }
	  if ($parkNode->field_park_date_open_day[0]["value"] != date("j", strtotime($park[23]))) {
	    $parkNode->field_park_date_open_day[0]["value"] = date("j", strtotime($park[23]));
	    $parkNode->field_park_date_open_day[0]["safe"] = date("j", strtotime($park[23]));
	    $y++;
	  }
	}
	if ($park[21]) {
	  if ($parkNode->field_park_date_closed_month[0]["value"] != date("F", strtotime($park[21]))) {
	    $parkNode->field_park_date_closed_month[0]["value"] = date("F", strtotime($park[21]));
	    $parkNode->field_park_date_closed_month[0]["safe"] = date("F", strtotime($park[21]));
		$y++;
	  }
	  if ($parkNode->field_park_date_closed_day[0]["value"] != date("j", strtotime($park[21]))) {
	    $parkNode->field_park_date_closed_day[0]["value"] = date("j", strtotime($park[21]));
	    $parkNode->field_park_date_closed_day[0]["safe"] = date("j", strtotime($park[21]));
		$y++;
	  }
	}
  }
  if ($y) {
    node_save($parkNode);
    echo $park["drupal_nid"] . " open/close dates changed.<br />";
  }
}

function updateQuantities($park, $parkNode) {
  // Total Sites Reported = 79
  // Electric & Water = 62
  // Only Electrical = 63
  // Full Hookups = 64
  // No Hookups = 68
  // Total Cabin Sites = 61
  // Total Park Model Sites = 70
  // Total Teepee Sites = 76
  // Total Yurt Sites = 81
  // Total Other Sites = 69
  // Total Tent Sites = 77
  // Max RV Length = 67
  
  $y = 0;
  if ($parkNode->field_camp_totals[0]["value"] != $park[79]) {
    $parkNode->field_camp_totals[0]["value"] = $park[79];
	$y++;
  }
  if ($parkNode->field_camp_electric_water[0]["value"] != $park[62]) {
    $parkNode->field_camp_electric_water[0]["value"] = $park[62];
    $y++;
  }
  if ($parkNode->field_camp_electrical[0]["value"] != $park[63]) {
    $parkNode->field_camp_electrical[0]["value"] = $park[63];
    $y++;
  }
  if ($parkNode->field_camp_full_hookups[0]["value"] != $park[64]) {
    $parkNode->field_camp_full_hookups[0]["value"] = $park[64];
    $y++;
  }
  if ($parkNode->field_camp_no_hookups[0]["value"] != $park[68]) {
    $parkNode->field_camp_no_hookups[0]["value"] = $park[68];
    $y++;
  }
  if ($parkNode->field_camp_rental_cabins[0]["value"] != $park[61]) {
    $parkNode->field_camp_rental_cabins[0]["value"] = $park[61];
    $y++;
  }
  if ($parkNode->field_camp_teepee[0]["value"] != $park[76]) {
    $parkNode->field_camp_teepee[0]["value"] = $park[76];
    $y++;
  }
  if ($parkNode->field_camp_yurts[0]["value"] != $park[81]) {
    $parkNode->field_camp_yurts[0]["value"] = $park[81];
    $y++;
  }
  if ($parkNode->field_camp_other[0]["value"] != $park[69]) {
    $parkNode->field_camp_other[0]["value"] = $park[69];
    $y++;
  }
  if ($parkNode->field_camp_rv_length[0]["value"] != $park[67]) {
    $parkNode->field_camp_rv_length[0]["value"] = $park[67];
    $y++;
  }
  
  if ($y) {
    node_save($parkNode);
	echo $park["drupal_nid"] . " site quantities updated.<br />";
  }
  
}

function updateTags($park, $parkNode) {
  // Terms are in data dump rows 7-15
  $x = 0;
  for ($i = 7; $i < 16; $i++) {
    if ($park[$i]) {
      $tempTerms = parseTermRows($park[$i]);
	}
	foreach ($tempTerms as $term) {
	  $tempResult[] = $term;
	}
	$x++;
  }
  $dumpArray = array_unique($tempResult);
  
  // Cycle through dump terms and attempt to find matching term in Drupal database
  $y = 0;
  foreach ($dumpArray as $dumpTerm) {
    if ($dumpTerm) {
      $termID = findTID($dumpTerm);
	  if ($termID) {
	    if (!$parkNode->taxonomy[$termID]) {
		  echo "Term " . $dumpTerm . " (" . $termID . ") not associated with park " . $park["drupal_nid"] . "<br />";
		  $parkNode->taxonomy[$termID]["tid"] = $termID;
		  $parkNode->taxonomy[$termID]["vid"] = getVID($termID);
		  $parkNode->taxonomy[$termID]["name"] = $dumpTerm;
		  $parkNode->taxonomy[$termID]["description"] = "";
		  $parkNode->taxonomy[$termID]["weight"] = 0;
		  $y++;
		}
	  }
	}
  }  
  
  if ($y) {
    node_save($parkNode);
    echo $park["drupal_nid"] . " terms updated.<br />";
  }
  
}

function getVID($tid) {
  $query = db_query("SELECT vid FROM {term_data} WHERE tid = %d", $tid);
  while ($row = db_fetch_array($query)) {
    $result = $row["vid"];
  }
  return $result;
}

function findTID($term) {
  $query = db_query("SELECT tid FROM {term_data} WHERE name = '%s' LIMIT 1", trim($term));
  while ($row = db_fetch_array($query)) {
    $result = $row["tid"];
  }
  return $result;
}

function parseTermRows($data) {
  $tempResult = explode("\n", $data);
  foreach ($tempResult as $temp) {
    if ($temp) {
	  $result[] = $temp;
	}
  }
  return $result;
}

function updateTitle($park, $parkNode) {
  // title = row 44
  // park description = 25
  // directions = 26
  // park website = 86
  // email = 28
  // reservations website = 87
  // local interest = 38
  
  $y = 0;
  
  // Update Title
  if ($parkNode->title != $park[44]) {
    //showDiff($park[44], $parkNode->title, $park["drupal_nid"], "Titles don't match");
	$parkNode->title = $park[44];
    $y++;
  }
  
  // Update Park Description (remember: site doesn't use the body field)
  
  if (str_replace("  ", " ", strip_tags($parkNode->field_park_description[0]["value"])) != trim(str_replace("  ", " ", strip_tags($park[25])))) {
	$parkNode->field_park_description[0]["value"] = $park[25];
	$parkNode->field_park_description[0]["safe"] = $park[25];
	$parkNode->field_park_description[0]["view"] = $park[25];
    $y++;
  }
  
  // Update Park Website
  if ($parkNode->field_camp_website[0]["url"] != $park[86]) {
	$parkNode->field_camp_website[0]["url"] = $park[86];
	$parkNode->field_camp_website[0]["display_url"] = $park[86];
	$parkNode->field_camp_website[0]["display_title"] = $park[86];
    $y++;
  }
  
  // Update Directions
  if ($parkNode->field_camp_directions[0]["value"] != $park[26]) {
    $parkNode->field_camp_directions[0]["value"] = $park[26];
	$parkNode->field_camp_directions[0]["safe"] = $park[26];
	$parkNode->field_camp_directions[0]["view"] = $park[26];
    $y++;
  }
  
  // Update Email address
  if ($parkNode->field_camp_email[0]["email"] != $park[28]) {
    $parkNode->field_camp_directions[0]["email"] = $park[28];
	$parkNode->field_camp_directions[0]["safe"] = $park[28];
	$parkNode->field_camp_directions[0]["view"] = $park[28];
    $y++;
  }
  
  // Update Reservations Website
  if ($parkNode->field_camp_reservation_website[0]["url"] != $park[87]) {
    $parkNode->field_camp_reservation_website[0]["url"] = $park[87];
	$parkNode->field_camp_reservation_website[0]["title"] = $park[87];
	$parkNode->field_camp_reservation_website[0]["view"] = $park[87];
    $y++;
  }
  
  // Update Local Interest
  if (str_replace("  ", " ", strip_tags($parkNode->field_camp_local_interest[0]["value"])) != trim(str_replace("  ", " ", strip_tags($park[38])))) {
	$parkNode->field_camp_local_interest[0]["value"] = $park[38];
	$parkNode->field_camp_local_interest[0]["safe"] = $park[38];
	$parkNode->field_camp_local_interest[0]["view"] = $park[38];
	$y++;
  }
  
  if ($y) {
    node_save($parkNode);
    echo $park["drupal_nid"] . " title and other fields updated.<br />";
  }
  
}

function showDiff($dump, $drupalData, $nid, $description) {
  echo $description . ":<br />";
  echo "Dump: " . $dump . "<br />";
  echo "Site: " . $drupalData . "<br />";
  echo "Nid: " . $nid . "<br />";
}

function getParkNID($uid) {
  $query = db_query("SELECT nid FROM {node} WHERE uid = %d LIMIT 1", $uid);
  while ($row = db_fetch_array($query)) {
    $result = $row["nid"];
  }
  return $result;
}

function getParkUID($name) {
  $query = db_query("SELECT uid FROM {users} WHERE name = '%s' LIMIT 1", $name);
  while ($row = db_fetch_array($query)) {
    $result = $row["uid"];
  }
  return $result;
}

function parseDumpFile($filePath) {
  // Convert file data to an array ($csvData)
  $row = 1;
  if (($handle = fopen($filePath, "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
      $num = count($data);
      $row++;
      for ($c=0; $c < $num; $c++) {
        $csvData[$row][] = $data[$c];
      }
    }
  }
  fclose($handle);
  return $csvData;
}
?>