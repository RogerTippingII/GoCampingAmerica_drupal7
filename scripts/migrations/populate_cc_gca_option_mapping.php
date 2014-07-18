<?php
/**
 * Created by IntelliJ IDEA.
 * User: patricepaquette
 * Date: 1/22/2014
 * Time: 3:17 PM
 */


chdir($_SERVER['DOCUMENT_ROOT']);
global $base_url;
$base_url = 'http://'.$_SERVER['HTTP_HOST'];
include_once './includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

include __DIR__.'/cc_gca_options_mapping_var.php';

$cc_gca_options_mapping = $cc_gca_options_mapping;

foreach($cc_gca_options_mapping as $mapping){
  if($mapping["Camp-Cal"] != ""){
    db_query("
      INSERT INTO cc_gca_options_mapping(gca_name, cc_name, category) VALUES('%s', '%s', '%s')",
      ($mapping["GCA"] != "")?$mapping["GCA"]:$mapping["Camp-Cal"],
      $mapping["Camp-Cal"],
      $mapping["Category"]
    );
  }
}