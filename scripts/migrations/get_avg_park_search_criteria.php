<?php
/**
 * Created by IntelliJ IDEA.
 * User: patricepaquette
 * Date: 2014-03-11
 * Time: 10:35 PM
 */

chdir($_SERVER['DOCUMENT_ROOT']);
global $base_url;
$base_url = 'http://'.$_SERVER['HTTP_HOST'];
include_once __DIR__ .'/../../includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

$rs = db_query("SELECT * FROM gca_searches");

$add = 0;
$counter = 0;
while($row = db_fetch_array($rs)){
  $criteriaCount = count(explode('|', $row["filters"]));
  $add += $criteriaCount;
  $counter++;
}

echo $add/$counter;