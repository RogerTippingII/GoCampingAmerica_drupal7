<?php
echo "The following database exports are available. To download, right click on the desired file and select 'Save As'.<br /><br />";
$files = glob('/var/www/vhosts/gocampingamerica.com/httpdocs/sites/default/files/dumps/*'); 

foreach ($files as $file) {
  $start = strrpos($file, "/") + 1;
  $filename = substr($file, $start, 13);
  if (substr($filename, -3, 3) == "csv") {
    echo "<a href='/sites/default/files/dumps/" . $filename . "'>" . $filename . "</a><br />";
  }
}
?>