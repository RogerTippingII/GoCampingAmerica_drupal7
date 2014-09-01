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
set_time_limit(0);

$startTime = mktime();

// Delete all files in the partials directory

$files = glob('sites/default/files/dumps/partials/*');
foreach($files as $file){ 
  if(is_file($file)){
    unlink($file);
  }
}


//for ($i = 0; $i < 1; $i++) {

//  $parks = "";
$i = 0;
while(TRUE) {

  $parks = getParks($i);

  if ($parks) {

    foreach ($parks as $park) {
      //$parkData[] = unserialize(file_get_contents("http://www.gocampingamerica.com/scripts/getParkData.php?n=" . $park));
      $parkData[] = getParkData($park);
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
    unset($parks);
  } else {
    break;
  }
  $i++;
}

function getParks($off) {
  $limit = 1000;
  if ($off != 0) {
    $offset = $off * $limit;
  } else {
    $offset = 0;
  }
  //$query = db_query("SELECT n.nid FROM {node} n WHERE n.type = 'camp' AND n.status = 1 LIMIT %d, %d", $offset, $limit);
  $query = db_query("SELECT n.nid FROM {node} n WHERE n.type = 'camp' AND n.status = 1 LIMIT " . $offset . ", " . $limit);
  //$query = db_query("SELECT n.nid FROM {node} n WHERE n.type = 'camp' AND n.status = 1");
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
      
      $cell_val = str_replace("\r\n", '', $cell_val);
      $cell_val = str_replace("\r", '', $cell_val);
      $cell_val = str_replace("\n", '', $cell_val);
			
			$tmp .= "$col_sep$qut$cell_val$qut";
		}
		$output .= substr($tmp, 1).$row_sep;
	}
	
	return $output;
}

function getParkData($parkid) {
  
  $nodeInfo = node_load($parkid);

  $result = array();

  $result["drupalID"] = $nodeInfo->nid;
  $result["arvcID"] = $nodeInfo->name;
  $result["changed"] = $nodeInfo->changed;
  $result["description"] = $nodeInfo->field_park_description[LANGUAGE_NONE][0]["value"];
  $result["title"] = $nodeInfo->title;
  $result["path"] = $nodeInfo->path;
  $result["website"] = $nodeInfo->field_camp_website[LANGUAGE_NONE][0]["url"];
  $result["status"] = $nodeInfo->field_camp_status[LANGUAGE_NONE][0]["value"];
  $result["region"] = (isset($nodeInfo->field_region[LANGUAGE_NONE][0]["value"]) ? $nodeInfo->field_region[LANGUAGE_NONE][0]["value"] : '');
  //$result["z20amp"] = $nodeInfo->field_camp20amp[LANGUAGE_NONE][0]["value"];
  //$result["z30amp"] = $nodeInfo->field_camp30amp[LANGUAGE_NONE][0]["value"];
  //$result["z50amp"] = $nodeInfo->field_camp50amp[LANGUAGE_NONE][0]["value"];
  //$result["zElecWater"] = $nodeInfo->field_camp_electric_water[LANGUAGE_NONE][0]["value"];
  //$result["zElectric"] = $nodeInfo->field_camp_electrical[LANGUAGE_NONE][0]["value"];
  //$result["zFullHU"] = $nodeInfo->field_camp_full_hookups[LANGUAGE_NONE][0]["value"];
  //$result["zGroup"] = $nodeInfo->field_camp_group[LANGUAGE_NONE][0]["value"];
  //$result["zHigherAmp"] = $nodeInfo->field_camp_higher_amp[LANGUAGE_NONE][0]["value"];
  //$result["zRVLeng"] = $nodeInfo->field_camp_rv_length[LANGUAGE_NONE][0]["value"];
  //$result["zNoHU"] = $nodeInfo->field_camp_no_hookups[LANGUAGE_NONE][0]["value"];
  //$result["zPullThr"] = $nodeInfo->field_camp_pull_throughs[LANGUAGE_NONE][0]["value"];
  //$result["zCabins"] = $nodeInfo->field_camp_rental_cabins[LANGUAGE_NONE][0]["value"];
  //$result["zTrailers"] = $nodeInfo->field_camp_rental_trailers[LANGUAGE_NONE][0]["value"];
  //$result["zSeasonal"] = $nodeInfo->field_camp_seasonal[LANGUAGE_NONE][0]["value"];
  //$result["zSideouts"] = $nodeInfo->field_camp_sideouts[LANGUAGE_NONE][0]["value"];
  //$result["zPullThr"] = $nodeInfo->field_camp_pull_throughs[LANGUAGE_NONE][0]["value"];
  //$result["zTents"] = $nodeInfo->field_camp_tents[LANGUAGE_NONE][0]["value"];
  //$result["zTotals"] = $nodeInfo->field_camp_totals[LANGUAGE_NONE][0]["value"];
  $result["resURL"] = $nodeInfo->field_camp_reservation_website[LANGUAGE_NONE][0]["url"];
  //$result["resCo"] = $nodeInfo->field_camp_reservation_co[LANGUAGE_NONE][0]["value"];
  //$result["gorvingAmt"] = $nodeInfo->field_camp_gorving_amount
  //[LANGUAGE_NONE][0]["value"];
  //$result["gorvingEnd"] = $nodeInfo->field_camp_gorving_enddate[LANGUAGE_NONE][0]["value"];
  //$result["lastInv"] = $nodeInfo->field_camp_lastinvoiced[LANGUAGE_NONE][0]["value"];
  $result["discountPct"] = $nodeInfo->field_camp_discount_percent[LANGUAGE_NONE][0]["value"];
  $result["localInterest"] = $nodeInfo->field_camp_local_interest[LANGUAGE_NONE][0]["value"];
  $result["reviewID"] = $nodeInfo->field_camp_guestreview_id[LANGUAGE_NONE][0]["value"];
  //$result["memberID"] = $nodeInfo->field_camp_member_id[LANGUAGE_NONE][0]["value"];
  $result["directions"] = $nodeInfo->field_camp_directions[LANGUAGE_NONE][0]["value"];
  $result["rules"] = $nodeInfo->field_camp_rules[LANGUAGE_NONE][0]["value"];
  $result["email"] = $nodeInfo->field_camp_email[LANGUAGE_NONE][0]["email"];
  $result["video"] = $nodeInfo->field_camp_video_url[LANGUAGE_NONE][0]["url"];
  $result["stAssnID"] = $nodeInfo->field_camp_state_assnid[LANGUAGE_NONE][0]["value"];
  //$result["ctcName"] = $nodeInfo->field_park_contact_name[LANGUAGE_NONE][0]["value"];
  $result["stAssnOptIn"] = $nodeInfo->field_park_state_assn_optin[LANGUAGE_NONE][0]["value"];
  //$result["reviewed"] = $nodeInfo->field_camp_reviewed[LANGUAGE_NONE][0]["value"];
  $result["rates"] = $nodeInfo->field_camp_rates_text[LANGUAGE_NONE][0]["value"];
  $result["stAssnName"] = $nodeInfo->field_camp_state_assnname[LANGUAGE_NONE][0]["value"];
  $result["swampCity"] = $nodeInfo->field_camp_swamp_city[LANGUAGE_NONE][0]["value"];
  //$result["renew"] = $nodeInfo->field_camp_renew_date[LANGUAGE_NONE][0]["value"];
  //$result["officialOptIn"] = $nodeInfo->field_camp_official_optin[LANGUAGE_NONE][0]["value"];
  $result["reviewOptIn"] = $nodeInfo->field_park_guest_reviews_optin[LANGUAGE_NONE][0]["value"];
  //$result["zTotals"] = $nodeInfo->field_camp_totals[LANGUAGE_NONE][0]["value"];
  $result["facebook"] = $nodeInfo->field_park_facebook[LANGUAGE_NONE][0]["url"];
  $result["twitter"] = $nodeInfo->field_park_twitter[LANGUAGE_NONE][0]["url"];
  $result["tier"] = $nodeInfo->field_park_tier[LANGUAGE_NONE][0]["value"];
  $result["tierExp"] = $nodeInfo->field_park_tier_expiration[LANGUAGE_NONE][0]["value"];
  //$result["zTeepee"] = $nodeInfo->field_camp_teepee[LANGUAGE_NONE][0]["value"];
  //$result["zYurts"] = $nodeInfo->field_camp_yurts[LANGUAGE_NONE][0]["value"];
  //$result["zOther"] = $nodeInfo->field_camp_other[LANGUAGE_NONE][0]["value"];
  //$result["zTotalRVCalc"] = $nodeInfo->field_camp_total_rv_calc[LANGUAGE_NONE][0]["value"];
  //$result["zTotalCalc"] = $nodeInfo->field_camp_total_calc[LANGUAGE_NONE][0]["value"];
  //$result["yearRound"] = $nodeInfo->field_camp_open_year_round[LANGUAGE_NONE][0]["value"];
  $result["closedMonth"] = $nodeInfo->field_park_date_closed_month[LANGUAGE_NONE][0]["value"];
  $result["openMonth"] = $nodeInfo->field_park_date_open[LANGUAGE_NONE][0]["value"];
  $result["openDay"] = $nodeInfo->field_park_date_open_day[LANGUAGE_NONE][0]["value"];
  $result["extendStay"] = $nodeInfo->field_park_extended_stay[LANGUAGE_NONE][0]["value"];
  $result["closedDay"] = $nodeInfo->field_park_date_closed_day[LANGUAGE_NONE][0]["value"];
  //$result["companyAssoc"] = $nodeInfo->field_camp_company_assoc[LANGUAGE_NONE][0]["value"];
  //$result["billingStreet"] = $nodeInfo->field_park_billing_street[LANGUAGE_NONE][0]["value"];
  //$result["billingCity"] = $nodeInfo->field_park_billing_city[LANGUAGE_NONE][0]["value"];
  //$result["billingState"] = $nodeInfo->field_park_billing_state[LANGUAGE_NONE][0]["value"];
  $result["billingZIP"] = $nodeInfo->field_park_billing_zip[LANGUAGE_NONE][0]["value"];
  //$result["billingCounty"] = $nodeInfo->field_park_billing_country[LANGUAGE_NONE][0]["value"];
  //$result["billingStreet2"] = $nodeInfo->field_park_billing_street2[LANGUAGE_NONE][0]["value"];
  //$result["fax"] = $nodeInfo->field_camp_fax[LANGUAGE_NONE][0]["number"];
  $result["locStreet"] = $nodeInfo->field_location[LANGUAGE_NONE][0]["street"];
  $result["locStreet2"] = $nodeInfo->field_location[LANGUAGE_NONE][0]["additional"];
  $result["locCity"] = $nodeInfo->field_location[LANGUAGE_NONE][0]["city"];
  $result["locState"] = $nodeInfo->field_location[LANGUAGE_NONE][0]["province"];
  $result["locZIP"] = $nodeInfo->field_location[LANGUAGE_NONE][0]["postal_code"];
  $result["locCountry"] = $nodeInfo->field_location[LANGUAGE_NONE][0]["country"];
  $result["locLat"] = $nodeInfo->field_location[LANGUAGE_NONE][0]["latitude"];
  $result["locLong"] = $nodeInfo->field_location[LANGUAGE_NONE][0]["longitude"];
  $result["phone"] = $nodeInfo->field_camp_phone[LANGUAGE_NONE][0]["number"];
  $result["planitGr"] = $nodeInfo->field_planit_green[LANGUAGE_NONE][0]["value"];

  $filteredSlideshow = array_filter($nodeInfo->field_camp_slideshow, function($photo){
    return isset($photo);
  });

  $result["hasPhotos"] = (count($filteredSlideshow) > 0)?'yes':'no';

  $vocabularies = array('taxonomy_vocabulary_5', 'taxonomy_vocabulary_17', 'taxonomy_vocabulary_11', 'taxonomy_vocabulary_1', 'taxonomy_vocabulary_18', 
    'taxonomy_vocabulary_2', 'taxonomy_vocabulary_6', 'taxonomy_vocabulary_3', 'taxonomy_vocabulary_12');

  foreach($vocabularies as $vocabulary) {
    $vid = str_replace("taxonomy_vocabulary_","",$vocabulary);
    $voc = taxonomy_vocabulary_load($vid);
    $terms = entity_load('taxonomy_term', FALSE, array('vid' => $voc->vid));

    foreach($terms as $key => $term) {
      $result["term_" . str_replace(" ","_", strtolower($term->name))] = "0";
    }
    if (isset($nodeInfo->{$vocabulary}[LANGUAGE_NONE]) && is_array($nodeInfo->{$vocabulary}[LANGUAGE_NONE]) && count($nodeInfo->{$vocabulary}[LANGUAGE_NONE]) > 0) {
      foreach($nodeInfo->{$vocabulary}[LANGUAGE_NONE] as $key => $term) {
        $tid = $term['tid'];
        $t = $terms[$tid];
        $result["term_" . str_replace(" ","_", strtolower($t->name))] = "1";
      }
    }

  }

  /*
  $result["tags"] = "";
  foreach ($nodeInfo->taxonomy as $key => $value) {
   $result["tags"] .= $value->name . ", ";
  }
  $result["tags"] = substr($result["tags"], 0, -2);
  */

  ksort($result);

  //echo "<pre>";
  //print_r($result);
  //echo "</pre>";
  //echo serialize($result);
  return $result;

}

?>