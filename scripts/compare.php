<?php 
//require_once('/var/www/vhosts/gocampingamerica.com/httpdocs/scripts/PHPExcel.php');

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


if (isset($_REQUEST["c1"]) && isset($_REQUEST["c2"])) {
  $instance1 = getInstance($_REQUEST["c1"]);
  $instance2 = getInstance($_REQUEST["c2"]);
  echo "<table style='width:1000px;'><tr><td valign='top'>";
  echo $instance1->title . "<br />";
  echo $instance1->field_camp_contact_name[0]["value"] . "<br />";
  echo $instance1->field_camp_status[0]["value"] . "<br />";
  echo $instance1->field_location[0]["street"] . "<br />";
  echo $instance1->field_location[0]["additional"] . "<br />";
  echo $instance1->field_location[0]["city"] . "<br />";
  echo $instance1->field_location[0]["province"] . "<br />";
  echo $instance1->field_location[0]["postal_code"] . "<br />";
  echo $instance1->field_location[0]["country"] . "<br />";
  echo $instance1->field_camp_phone[0]["number"] . "<br />";
  echo $instance1->field_camp_fax[0]["number"] . "<br />";
  echo $instance1->field_camp_email[0]["email"] . "<br />";
  echo $instance1->field_camp_tollfree_phone_number[0]["number"] . "<br />";
  echo "</td><td valign='top'>";
  echo $instance2->title . "<br />";
  echo $instance2->field_camp_contact_name[0]["value"] . "<br />";
  echo $instance2->field_camp_status[0]["value"] . "<br />";
  echo $instance2->field_location[0]["street"] . "<br />";
  echo $instance2->field_location[0]["additional"] . "<br />";
  echo $instance2->field_location[0]["city"] . "<br />";
  echo $instance2->field_location[0]["province"] . "<br />";
  echo $instance2->field_location[0]["postal_code"] . "<br />";
  echo $instance2->field_location[0]["country"] . "<br />";
  echo $instance2->field_camp_phone[0]["number"] . "<br />";
  echo $instance2->field_camp_fax[0]["number"] . "<br />";
  echo $instance2->field_camp_email[0]["email"] . "<br />";
  echo $instance2->field_camp_tollfree_phone_number[0]["number"] . "<br />";
  echo "</td></tr></table>";
}

function getInstance($cid) {
  $query = db_query("SELECT data FROM {park_changes} WHERE cid = %d LIMIT 1", $cid);
  while ($row = db_fetch_array($query)) {
    $result = unserialize($row["data"]);
  }
  if (isset($result)) {
    return $result;
  }
  return;
}
?>