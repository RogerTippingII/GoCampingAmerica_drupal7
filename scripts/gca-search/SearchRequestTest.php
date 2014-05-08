<?php
/**
 * Created by IntelliJ IDEA.
 * User: patricepaquette
 * Date: 2013-10-22
 * Time: 1:00 AM
 * To change this template use File | Settings | File Templates.
 */

class SearchRequestTest extends PHPUnit_Framework_TestCase{
  public function testSearchParksByLocation(){
    $searchType = "location";
    $searchParams = array(
      "location" => "denver, CO",
      "radius" => 25
    );
    $url = $this->buildSearchUrl($searchType, $searchParams);

    echo $url ."\n";

    $responseBody = file_get_contents($url);
    $parks = json_decode($responseBody, true);

    $this->assertTrue(isset($parks));
    $this->assertTrue(count($parks) == 3);
  }

  public function testSearchParksByState(){
    $searchType = "state";
    $searchParams = array(
      "state_short" => "NY",
      "state_long" => "New York"
    );
    $url = $this->buildSearchUrl($searchType, $searchParams);

    $responseBody = file_get_contents($url);
    $parks = json_decode($responseBody, true);

    $this->assertTrue(isset($parks));
  }

  public function testSearchParksByParkName(){
    $type = "park_name";
    $params = array(
      "park_name" => "Prospect RV Park"
    );
    $url = $this->buildSearchUrl($type, $params);

    $responseBody = file_get_contents($url);
    $parks = json_decode($responseBody, true);

    $this->assertTrue(isset($parks));
  }

  public function testGetStateInfo(){
    $type = "state_info";
    $params = array(
      "state_short" => "NY",
      "state_long" => "New York"
    );
    $url = $this->buildSearchUrl($type, $params);

    $responseBody = file_get_contents($url);
    $state_info = json_decode($responseBody, true);

    $this->assertTrue(isset($state_info));
  }

  public function testGetParkTaxonomies(){
    $type = "park_taxonomies";
    $url = $this->buildSearchUrl($type, array());

    $responseBody = file_get_contents($url);
    $taxonomies= json_decode($responseBody, true);

    $this->assertTrue(isset($taxonomies));
  }

  private function buildSearchUrl($type, $params){
    return "http://gca.dev/scripts/SearchRequest.php?type=". $type ."&params=". urlencode(json_encode($params));
  }
}