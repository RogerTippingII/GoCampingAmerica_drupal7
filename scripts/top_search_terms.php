<?php

/* Get top 500 search terms for Gocampingamerica.com */

$con = mysqli_connect("localhost","gcadb","f2897eg3fA33","gocamping");
if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }

$searchTerms = getTerms($con);
$x = 0;
foreach ($searchTerms as $key => $value) {
  echo $key . "," . $value . "<br />";
  $x++;
  if ($x == 500) {
    break;
  }
}

function getTerms($con) {
  $query = mysqli_query($con, "SELECT data FROM `gca_searches` WHERE data != ''");
  while ($row = mysqli_fetch_array($query)) {
    $key = trim(str_replace(array("'", '"', ","), array("", "", ""), strtolower($row["data"])));
	if ($key != "salina kansas" && $key != "richmond in") {
      $result[$key]++;
    }
  }
  if ($result) {
    arsort($result);
    return $result;
  }
  return;
}

mysqli_close($con);
?>