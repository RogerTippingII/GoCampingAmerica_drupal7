<?php

/* ********************************************** */
/* This script displays deals for the  */
/* mobile site.                                   */
/* ********************************************** */

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

if (isset($_REQUEST["c"])) {
  $results = getItems($_REQUEST["c"]);
  $feed["title"] = getFeedTitle($_REQUEST["c"]);
  $feed["link"] = "http://www.gocampingamerica.com/blog/" . strtolower(str_replace(" ", "-", $feed["title"]));
} else {
  $results = getItems("all");
  $feed["title"] = "GoCampingAmerica.com Blog";
  $feed["link"] = "http://www.gocampingamerica.com/blog";
}

$x = 0;
foreach ($results as $result) {
  $items[$x] = node_load($result);
  $x++;
}

function getFeedTitle($cat) {
  $query = db_query("SELECT name FROM {term_data} WHERE tid = %d LIMIT 1", $cat);
  while ($row = db_fetch_array($query)) {
    $result = $row["name"];
  }
  if (isset($result)) {
    return $result;
  }
  return;
}

function getItems($cat) {
  $timenow = mktime();
  if ($cat == "all") {
    $query = db_query("SELECT nid FROM {node} WHERE type = 'blog_post' AND created < %d ORDER BY created DESC LIMIT 15", $timenow);
  } else {
    $query = db_query("SELECT n.nid FROM {node} n, {content_field_blog_category} c WHERE n.nid = c.nid AND n.type = 'blog_post' AND n.created < %d AND c.field_blog_category_value = %d ORDER BY created DESC LIMIT 15", $timenow, $cat);
  }
  while ($row = db_fetch_array($query)) {
    $result[] = $row["nid"];
  }
  if (isset($result)) {
    return $result;
  }
  return;
}
echo '<?xml version="1.0" encoding="utf-8" ?><rss version="2.0" xml:base="http://www.gocampingamerica.com/blog/explore-america" xmlns:media="http://search.yahoo.com/mrss/" xmlns:dc="http://purl.org/dc/elements/1.1/">';
echo '<channel>'; ?>
    <title><?php echo $feed["title"]; ?></title>
    <link><?php echo $feed["link"]; ?></link>
    <description></description>
    <language>en</language>
	<?php foreach ($items as $item) { ?>
    <item>
      <title><?php echo $item->title; ?></title>
      <link><?php echo "http://www.gocampingamerica.com/" . $item->path; ?></link>
      <description><?php echo substr(strip_tags($item->body), 0, 200) . " ..."; ?></description>
	  <pubDate><?php echo date("r", $item->created); ?></pubDate>
	  <dc:creator></dc:creator>
	</item>
	<?php } ?>
  </channel>
</rss>
  