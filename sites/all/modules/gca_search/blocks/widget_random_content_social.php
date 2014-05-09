<?php
$blocks = array(78, 79);
$targetBlock = rand(0,1);
$block = module_invoke('block', 'block_view', $blocks[$targetBlock]);
print $block['content'];
?>