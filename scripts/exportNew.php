<?php
/* ******************** */
/* Data export for GCA  */
/* ******************** */

// Bootstrap
chdir($_SERVER['DOCUMENT_ROOT']);
define('DRUPAL_ROOT', __DIR__ . "/..");
global $base_url;
$base_url = 'http://'.$_SERVER['HTTP_HOST'];
require_once './includes/bootstrap.inc';
require_once './includes/common.inc';
require_once './includes/module.inc';
//drupal_bootstrap(DRUPAL_BOOTSTRAP_SESSION);
//drupal_bootstrap(DRUPAL_BOOTSTRAP_DATABASE);
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);
drupal_load('module', 'node');
module_invoke('node', 'boot');

//echo "doc root: " . $_SERVER["DOCUMENT_ROOT"] . "<BR />";

$startTime = mktime();

// Delete all files in the partials directory

$files = glob('sites/default/files/dumps/partials/*');
foreach($files as $file){ 
  if(is_file($file)){
    unlink($file);
  }
}


for ($i = 0; $i < 1; $i++) {

  $parks = "";
  $parks = getParks($i);
  
  if ($parks) {

    foreach ($parks as $park) {
      $parkData[] = unserialize(file_get_contents("http://www.gocampingamerica.com/scripts/getParkData.php?n=" . $park));
    }

    echo "Start: " . date("m j, Y g:i a", $startTime) . "<br />";
    echo "End: " . date("m j, Y g:i a", mktime()) . "<br />";
    echo "Park Count: " . count($parks) . "<br />";

    //echo "<pre>";
    //print_r($parkData);
    //echo "</pre>";
//    echo "File: <a href='/sites/default/files/dumps/export.csv'>Download File</a><br />";
//    echo "<i>(Right click on link and select 'Save As'.)</i><br />";

  
    if ($i == 0) {
      $output = array_to_scv($parkData);
    } else {
      $output = array_to_scv($parkData, FALSE);
    }
    $file = "sites/default/files/dumps/partials/export";
    if ($i < 10) {
      $file .= "0";
    }	
	$file = $file . $i . ".csv";
    file_put_contents($file, $output);
    unset($parkData);
  }
}

function getParks($off) {
  $limit = 100;
  if ($off != 0) {
    $offset = $off * $limit;
  } else {
    $offset = 0;
  }
  //$query = db_query("SELECT n.nid FROM {node} n WHERE n.type = 'camp' AND n.status = 1 LIMIT %d, %d", $offset, $limit);
  $query = db_query("SELECT n.nid FROM {node} n WHERE n.type = 'camp' AND n.status = 1 LIMIT " . $offset . ", " . $limit);
//  while ($row = $query->fetchAssoc()) {
  foreach($query as $row) {
    //$result[] = $row["nid"];
    $result[] = $row->nid;
  }
  if (isset($result)) {
    return $result;
  }
  return;
}

/**
* Generatting CSV formatted string from an array.
* By Sergey Gurevich.
*/
function array_to_scv($array, $header_row = true, $col_sep = ",", $row_sep = ";\r", $qut = '"')
{
	if (!is_array($array) or !is_array($array[0])) return false;
	
	//Header row.
	if ($header_row)
	{
		foreach ($array[0] as $key => $val)
		{
			//Escaping quotes.
			$key = str_replace($qut, "$qut$qut", $key);
			$output .= "$col_sep$qut$key$qut";
		}
		$output = substr($output, 1).$row_sep;
	}
	//Data rows.
	foreach ($array as $key => $val)
	{
		$tmp = '';
		foreach ($val as $cell_key => $cell_val)
		{
			//Escaping quotes.
			$cell_val = str_replace($qut, "$qut$qut", $cell_val);
			$tmp .= "$col_sep$qut$cell_val$qut";
		}
		$output .= substr($tmp, 1).$row_sep;
	}
	
	return $output;
}

?>