<?php
$articles = getArticles();
arsort($articles);

$x = 0;
foreach ($articles as $key => $value) {
  $result[$x] = $key;
  $x++;
}

echo "<div id='updated-parks' class='ui-corners-all'>";
echo "<h3>Most Popular Articles</h3>";
echo "<ul>";
for ($i = 0; $i < 10; $i++) {
  $nodeInfo = node_load($result[$i]);
  echo "<li><a href='/" . drupal_get_path_alias("node/" . $result[$i]) . "'>" . $nodeInfo->title . "</a></li>";
}
echo "</ul>";
echo "</div>";

function getArticles() {
  $result = array();
  $query = db_query("SELECT nid FROM {article_count}");
  while ($row = $query->fetchAssoc()) {
    $result[$row["nid"]]++;
  }
  return $result;
}
?>