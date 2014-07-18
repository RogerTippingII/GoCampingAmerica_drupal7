<?php
/**
 * Created by IntelliJ IDEA.
 * User: patricepaquette
 * Date: 2014-06-05
 * Time: 8:58 AM
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

$dev_rs = array();

db_set_active('legacy');

$legacy_rs = db_query("
  SELECT * FROM node
  WHERE node.created > 1396678265 AND node.type = 'camp'"
);

//$legacy_nids = array();
//
//foreach($legacy_rs as $legacy_row){
//  $legacy_nids[] = $legacy_row->nid;
//}

foreach($legacy_rs as $row){
  $nid = $row->nid;

  db_set_active('legacy');

  $to_add = array();
  $to_delete = array();

  $legacy_options = db_query("
    SELECT td.*, v.name vname
    FROM node
    JOIN term_node tn ON tn.vid = node.vid
    JOIN term_data td ON td.tid = tn.tid
    JOIN vocabulary v ON v.vid = td.vid
    WHERE node.nid = :nid
    ORDER BY td.tid",
    array(":nid" => $nid)
  );

  db_set_active();

  $dev_options = db_query("
    SELECT td.*, v.name vname
    FROM node
    JOIN taxonomy_index ti ON ti.nid = node.nid
    JOIN taxonomy_term_data td ON td.tid = ti.tid
    JOIN taxonomy_vocabulary v ON v.vid = td.vid
    WHERE node.nid = :nid
    ORDER BY td.tid",
    array(":nid" => $nid)
  );

  $legacy_options = $legacy_options->fetchAllAssoc("tid");
  $dev_options = $dev_options->fetchAllAssoc("tid");

  foreach($legacy_options as $legacy_key => &$legacy_option){
    foreach($dev_options as $dev_key => &$dev_option){
      if($legacy_key == $dev_key){
        unset($legacy_options[$legacy_key]);
        unset($dev_options[$dev_key]);
      }
    }
  }

  usort($legacy_options, function($option1, $option2){
    $c = strcmp($option1->vname, $option2->vname);

    if($c == 0){
      return strcmp($option1->name, $option2->name);
    }

    return $c;
  });

  usort($dev_options, function($option1, $option2){
    $c = strcmp($option1->vname, $option2->vname);

    if($c == 0){
      return strcmp($option1->name, $option2->name);
    }

    return $c;
  });

  if(count($legacy_options) == 0 && count($dev_options) == 0){
    echo $nid . " : " . $row->title . "<br/>";
    echo "to add : <br/>";
    foreach($legacy_options as $option){
      echo $option->vname . ":" . $option->name . "<br/> ";
    }
    echo "<br/><br/>";
    echo "to delete : <br/>";
    foreach($dev_options as $option){
      echo $option->vname . ":" . $option->name . "<br/> ";
    }


    echo "<br/><br/>";
  }

  unset($legacy_options);
  unset($dev_options);
}
