<?php
echo $_SERVER["DOCUMENT_ROOT"] . "<br />";

//get the list of all files and delete the oldest if there are more than $maxFileNumber
$maxFileNumber = 10;
$dumpFiles = glob('/var/www/vhosts/gocampingamerica.com/httpdocs_drupal7/sites/default/files/dumps/*');

if(count($dumpFiles) >= $maxFileNumber){
  sort($dumpFiles);
  unlink($dumpFiles[0]);
}


$combinedFile = $_SERVER["DOCUMENT_ROOT"] . "/sites/default/files/dumps/" . date("Ymd", mktime()) . ".csv";
$deleteFile = $_SERVER["DOCUMENT_ROOT"] . "/sites/default/files/dumps/" . (date("Ymd", mktime()) - 7) . ".csv";

$files = glob('/var/www/vhosts/gocampingamerica.com/httpdocs_drupal7/sites/default/files/dumps/partials/*'); 

foreach ($files as $file) {
  $start = strrpos($file, "/") + 1;
  $filenames[] = trim(substr($file, $start, 13));
}

echo "file: " . $combinedFile . "<br />";
echo "toDelete: " . $deleteFile . "<br />";

$data = "";

foreach ($filenames as $filename) {
  $data .= file_get_contents("http://www.gocampingamerica.com/sites/default/files/dumps/partials/" . $filename);
}

if ($data) {
  file_put_contents($combinedFile, $data);
}

//unlink($deleteFile);

echo "Finished.";
?>