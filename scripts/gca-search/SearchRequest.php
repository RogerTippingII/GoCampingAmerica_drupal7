<?php
/**
 * Created by IntelliJ IDEA.
 * User: patricepaquette
 * Date: 2013-10-17
 * Time: 11:58 AM
 * To change this template use File | Settings | File Templates.
 */

require_once __DIR__ . "/../../sites/all/gca-search/controllers/ParkSearchCtrl.php";
require_once __DIR__ . "/../../sites/all/gca-search/repositories/ParkRepository.php";

switch($_REQUEST["type"]){
  case "location":
    $params = json_decode($_REQUEST["params"], true);
    $parks = ParkSearchCtrl::SearchByLocation($params["location"], $params["radius"], $params["tags"], $params["park_features"]);

    SendHttpResponse(200, json_encode($parks));
    return;

  case "park_name":
    $params = json_decode($_REQUEST["params"], true);
    $parks = ParkSearchCtrl::SearchByParkName($params["park_name"], $params["state_short"], $params["tags"]);

    SendHttpResponse(200, json_encode($parks));
    return;

  case "state":
    $params = json_decode($_REQUEST["params"], true);
    $parks = ParkSearchCtrl::SearchByState($params["state_short"], $params["tags"], $params["park_features"]);

    SendHttpResponse(200, json_encode($parks));
    return;

  case "park_detail":
    $params = json_decode($_REQUEST["params"], true);
    $nid = $params["nid"];
    $name = $params["name"];

    if(!$nid){
      $node = ParkRepository::GetNodeByTitle($name);

      if($node){
        $nid = $node["nid"];
      }
    }

    $detail = ParkSearchCtrl::GetParkDetail($nid);

    SendHttpResponse(200, json_encode($detail));
    return;

  case "state_info":
    $params = json_decode($_REQUEST["params"], true);
    $stateInfo = ParkSearchCtrl::GetStateInfo($params["state_long"]);

    SendHttpResponse(200, json_encode($stateInfo));
    return;

  case "taxonomies":
    $taxonomies = ParkSearchCtrl::GetTaxonomies();

    SendHttpResponse(200, json_encode($taxonomies));
    return;

  case "features":
    $features = ParkSearchCtrl::GetFeatureList();

    SendHttpResponse(200, json_encode($features));
    return;

  case "park_taxonomies":
    $params = json_decode($_REQUEST["params"], true);

    $taxonomies = ParkSearchCtrl::GetParkTaxonomies($params["nid"]);

    SendHttpResponse(200, json_encode($taxonomies));
    return;

  case "park_features":
    $params = json_decode($_REQUEST["params"], true);
    $features = ParkSearchCtrl::GetParkFeatures($params["nid"]);

    SendHttpResponse(200, json_encode($features));
    return;

  case "park_detail_w_taxonomies":
    $params = json_decode($_REQUEST["params"], true);
    $nid = $params["nid"];
    $name = $params["name"];

    if(!$nid){
      $node = ParkRepository::GetNodeByTitle($name);

      if($node){
        $nid = $node["nid"];
      }
    }

    $details = ParkSearchCtrl::GetParkDetail($nid);
    $taxonomies = ParkSearchCtrl::GetParkTaxonomies($nid);
    $features = ParkSearchCtrl::GetParkFeatures($nid);
    $imagePaths = ParkSearchCtrl::GetParkImages($nid);

    foreach($features as $key => $feature_set){
      if(!isset($taxonomies[$key])){
        $taxonomies[$key] = array();
      }

      $taxonomies[$key] = array_merge($taxonomies[$key], $feature_set);
    }

    $details["taxonomies"] = $taxonomies;
    $details["image_paths"] = $imagePaths;

    SendHttpResponse(200, json_encode($details));
    return;
}

function SendHttpResponse($status, $body, $content_type = 'application/json', $from = 'GCA'){
  header('HTTP/1.1 '. $status, true, $status);
  header('Content-type: '. $content_type);
  header('From', $from);
  echo $body;
}
