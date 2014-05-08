<?php

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

global $user;

if ($user->roles[4] == "dev") {

?>

<html>
  <head>
    <title>Dev Tools</title>
    <style>
	  input {
		padding:10px;
		border:1px solid #ccc;
		border-radius:6px;
		float:left;
		margin-right:10px;
	  }
	  body {
	    font-family:Arial, Helvetica, sans-serif;
	  }
	</style>
  </head>
  <body>
  <?php
  showForm();
  echo "<br clear='all' />";
  if (isset($_REQUEST["action"])) {
    if ($_REQUEST["action"] == "node" && isset($_REQUEST["nid"])) {
      echo "<pre>";
      print_r(node_load($_REQUEST["nid"]));
      echo "</pre>";
    }
    if ($_REQUEST["action"] == "user" && isset($_REQUEST["uid"])) {
      echo "<pre>";
      print_r(user_load($_REQUEST["uid"]));
      echo "</pre>";
    }
    if ($_REQUEST["action"] == "username" && isset($_REQUEST["name"])) {
      echo "Username: " . $_REQUEST["name"] . "<br />";
      $uid = getUID($_REQUEST["name"]);
      echo "UID: " . $uid . "<br />";
      echo "<pre>";
      print_r(user_load($uid));
      echo "</pre>";
    }
  }
  ?>
  
  </body>
</html>

<?php
} else {
  echo "Unauthorized.";
} // Role check

function getUID($name) {
  $query = db_query("SELECT uid FROM {users} WHERE name = '%s' LIMIT 1", $name);
  while ($row = db_fetch_array($query)) {
    $result = $row["uid"];
  }
  if (isset($result)) {
    return $result;
  }
  return FALSE;
}

function showForm() { ?>

<form>
  <input type="hidden" name="action" value="node" />
  <input type="text" name="nid" value="" placeholder="Enter nid" />
</form>

<form>
  <input type="hidden" name="action" value="user" />
  <input type="text" name="uid" value="" placeholder="Enter uid" />
</form>

<form>
  <input type="hidden" name="action" value="username" />
  <input type="text" name="name" value="" placeholder="Enter username" />
</form>
<?php }
?>