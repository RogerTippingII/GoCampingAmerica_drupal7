<?php
/* ****************** */
/* WEEKLY DATA DUMP   */
/* Created 11/14/2012 */
/* Updated 11/14/2012 */
/* ****************** */

require_once('/var/www/vhosts/gocampingamerica.com/httpdocs/scripts/PHPExcel.php');

// DRUPAL BOOTSTRAP
chdir($_SERVER['DOCUMENT_ROOT']);
global $base_url;
$base_url = 'http://'.$_SERVER['HTTP_HOST'];
require_once './includes/bootstrap.inc';
require_once './includes/common.inc';
require_once './includes/module.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_DATABASE);
drupal_bootstrap(DRUPAL_BOOTSTRAP_SESSION);
drupal_load('module', 'node');
module_invoke('node', 'boot');

// EXECUTE

if (isset($_REQUEST["action"]) == "dump") {
  if (isset($_REQUEST["begin"]) && isset($_REQUEST["end"])) {
	//displayForm();
	doDataDump();
  }
} else {
  displayForm();
}

// FUNCTIONS

function doDataDump() {
  $begin = convertTime($_REQUEST["begin"]);
  $end = convertTime($_REQUEST["end"]);
  //echo "Begin: " . $_REQUEST["begin"] . " | End: " . $_REQUEST["end"] . "<br />";
  //echo "Begin: " . $begin . " | End: " . $end . "<br />";
  $parks = getParks($begin, $end);
  $x = 0;
  //echo "Changes: " . count($parks) . "<br />";
  $parksAdded = array();
  foreach ($parks as $park) {
    $parkInfo = getParkInfo($park);
    if (!in_array($parkInfo["nid"], $parksAdded)) {
	  $spreadsheet[$x] = extractFields($parkInfo);
	  $parksAdded[] = $parkInfo["nid"];
	  $x++;
    }
  }
  //echo "Parks Changed: " . $x . "<br />";
  //echo "<pre>";
  //print_r($spreadsheet);
  //echo "</pre>";
  exportSpreadsheet($spreadsheet, $begin, $end);
}

function exportSpreadsheet($info, $begin, $end) {
  //$headerNames = array("pkID", "Status", "Member Name", "Location Address 1", "Location Address 2", "Location City", "Location State", "Location ZIP", "Location Country", "Phone", "Fax", "Email Address", "Website", "Primary Contact Name", "Billing Address 1", "Billing Address 2", "Billing City", "Billing State", "Billing ZIP", "Billing Country", "Month Open", "Day Open", "Month Closed", "Day Closed", "Open Year-Round", "Sites Cabin", "Sites Electric_Water", "Sites Electrical", "Sites Full Hookups", "Sites No Hookups", "Sites Other", "Sites Park Model", "Sites Teepee", "Sites Tent", "Sites Total RV", "Sites Yurt", "Sites Total", "Sites Total Reported", "Company Association", "pkGuestReviewID", "State Association Name", "fkStateID");
  $headerNames = array("Changed By", "pkID", "Status", "Member Name", "Location Address 1", "Location Address 2", "Location City", "Location State", "Location ZIP", "Location Country", "Phone", "Fax", "Email Address", "Primary Contact Name", "Billing Address 1", "Billing Address 2", "Billing City", "Billing State", "Billing ZIP", "Billing Country");
  // Create new excel object
  $objPHPExcel = new PHPExcel();

  // Set metadata
  $objPHPExcel->getProperties()->setCreator("ARVC")
							 ->setLastModifiedBy("ARVC")
							 ->setTitle("ARVC")
							 ->setSubject("ARVC")
							 ->setDescription("ARVC")
							 ->setKeywords("ARVC")
							 ->setCategory("ARVC");
  $objPHPExcel->getDefaultStyle()->getFont()
    ->setName('Arial')
    ->setSize(10);
  $objPHPExcel->getActiveSheet()->setTitle('Data Dump');

  // Define active sheet
  $objPHPExcel->setActiveSheetIndex(0);
  $objPHPExcel->getActiveSheet()->getRowDimension(2)->setRowHeight(18);

  // Set column header names
  $cols = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z", "AA", "AB", "AC", "AD", "AE", "AF", "AG", "AH", "AI", "AJ", "AK", "AL", "AM", "AN", "AO", "AP");
  $x = 0;
  foreach ($headerNames as $name) {
    $cell = $cols[$x] . "1";
    $objPHPExcel->getActiveSheet()->SetCellValue($cell, $name);
    $x++;
  }
  $styleArray = array('font' => array('bold' => true));
  $objPHPExcel->getActiveSheet()->getStyle('A1:AP1')->applyFromArray($styleArray);
  
  $x = 2;
  foreach ($info as $datarow) {
    $y = 0;
	foreach ($datarow as $key => $value) {
      $cell = $cols[$y] . $x;
	  $objPHPExcel->getActiveSheet()->SetCellValue($cell, $value);
      $y++;
    }
	$x++;
  }
  
  $OutputFilename = "Data Dump (" . date("Y-m-d", $begin) . " - " . date("Y-m-d", $end) . ").xlsx";
  header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); 
  header("Content-Disposition: attachment;filename=\"" . $OutputFilename . "\"");
  header('Cache-Control: max-age=0'); 

  $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

  unset($projectData);
  unset($pmCased);
  unset($headerNames);

  $objWriter->save('php://output');
}

function getChangedBy($user) {
  $query = db_query("SELECT name FROM {users} WHERE uid = %d LIMIT 1", $user);
  while ($row = db_fetch_array($query)) {
    $result = $row["name"];
  }
  if ($result) {
    return $result;
  }
  return;
}

function extractFields($info) {
  $result["Changed By"] = getChangedBy($info["user"]);
  $result["pkID"] = $info["username"];
  $result["Status"] = $info["data"]->field_camp_status[0]["value"];
  $result["Member Name"] = $info["park_name"];
  $result["Location Address 1"] = $info["data"]->field_location[0]["street"];
  $result["Location Address 2"] = $info["data"]->field_location[0]["additional"];
  $result["Location City"] = $info["data"]->field_location[0]["city"];
  $result["Location State"] = $info["data"]->field_location[0]["province"];
  $result["Location ZIP"] = $info["data"]->field_location[0]["postal_code"];
  $result["Location Country"] = $info["data"]->field_location[0]["country"];
  $result["Phone"] = $info["data"]->field_camp_phone[0]["number"];
  $result["Fax"] = $info["data"]->field_camp_faxs[0]["number"];
  $result["Email Address"] = $info["data"]->field_camp_email[0]["email"];
  //$result["Website"] = $info["data"]->field_camp_website[0]["url"];
  $result["Primary Contact Name"] = $info["data"]->field_park_contact_name[0]["value"];
  $result["Billing Address 1"] = $info["data"]->field_park_billing_street[0]["value"];
  $result["Billing Address 2"] = $info["data"]->field_park_billing_street2[0]["value"];
  $result["Billing City"] = $info["data"]->field_park_billing_city[0]["value"];
  $result["Billing State"] = $info["data"]->field_park_billing_state[0]["value"];
  $result["Billing ZIP"] = $info["data"]->field_park_billing_zip[0]["value"];
  $result["Billing Country"] = $info["data"]->field_park_billing_country[0]["value"];
  /*
  $result["Month Open"] = $info["data"]->field_park_date_open[0]["value"];
  $result["Day Open"] = $info["data"]->field_park_date_open_day[0]["value"];
  $result["Month Closed"] = $info["data"]->field_park_date_closed_month[0]["value"];
  $result["Day Closed"] = $info["data"]->field_park_date_closed_day[0]["value"];
  $result["Open Year-Round"] = "";
  if ($info["data"]->field_camp_open_year_round[0]["value"] == "off") {
    $result["Open Year-Round"] = "No";
  } elseif ($info["data"]->field_camp_open_year_round[0]["value"] == "on") {
    $result["Open Year-Round"] = "Yes";
  }
  $result["Sites Cabin"] = $info["data"]->field_camp_rental_cabins[0]["value"];
  $result["Sites Electric_Water"] = $info["data"]->field_camp_electric_water[0]["value"];
  $result["Sites Electrical"] = $info["data"]->field_camp_electrical[0]["value"];
  $result["Sites Full Hookups"] = $info["data"]->field_camp_full_hookups[0]["value"];
  $result["Sites No Hookups"] = $info["data"]->field_camp_no_hookups[0]["value"];
  $result["Sites Other"] = $info["data"]->field_camp_other[0]["value"];
  $result["Sites Park Model"] = $info["data"]->field_camp_rental_trailers[0]["value"];
  $result["Sites Teepee"] = $info["data"]->field_camp_teepee[0]["value"];
  $result["Sites Tent"] = $info["data"]->field_camp_tents[0]["value"];
  $result["Sites Total RV"] = $info["data"]->field_camp_total_rv_calc[0]["value"];
  $result["Sites Yurt"] = $info["data"]->field_camp_yurts[0]["value"];
  $result["Sites Total"] = $info["data"]->field_camp_total_calc[0]["value"];
  $result["Sites Total Reported"] = $info["data"]->field_camp_totals[0]["value"];
  $result["Company Association"] = $info["data"]->field_camp_company_assoc[0]["value"];
  $result["pkGuestReviewID"] = $info["data"]->field_camp_guestreview_id[0]["value"];
  $result["State Association Name"] = $info["data"]->field_camp_state_assnname[0]["value"];
  $result["fkStateID"] = $info["data"]->field_camp_state_assnid[0]["value"];
  */
  return $result;
}

function getParkInfo($cid) {
  $query = db_query("SELECT nid, username, park_name, modified, data, user FROM {park_changes} WHERE cid = %s LIMIT 1", $cid);
  while ($row = db_fetch_array($query)) {
    $result["cid"] = $cid;
	$result["nid"] = $row["nid"];
	$result["username"] = $row["username"];
	$result["park_name"] = $row["park_name"];
	$result["modified"] = $row["modified"];
	$result["data"] = unserialize($row["data"]);
	$result["user"] = $row["user"];
  }
  if ($result) {
    return $result;
  }
  return;
}

function checkGCARole($user) {
  $query = db_query("SELECT rid FROM {users_roles} WHERE uid = %d AND (rid = 6 || rid = 4)", $user);
  while ($row = db_fetch_array($query)) {
    return 1;
  }
  return 0;
}

function getParks($begin, $end) {
  $query = db_query("SELECT cid, user FROM {park_changes} WHERE modified > %s AND modified < %s ORDER BY modified DESC", $begin, $end);
  while ($row = db_fetch_array($query)) {
    if (checkGCARole($row["user"]) != 1) {
	  $result[] = $row["cid"];
    }
  }
  if ($result) {
    return $result;
  }
  return;
}

function convertTime($string) {
  return strtotime("12:00am " . $string);
}

function displayForm() { ?>

Weekly Data Dump<br />
(Date format: mm/dd/yyyy)<br />
<form method="get">
  <input type="hidden" name="action" value="dump" />
  <table>
  <tr><td>Begin:</td><td><input type="text" name="begin" /></td></tr>
  <tr><td>End:</td><td><input type="text" name="end" /></td></tr>
  </table>
  <input type="submit" name="Submit" />
</form>

<?php }


?>