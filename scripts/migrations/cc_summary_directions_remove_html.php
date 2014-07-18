<?php
/**
 * Created by IntelliJ IDEA.
 * User: patricepaquette
 * Date: 2014-07-09
 * Time: 3:23 PM
 */

chdir($_SERVER['DOCUMENT_ROOT']);
define('DRUPAL_ROOT', __DIR__ . "/../..");
global $base_url;
$base_url = 'http://' . $_SERVER['HTTP_HOST'];
require_once './includes/bootstrap.inc';
require_once './includes/common.inc';
require_once './includes/module.inc';
require_once('./includes/class.html2text.inc');
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);
drupal_load('module', 'node');
module_invoke('node', 'boot');

$results = db_query("SELECT * FROM cc_summary_directions");

foreach($results as $row){
  $new_summary = '';
  $new_directions = '';

  if($row->summary != ''){
    $new_summary = (new html2text($row->summary))->get_text();
  }
  if($row->directions != ''){
    $new_directions = (new html2text($row->directions))->get_text();
  }

  db_query("UPDATE cc_summary_directions SET summary = :summary, directions = :directions WHERE name = :name", array(":summary" => $new_summary, ":directions" => $new_directions, ":name" => $row->name));
}