<?php

/* ********************************************** */
/* This script receives search criteria from the  */
/* Find a Park page, runs the database query and  */
/* displays the results. It also provides the     */
/* list of parks for the State Overview pages.    */
/* ********************************************** */

/*
 * There are three scenarios in which this script
 * receives requests:
 * 1. The home page (location search, no filters)
 * 2. The Find a Park page (location search, w/filters)
 * 3. The state pages (location search, just within
 *    the respective state
 */

// Bootstrap
chdir($_SERVER['DOCUMENT_ROOT']);
global $base_url;
$base_url = 'http://'.$_SERVER['HTTP_HOST'];
require_once __DIR__ . '/../includes/bootstrap.inc';
require_once __DIR__ . '/../includes/common.inc';
require_once __DIR__ . '/../includes/module.inc';
require_once __DIR__ . '/vendor/FirePHPCore/FirePHP.class.php';
require_once __DIR__ . '/gca-search/repositories/ParkRepository.php';
require_once __DIR__ . '/gca-search/lib/Util.php';
require_once __DIR__ . '/vendor/autoload.php';

drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);
drupal_load('module', 'node');
module_invoke('node', 'boot');

$firephp = FirePHP::getInstance(true);
Twig_Autoloader::register();
$loader = new Twig_Loader_Filesystem('scripts/gca-search/site/public/Templates');
$twig = new Twig_Environment($loader);
ob_start();
$zoom = 6;

?>

<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js"></script>
  
<?php
// Main controller

if (isset($_REQUEST["action"])) {
  // hs = homepage search
  // fp = find a park search
  // st = state search
  $offset = 0;
  if ($_REQUEST["action"] == "fp") {
    $smap = $_REQUEST["smap"];
    $tempResults = fpSearch();
    if ($tempResults["tags"]) {
      $results = $tempResults["tag_results"];
    } else {
      $results = $tempResults["results"];
    }

    for($i = 0; $i < sizeof($results); $i++){
      $details = ParkRepository::GetParkDetails($results[$i]['nid']);

      $results[$i] = array_merge($results[$i], $details);
      $results[$i]['link'] = drupal_get_path_alias("node/" . $results[$i]["nid"]);
      $markers[] = array(
        "latitude" => $results[$i]['lat'],
        "longitude" => $results[$i]['lng'],
        "html" => "<b>". $details["title"] ."</b><br/>".
                  "Rates: ". (($results[$i]["rates"])?$results[$i]["rates"]:"Not Specified<br/>") .
                  "<a href='../". drupal_get_path_alias("node/". $results[$i]['nid']) ."' target='_blank'>View &gt;&gt;</a>"

      );
    }

    $firephp->log($results, "Results");
    echo $twig->render('fap_search_results.twig', array(
      "results" => $results,
      "gmap" => array(
        "markers" => $markers,
        "geo" => array(
          "latitude" => $tempResults["geo"][0],
          "longitude" => $tempResults["geo"][1]
        ),
        "zoom" => $zoom
      )
    ));
  }
}

function fpSearch() {
  global $firephp;
  
  // First, get all the parks within the radius

  $search = getIncoming();

  $firephp->log($search["location"]);
  $search["results"] = ParkRepository::ByLocation($search["location"], $search["radius"]);
  $search["tags"] = array_filter($search["tags"]);

  // Next, get all parks that match the tags
  if ($search["tags"] && $search["tags"][0] != "") {
    $search["tag_results"] = ParkRepository::FilterByTags($search["tags"], $search["results"]);
  } else {
    unset($search["tags"]);
  }
  
  return $search;
}

function getIncoming() {
  $search["tags"] = explode("|", str_replace(array("%22", '"'), "", $_REQUEST["t"]));
  $search["location"] = $_REQUEST["l"];
  $search["radius"] = $_REQUEST["r"];
  $search["geo"] = Util::GetGeoMSDN($search["location"]);
  $search["page_limit"] = 20;
  if (isset($_REQUEST["offset"])) {
	$search["offset"] = (int) $search["offset"];
  } else {
	$search["offset"] = 0;
  }
  return $search;
}

?>