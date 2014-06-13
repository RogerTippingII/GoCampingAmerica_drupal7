<?php
// Bootstrap
chdir($_SERVER['DOCUMENT_ROOT']);
define('DRUPAL_ROOT', __DIR__ . "/..");
global $base_url;
$base_url = 'http://' . $_SERVER['HTTP_HOST'];
require_once './includes/bootstrap.inc';
require_once './includes/common.inc';
require_once './includes/module.inc';
require_once './sites/all/gca-search/lib/Util.php';
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);
drupal_load('module', 'node');
module_invoke('node', 'boot');

if (isset($_REQUEST["nid"])) {
  $nid = $_REQUEST["nid"];
  $nodeInfo = node_load($nid);
  $deals = serialize(getDeals($nodeInfo));
  echo $deals;
}

function getDeals($nodeInfo)
{
  if ($nodeInfo->field_camp_state_assnid[LANGUAGE_NONE][0]["value"] && $nodeInfo->field_park_state_assn_optin[LANGUAGE_NONE][0]["value"]) {
    $timenow = date("Y-m-d" . "T" . "G:i:s", mktime());
//    $query = db_query("SELECT DISTINCT nid FROM {content_type_deal} WHERE field_deal_sid_value = %d AND field_deal_start_value < '%s' AND field_deal_end_value > '%s'", $nodeInfo->field_camp_state_assnid[0]["value"], $timenow, $timenow);

    $query = EntityFieldQuery();
    $query->entityCondition('entity_type', 'node')
      ->entityCondition('bundle', 'deal')
      ->fieldCondition('field_deal_sid', 'value', $nodeInfo->field_camp_state_assnid[LANGUAGE_NONE][0]["value"])
      ->fieldCondition('field_deal_start', 'value', $timenow, '<')
      ->fieldCondition('field_deal_end', 'value', $timenow, '>');

    $result = $query->execute();

    $nids = array();
    if(isset($result["node"])){
      $nids = array_keys($result["node"]);
    }

    foreach($nids as $nid)
      $result[] = node_load($nid);
    }

    if ($result) {
      return $result;
    }
  }

  return;
}

?>