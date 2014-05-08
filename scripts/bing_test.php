<?php
$string = "Richmond, in";
$string = urlencode($string);
$path = "http://dev.virtualearth.net/REST/v1/Locations?q=" . $string . "&key=ArE2VaXCsE8FJpHX5w2rKnJCc_yQKBg9ovETjEeLX7XBhRTDw-OU-HmmBi0eJXMO";
echo $path . "<br />";
$raw = json_decode(file_get_contents($path));
$lat = $raw->resourceSets[0]->resources[0]->point->coordinates[1];
$lng = $raw->resourceSets[0]->resources[0]->point->coordinates[0];
echo $lat . " | " . $lng;
?>