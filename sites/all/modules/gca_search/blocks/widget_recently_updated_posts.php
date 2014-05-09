<div id="updated-parks" class="ui-corners-all">
  <h3>Recently Updated Posts</h3>
<?php
$posts = getPosts();

if ($posts) {
  shuffle($posts);
  echo "<ul>";
  for ($i = 0; $i < 5; $i++) {
    $nodeInfo = node_load($posts[$i]);
    echo "<li><a href='/" . drupal_get_path_alias("node/" . $posts[$i]) . "' onClick=\"_gaq.push(['_trackEvent', 'Widgets', 'New/Updated Content Widget', 'Clicked',, false]);\" >" . $nodeInfo->title. "</a></li>";
  }
  echo "</ul>";
}
echo "</div>";

function getPosts() {
  $query = db_query("SELECT nid FROM {node} WHERE type = 'blog_post' AND status = 1 ORDER BY changed DESC LIMIT 30");
  while ($row = $query->fetchAssoc()) {
    $result[] = $row["nid"];
  }
  if ($result) {
    return $result;
  }
  return;
}
?>