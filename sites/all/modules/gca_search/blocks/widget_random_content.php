<?php
$blocks = array("gca_widget_most_popular_articles", "gca_widget_featured_parks", "gca_widget_updated_posts");
$targetBlock = rand(0,2);

$block = module_invoke('gca_search', 'block_view', $blocks[$targetBlock]);

print $block['content'];
?>