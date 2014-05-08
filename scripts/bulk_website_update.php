/**
 * Created by patricepaquette on 12/5/2013.
 */

<?php
chdir($_SERVER['DOCUMENT_ROOT']);
global $base_url;
$base_url = 'http://'.$_SERVER['HTTP_HOST'];
include_once './includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

$rs = db_query("SELECT * FROM park_import");

while($row = db_fetch_array($rs)){


};


// Terminate if an error occured during user_save().
?>