<?php
/* ****************** */
/* SEARCH REPORTS     */
/* Created 08/26/2014 */
/* ****************** */


// DRUPAL BOOTSTRAP
chdir($_SERVER['DOCUMENT_ROOT']);
define('DRUPAL_ROOT', __DIR__ . "/..");
global $base_url;
$base_url = 'http://' . $_SERVER['HTTP_HOST'];
require_once './includes/bootstrap.inc';
require_once './includes/common.inc';
require_once './includes/module.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);
drupal_load('module', 'node');
module_invoke('node', 'boot');

require_once('scripts/PHPExcel.php');


ini_set('display_startup_errors',1);
ini_set('display_errors',1);
error_reporting(-1);
ini_set('memory_limit','999M');

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
  //$searches = getSearches($begin, $end);
  // Loop through. Check for changes between current and previous (only
  // for specified fields). If a change (any change) is detected, add
  // the instance to the semifinal array for future analysis.
  
  $affiliation = taxonomy_get_tree(1);
  $amenities = taxonomy_get_tree(2);
  $credit_card = taxonomy_get_tree(3);
  $recreation = taxonomy_get_tree(5);
  $life = taxonomy_get_tree(6);
  $services = taxonomy_get_tree(17);
  $site_options = taxonomy_get_tree(18);
  
  $x = 0;
  $hit_list = array();
  
  // get all non-advanced searches
  $semiResult[$x]['space'] = "----";
  $x++;
  
  $semiResult[$x]['Title'] = "** NON ADVANCED SEARCHES **";
  $x++;
  
  $aux_array = getFilterCount($begin, $end, "", "count");
  
  $semiResult[$x]['Name'] = $aux_array['name'];
  $semiResult[$x]['Hits'] = $aux_array['hits'];
  
  // adding affiliations to table
  $x++;
  $semiResult[$x][''] = "";
  $semiResult[$x]['Title'] = "** ADVANCED SEARCHES **";
  $x++;
  $semiResult[$x]['space'] = "----";
  $semiResult[$x]['Title'] = "AFFILIATION";
  $x++;
  foreach ($affiliation as $value) {
    $aux_array = getFilterCount($begin, $end, $value->tid, $value->name);
    $hit_list[str_pad($aux_array['hits'], 4, "0", STR_PAD_LEFT)."_".$aux_array['name']] = array(
      'name' => $aux_array['name'],
      'hits' => $aux_array['hits']
    );
  }
  krsort($hit_list);
  foreach ($hit_list as $hit) {
    $semiResult[$x]['Name'] = $hit['name'];
    $semiResult[$x]['Hits'] = $hit['hits'];
    $x++;
  }

  // adding amenities to table
  $x++;
  $hit_list = array();
  $semiResult[$x]['space'] = "----";
  $semiResult[$x]['Title'] = "AMENITIES";
  $x++;
  foreach ($amenities as $value) {
    $aux_array = getFilterCount($begin, $end, $value->tid, $value->name);
    $hit_list[str_pad($aux_array['hits'], 4, "0", STR_PAD_LEFT)."_".$aux_array['name']] = array(
      'name' => $aux_array['name'],
      'hits' => $aux_array['hits']
    );
  }
  krsort($hit_list);
  foreach ($hit_list as $hit) {
    $semiResult[$x]['Name'] = $hit['name'];
    $semiResult[$x]['Hits'] = $hit['hits'];
    $x++;
  }

  // adding credit_card to table
  $x++;
  $hit_list = array();
  $semiResult[$x]['space'] = "----";
  $semiResult[$x]['Title'] = "CREDIT CARD";
  $x++;
  foreach ($credit_card as $value) {
    $aux_array = getFilterCount($begin, $end, $value->tid, $value->name);
    $hit_list[str_pad($aux_array['hits'], 4, "0", STR_PAD_LEFT)."_".$aux_array['name']] = array(
      'name' => $aux_array['name'],
      'hits' => $aux_array['hits']
    );
  }
  krsort($hit_list);
  foreach ($hit_list as $hit) {
    $semiResult[$x]['Name'] = $hit['name'];
    $semiResult[$x]['Hits'] = $hit['hits'];
    $x++;
  }

  // adding recreation to table
  $x++;
  $hit_list = array();
  $semiResult[$x]['space'] = "----";
  $semiResult[$x]['Title'] = "RECREATION";
  $x++;
  foreach ($recreation as $value) {
    $aux_array = getFilterCount($begin, $end, $value->tid, $value->name);
    $hit_list[str_pad($aux_array['hits'], 4, "0", STR_PAD_LEFT)."_".$aux_array['name']] = array(
      'name' => $aux_array['name'],
      'hits' => $aux_array['hits']
    );
  }
  krsort($hit_list);
  foreach ($hit_list as $hit) {
    $semiResult[$x]['Name'] = $hit['name'];
    $semiResult[$x]['Hits'] = $hit['hits'];
    $x++;
  }

  // adding life to table
  $x++;
  $hit_list = array();
  $semiResult[$x]['space'] = "----";
  $semiResult[$x]['Title'] = "LIFE";
  $x++;
  foreach ($life as $value) {
    $aux_array = getFilterCount($begin, $end, $value->tid, $value->name);
    $hit_list[str_pad($aux_array['hits'], 4, "0", STR_PAD_LEFT)."_".$aux_array['name']] = array(
      'name' => $aux_array['name'],
      'hits' => $aux_array['hits']
    );
  }
  krsort($hit_list);
  foreach ($hit_list as $hit) {
    $semiResult[$x]['Name'] = $hit['name'];
    $semiResult[$x]['Hits'] = $hit['hits'];
    $x++;
  }

  // adding services to table
  $x++;
  $hit_list = array();
  $semiResult[$x]['space'] = "----";
  $semiResult[$x]['Title'] = "SERVICES";
  $x++;
  foreach ($services as $value) {
    $aux_array = getFilterCount($begin, $end, $value->tid, $value->name);
    $hit_list[str_pad($aux_array['hits'], 4, "0", STR_PAD_LEFT)."_".$aux_array['name']] = array(
      'name' => $aux_array['name'],
      'hits' => $aux_array['hits']
    );
  }
  krsort($hit_list);
  foreach ($hit_list as $hit) {
    $semiResult[$x]['Name'] = $hit['name'];
    $semiResult[$x]['Hits'] = $hit['hits'];
    $x++;
  }
  
  // adding site_options to table
  $x++;
  $hit_list = array();
  $semiResult[$x]['space'] = "----";
  $semiResult[$x]['Title'] = "SITE OPTIONS";
  $x++;
  foreach ($site_options as $value) {
    $aux_array = getFilterCount($begin, $end, $value->tid, $value->name);
    $hit_list[str_pad($aux_array['hits'], 4, "0", STR_PAD_LEFT)."_".$aux_array['name']] = array(
      'name' => $aux_array['name'],
      'hits' => $aux_array['hits']
    );
  }
  krsort($hit_list);
  foreach ($hit_list as $hit) {
    $semiResult[$x]['Name'] = $hit['name'];
    $semiResult[$x]['Hits'] = $hit['hits'];
    $x++;
  }
   
  //die ("<pre>".print_r($semiResult,true)."</pre>");
  exportSpreadsheet($semiResult, $begin, $end);
  
}

function getFilterCount($begin, $end, $filters, $filters_name) {
  if ($filters == "") // non-advanced searches
    $query = db_query('SELECT count(sid) as hits FROM gca_searches WHERE created > :begin AND created < :end AND filters = :filters', array(':begin' => $begin, ':end' => $end, ':filters' => ""));
  else
    $query = db_query('SELECT count(sid) as hits FROM gca_searches WHERE created > :begin AND created < :end AND filters LIKE :filters', array(':begin' => $begin, ':end' => $end, ':filters' => "%".$filters."%"));

  while ($row = $query->fetchAssoc()) {
    if (checkGCARole($row["user"]) != 1) {
      $result["name"] = $filters_name;
      $result["hits"] = $row["hits"];
    }
  }

  if (isset($result)) {
    return $result;
  }
  return;  
}

function getSearches($begin, $end) {
  $query = db_query("SELECT sid, ip, created, data, filters, state FROM {gca_searches} WHERE created > :begin AND created < :end ORDER BY created DESC", array(':begin' => $begin, ':end' => $end));

  $x = 0;
  while ($row = $query->fetchAssoc()) {
    if (checkGCARole($row["user"]) != 1) {
      $result[$x]["sid"] = $row["sid"];
      $result[$x]["ip"] = $row["ip"];
      $result[$x]["created"] = $row["created"];
      $result[$x]["data"] = $row["data"];
      $result[$x]["filters"] = $row["filters"];
      $result[$x]["state"] = $row["state"];
      $x++;
    }
  }

  if (isset($result)) {
    return $result;
  }
  return;
}

function exportSpreadsheet($info, $begin, $end) {
  $headerNames = array();
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
  $objPHPExcel->getActiveSheet()->setTitle('Search Reports');

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
  
  $OutputFilename = "Search Results (" . date("Y-m-d", $begin) . " - " . date("Y-m-d", $end) . ").xlsx";
  header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); 
  header("Content-Disposition: attachment;filename=\"" . $OutputFilename . "\"");
  header('Cache-Control: max-age=0'); 

  $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

  unset($projectData);
  unset($pmCased);
  unset($headerNames);

  $objWriter->save('php://output');
}


function checkGCARole($user) {
  $query = db_query("SELECT rid FROM {users_roles} WHERE uid = :uid AND (rid = 6 || rid = 4)", array(':uid' => $user));
  while ($row = $query->fetchAssoc()) {
    return 1;
  }
  return 0;
}

function convertTime($string) {
  return strtotime("12:00am " . $string);
}

function displayForm() { ?>

Search Reports<br />
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