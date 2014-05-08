<?php

// Bootstrap
chdir($_SERVER['DOCUMENT_ROOT']);
global $base_url;
$base_url = 'http://'.$_SERVER['HTTP_HOST'];
require_once './includes/bootstrap.inc';
require_once './includes/common.inc';
require_once './includes/module.inc';
//drupal_bootstrap(DRUPAL_BOOTSTRAP_SESSION);
//drupal_bootstrap(DRUPAL_BOOTSTRAP_DATABASE);
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);
drupal_load('module', 'node');
module_invoke('node', 'boot');

//$parks = getParks();
$parks = array(109000);
echo "Count: " . count($parks) . "<br /><br />";
$skipFields = array("type", "language", "comment", "promote", "moderate", "sticky", "tnid", "translate", "revision_uid", "log", "format", "picture", "data", "field_camp_slideshow", "field_camp_contact", "node_weight", "print_display", "print_display_comment", "print_display_urllist", "print_mail_display", "print_mail_display_comment", "print_mail_display_urllist", "last_comment_timestamp", "last_comment_name", "comment_count", "0", "channel", "premiere", "remnant", "revision_timestamp");
$specialFields = array("field_planit_green", "field_location", "taxonomy");

foreach ($parks as $park) {
  $dataString .= getDataString($park, $skipFields, $specialFields) . "<br />";
}

echo $dataString;



function getDataString($nid, $skipFields, $specialFields) {
  $data = node_load($nid);

  foreach ($data as $key => $value) {
    if (!in_array($key, $skipFields)) {
      if (!is_array($value)) {
        $result[$key] = $value;
      } else {
        if (!in_array($key, $specialFields)) {
          if (isset($value[0]["value"])) {
            $result[$key] = $value[0]["value"];
          } elseif (isset($value[0]["url"])) {
            $result[$key] = $value[0]["url"];
          } elseif (isset($value[0]["email"])) {
            $result[$key] = $value[0]["email"];
          } elseif (isset($value[0]["number"])) {
            $result[$key] = $value[0]["number"];
          } else {
            $result[$key] = "";
          }
        } else {
          // Is either field_planit_green, field_location or taxonomy
        
          if ($key == "field_planit_green") {
            $result[$key] = "";
          } elseif ($key == "field_location") {
            foreach ($value[0] as $key2 => $value2) {
              $tempKey = $key . "_" . $key2;
              $result[$tempKey] = $value2;
            }
          } elseif ($key == "taxonomy") {
           
            foreach ($value as $key3 => $value3) {
              $tagString .= ", " . $value3->name;
            }
            $finalTagString = substr($tagString, 2, strlen($tagString));
            $result["tags"] = $finalTagString;
          
          }
        }
      }
    }
  }

  echo "<pre>";
  print_r($result);
  echo "</pre>";


  foreach ($result as $key => $value) {
    if (is_numeric($value)) {
      if ($key == "created" || $key == "changed") {
        $resultString .= '"' . convertTimestamp($value) . '", ';
      } else {
        $resultString .= $value . ", ";
      }
    } elseif ($value == "") {
      $resultString .= ", ";
    } else {
      $resultString .= '"' . str_replace(array("\n", "\r", "<p>", "</p>"), "", str_replace(array(",", ";"), "||", $value)) . '", ';
    }
  }
  $resultString = substr($resultString, 0, (strlen($resultString) - 2)) . ";";
  return $resultString;
}

function getParks() {
  $query = db_query_range("SELECT nid FROM {node} WHERE type = 'camp' AND status = 1 ORDER BY nid ASC", 5250, 750);
  while ($row = db_fetch_array($query)) {
    $result[] = $row["nid"];
  }
  return $result;
}

function convertTimestamp($timestamp) {
  $result = date("Y-m-d", $timestamp) . "T" . date("H:i:s", $timestamp);
  return $result;
}
?>