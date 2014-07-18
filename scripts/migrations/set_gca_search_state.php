<?php
/**
 * Created by IntelliJ IDEA.
 * User: patricepaquette
 * Date: 2014-03-11
 * Time: 12:44 PM
 */

chdir($_SERVER['DOCUMENT_ROOT']);
global $base_url;
$base_url = 'http://'.$_SERVER['HTTP_HOST'];
include_once __DIR__ .'/../../includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

require __DIR__ . "/vars/us_states.php";

foreach($states_list AS $abbrev => $state_name){
  db_query(
    "UPDATE gca_searches SET state = '%s' WHERE data LIKE '%s' OR data LIKE '%s' OR data LIKE '%s'",
    $abbrev, "%% $abbrev", "%% $abbrev %%", "%%$state_name%%"
  );

  foreach($cities_list[strtoupper($state_name)] AS $city_name){
    db_query(
      "UPDATE gca_searches SET state = '%s' WHERE data LIKE '%s'",
      $abbrev, "%%$city_name%%"
    );
  }
}