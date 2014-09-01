<?php
/**
 * Created by IntelliJ IDEA.
 * User: patricepaquette
 * Date: 2014-07-17
 * Time: 1:27 PM
 */
error_reporting(E_ALL);
ini_set('display_errors', '1');

chdir($_SERVER['DOCUMENT_ROOT']);
define('DRUPAL_ROOT', __DIR__ . "/../..");
global $base_url;
$base_url = 'http://' . $_SERVER['HTTP_HOST'];
require_once './includes/bootstrap.inc';
require_once './includes/common.inc';
require_once './includes/module.inc';

drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);
drupal_load('module', 'node');
module_invoke('node', 'boot');
module_invoke('taxonomy', 'boot');

// get all states with user info
$result = db_query("SELECT * FROM {state_assoc_mapping}");

$state_parks = array();

while ($row = $result->fetchAssoc()) {
  $state_parks[] = $row;
}

foreach ($state_parks as $id => $park) {
  $subquery = db_select('location');
  $subquery->addField('location', 'lid');
  $subquery->condition('location.province', $park['state']);
  
  $query = new EntityFieldQuery();
  $query->entityCondition('entity_type', 'node')
    ->entityCondition('bundle', 'camp')
    ->fieldCondition('field_location', 'lid', $subquery, 'IN')
    ->fieldCondition('field_camp_stateassn_user', 'uid', $park['uid'], '!=');
  
  $result = $query->execute();

  print "Checking state assoc for all parks from state: " . $park['state'] . "<br>";

  if (isset($result['node'])) {
    $news_items_nids = array_keys($result['node']);
    print ($park['state']."<pre>".print_r($result,true)."</pre><br>Park should have this uid under state assoc info= ".$park['uid']."<hr>");
    //$entities = entity_load('node', array_keys($result['node']));

  } else {
    print "No wrong state assoc from state: " . $park['state'] . "<br><hr>";
  }

}

