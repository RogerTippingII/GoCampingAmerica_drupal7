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

if (isset($_REQUEST["nid"])) {
  $nid = $_REQUEST["nid"];
  $nodeInfo = node_load($nid);
  $deals = serialize(getDeals($nodeInfo));
  echo $deals;
}

function getDeals($nodeInfo) {
if ($nodeInfo->field_camp_state_assnid[0]["value"] && $nodeInfo->field_park_state_assn_optin[0]["value"]) {
    $timenow = date("Y-m-d" . "T" . "G:i:s", mktime());
    $query = db_query("SELECT DISTINCT nid FROM {content_type_deal} WHERE field_deal_sid_value = %d AND field_deal_start_value < '%s' AND field_deal_end_value > '%s'", $nodeInfo->field_camp_state_assnid[0]["value"], $timenow, $timenow);
    $x = 0;	
    while ($row = db_fetch_array($query)) {
	  $result[$x] = node_load($row["nid"]);
	  $x++;
	}
	if ($result) {
	  return $result;
	}
  }
  return;
}
?>