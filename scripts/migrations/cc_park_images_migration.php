<?php
/**
 * Created by IntelliJ IDEA.
 * User: patricepaquette
 * Date: 1/2/2014
 * Time: 2:38 PM
 */

chdir($_SERVER['DOCUMENT_ROOT']);
global $base_url;
$base_url = 'http://'.$_SERVER['HTTP_HOST'];
$cc_import_images_url = "./sites/default/files/cc_import/";
include_once './includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);
ini_set('max_execution_time', 300);

//modify file paths to match GCA's directory structure
db_query("UPDATE cc_park_images_import cci
          SET POCUWI_REGULAR_IMAGE_URL = REPLACE(POCUWI_REGULAR_IMAGE_URL, 'http://www.campingfriend.com/', 'sites/default/files/cc_import/');");
db_query("UPDATE cc_park_images_import cci
          SET POCUWI_SMALL_IMAGE_URL = REPLACE(POCUWI_SMALL_IMAGE_URL, 'http://www.campingfriend.com/', 'sites/default/files/cc_import/');");

//create file rows in database
$rs_cc_img_data = db_query(
  " SELECT *
    FROM node
    JOIN content_type_camp c ON c.vid = node.vid
    JOIN cc_park_images_import cc ON c.field_camp_lagacy_id_value = cc.CU_ID
    ORDER BY cc.CU_ID"
);

$bufferRow = null;

$counterNotExists = 0;

while($row = db_fetch_array($rs_cc_img_data)){
  $imgPath = $row["POCUWI_REGULAR_IMAGE_URL"];

  if(!file_exists($imgPath)){
    echo $imgPath . " does not exist\n";
    echo ++$counterNotExists;
  }
  else{
    if(!isset($bufferRow) || $bufferRow["nid"] != $row["nid"]){
      if(isset($node)){
        $node = node_submit($node);
        node_save($node);
      }

      $node = node_load($row["nid"]);
    }

    $field = content_fields("field_image", "content_type_camp");
    $validators = array_merge(filefield_widget_upload_validators($field), imagefield_widget_upload_validators($field));
    $files_path = filefield_widget_file_path($field);
    $file = field_file_save_file($imgPath, $validators, $files_path, FILE_EXISTS_REPLACE);

    $node->field_camp_slideshow[] = $file;

    if(strpos($imgPath, 'home1.jpg') !== FALSE){
      $rs_thumbnail_insert = db_query("INSERT INTO park_thumbnail_paths(nid, filepath) VALUES(%d, '%s')", $row["nid"], $row["POCUWI_SMALL_IMAGE_URL"]);
      if(!$rs_cc_img_data){
        echo "Could not insert thumbnail path \n";
      }
    }
  }

  $bufferRow = $row;
}
