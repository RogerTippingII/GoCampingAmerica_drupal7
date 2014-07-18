<?php
/**
 * Created by IntelliJ IDEA.
 * User: patricepaquette
 * Date: 2014-06-16
 * Time: 11:14 PM
 */

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


db_set_active('legacy');

$legacy_roles = db_query("SELECT * FROM users_roles JOIN users ON users.uid = users_roles.uid WHERE rid = 5");

db_set_active();

foreach($legacy_roles as $row){
  $num_rows = db_query("SELECT COUNT(*) FROM users_roles JOIN users ON users.uid = users_roles.uid WHERE name = :name AND rid = 5", array(":name" => $row->name))->fetchField();


  if($num_rows == 0){
    db_query("INSERT INTO users_roles(uid, rid) SELECT uid, 5 FROM users WHERE name = :name LIMIT 1", array(":name" => $row->name));
  }
}