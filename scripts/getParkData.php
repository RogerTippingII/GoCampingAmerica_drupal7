<?php

// Bootstrap
chdir($_SERVER['DOCUMENT_ROOT']);
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

if (isset($_REQUEST["n"])) {
  $nodeInfo = node_load($_REQUEST["n"]);
}



$result["drupalID"] = $nodeInfo->nid;
$result["arvcID"] = $nodeInfo->name;
$result["changed"] = $nodeInfo->changed;
$result["description"] = $nodeInfo->field_park_description[0]["value"];
$result["title"] = $nodeInfo->title;
$result["path"] = $nodeInfo->path;
$result["website"] = $nodeInfo->field_camp_website[0]["url"];
$result["status"] = $nodeInfo->field_camp_status[0]["value"];
//$result["z20amp"] = $nodeInfo->field_camp20amp[0]["value"];
//$result["z30amp"] = $nodeInfo->field_camp30amp[0]["value"];
//$result["z50amp"] = $nodeInfo->field_camp50amp[0]["value"];
//$result["zElecWater"] = $nodeInfo->field_camp_electric_water[0]["value"];
//$result["zElectric"] = $nodeInfo->field_camp_electrical[0]["value"];
//$result["zFullHU"] = $nodeInfo->field_camp_full_hookups[0]["value"];
//$result["zGroup"] = $nodeInfo->field_camp_group[0]["value"];
//$result["zHigherAmp"] = $nodeInfo->field_camp_higher_amp[0]["value"];
//$result["zRVLeng"] = $nodeInfo->field_camp_rv_length[0]["value"];
//$result["zNoHU"] = $nodeInfo->field_camp_no_hookups[0]["value"];
//$result["zPullThr"] = $nodeInfo->field_camp_pull_throughs[0]["value"];
//$result["zCabins"] = $nodeInfo->field_camp_rental_cabins[0]["value"];
//$result["zTrailers"] = $nodeInfo->field_camp_rental_trailers[0]["value"];
//$result["zSeasonal"] = $nodeInfo->field_camp_seasonal[0]["value"];
//$result["zSideouts"] = $nodeInfo->field_camp_sideouts[0]["value"];
//$result["zPullThr"] = $nodeInfo->field_camp_pull_throughs[0]["value"];
//$result["zTents"] = $nodeInfo->field_camp_tents[0]["value"];
//$result["zTotals"] = $nodeInfo->field_camp_totals[0]["value"];
$result["resURL"] = $nodeInfo->field_camp_reservation_website[0]["url"];
//$result["resCo"] = $nodeInfo->field_camp_reservation_co[0]["value"];
//$result["gorvingAmt"] = $nodeInfo->field_camp_gorving_amount
//[0]["value"];
//$result["gorvingEnd"] = $nodeInfo->field_camp_gorving_enddate[0]["value"];
//$result["lastInv"] = $nodeInfo->field_camp_lastinvoiced[0]["value"];
$result["discountPct"] = $nodeInfo->field_camp_discount_percent[0]["value"];
$result["localInterest"] = $nodeInfo->field_camp_local_interest[0]["value"];
$result["reviewID"] = $nodeInfo->field_camp_guestreview_id[0]["value"];
//$result["memberID"] = $nodeInfo->field_camp_member_id[0]["value"];
$result["directions"] = $nodeInfo->field_camp_directions[0]["value"];
$result["rules"] = $nodeInfo->field_camp_rules[0]["value"];
$result["email"] = $nodeInfo->field_camp_email[0]["email"];
$result["video"] = $nodeInfo->field_camp_video_url[0]["url"];
$result["stAssnID"] = $nodeInfo->field_camp_state_assnid[0]["value"];
//$result["ctcName"] = $nodeInfo->field_park_contact_name[0]["value"];
$result["stAssnOptIn"] = $nodeInfo->field_park_state_assn_optin[0]["value"];
//$result["reviewed"] = $nodeInfo->field_camp_reviewed[0]["value"];
$result["rates"] = $nodeInfo->field_camp_rates_text[0]["value"];
$result["stAssnName"] = $nodeInfo->field_camp_state_assnname[0]["value"];
$result["swampCity"] = $nodeInfo->field_camp_swamp_city[0]["value"];
//$result["renew"] = $nodeInfo->field_camp_renew_date[0]["value"];
//$result["officialOptIn"] = $nodeInfo->field_camp_official_optin[0]["value"];
$result["reviewOptIn"] = $nodeInfo->field_park_guest_reviews_optin[0]["value"];
//$result["zTotals"] = $nodeInfo->field_camp_totals[0]["value"];
$result["facebook"] = $nodeInfo->field_park_facebook[0]["url"];
$result["twitter"] = $nodeInfo->field_park_twitter[0]["url"];
$result["tier"] = $nodeInfo->field_park_tier[0]["value"];
$result["tierExp"] = $nodeInfo->field_park_tier_expiration[0]["value"];
//$result["zTeepee"] = $nodeInfo->field_camp_teepee[0]["value"];
//$result["zYurts"] = $nodeInfo->field_camp_yurts[0]["value"];
//$result["zOther"] = $nodeInfo->field_camp_other[0]["value"];
//$result["zTotalRVCalc"] = $nodeInfo->field_camp_total_rv_calc[0]["value"];
//$result["zTotalCalc"] = $nodeInfo->field_camp_total_calc[0]["value"];
//$result["yearRound"] = $nodeInfo->field_camp_open_year_round[0]["value"];
$result["closedMonth"] = $nodeInfo->field_park_date_closed_month[0]["value"];
$result["openMonth"] = $nodeInfo->field_park_date_open[0]["value"];
$result["openDay"] = $nodeInfo->field_park_date_open_day[0]["value"];
$result["extendStay"] = $nodeInfo->field_park_extended_stay[0]["value"];
$result["closedDay"] = $nodeInfo->field_park_date_closed_day[0]["value"];
//$result["companyAssoc"] = $nodeInfo->field_camp_company_assoc[0]["value"];
//$result["billingStreet"] = $nodeInfo->field_park_billing_street[0]["value"];
//$result["billingCity"] = $nodeInfo->field_park_billing_city[0]["value"];
//$result["billingState"] = $nodeInfo->field_park_billing_state[0]["value"];
$result["billingZIP"] = $nodeInfo->field_park_billing_zip[0]["value"];
//$result["billingCounty"] = $nodeInfo->field_park_billing_country[0]["value"];
//$result["billingStreet2"] = $nodeInfo->field_park_billing_street2[0]["value"];
//$result["fax"] = $nodeInfo->field_camp_fax[0]["number"];
$result["locStreet"] = $nodeInfo->field_location[0]["street"];
$result["locStreet2"] = $nodeInfo->field_location[0]["additional"];
$result["locCity"] = $nodeInfo->field_location[0]["city"];
$result["locState"] = $nodeInfo->field_location[0]["province"];
$result["locZIP"] = $nodeInfo->field_location[0]["postal_code"];
$result["locCountry"] = $nodeInfo->field_location[0]["country"];
$result["locLat"] = $nodeInfo->field_location[0]["latitude"];
$result["locLong"] = $nodeInfo->field_location[0]["longitude"];
$result["phone"] = $nodeInfo->field_camp_phone[0]["number"];
$result["planitGr"] = $nodeInfo->field_planit_green[0]["value"];

$filteredSlideshow = array_filter($nodeInfo->field_camp_slideshow, function($photo){
  return isset($photo);
});

$result["hasPhotos"] = (count($filteredSlideshow) > 0)?'yes':'no';

$result["tags"] = "";
foreach ($nodeInfo->taxonomy as $key => $value) {
 $result["tags"] .= $value->name . ", ";
}
$result["tags"] = substr($result["tags"], 0, -2);


ksort($result);

//echo "<pre>";
//print_r($result);
//echo "</pre>";
echo serialize($result);
?>