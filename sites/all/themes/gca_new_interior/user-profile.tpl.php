<?php
// $Id: user-profile.tpl.php,v 1.2.2.2 2009/10/06 11:50:06 goba Exp $

/**
 * @file user-profile.tpl.php
 * Default theme implementation to present all user profile data.
 *
 * This template is used when viewing a registered member's profile page,
 * e.g., example.com/user/123. 123 being the users ID.
 *
 * By default, all user profile data is printed out with the $user_profile
 * variable. If there is a need to break it up you can use $profile instead.
 * It is keyed to the name of each category or other data attached to the
 * account. If it is a category it will contain all the profile items. By
 * default $profile['summary'] is provided which contains data on the user's
 * history. Other data can be included by modules. $profile['user_picture'] is
 * available by default showing the account picture.
 *
 * Also keep in mind that profile items and their categories can be defined by
 * site administrators. They are also available within $profile. For example,
 * if a site is configured with a category of "contact" with
 * fields for of addresses, phone numbers and other related info, then doing a
 * straight print of $profile['contact'] will output everything in the
 * category. This is useful for altering source order and adding custom
 * markup for the group.
 *
 * To check for all available data within $profile, use the code below.
 * @code
 *   print '<pre>'. check_plain(print_r($profile, 1)) .'</pre>';
 * @endcode
 *
 * Available variables:
 *   - $user_profile: All user profile data. Ready for print.
 *   - $profile: Keyed array of profile categories and their items or other data
 *     provided by modules.
 *
 * @see user-profile-category.tpl.php
 *   Where the html is handled for the group.
 * @see user-profile-item.tpl.php
 *   Where the html is handled for each item in the group.
 * @see template_preprocess_user_profile()
 */
?>
<div class="profile">
  <?php 
  global $user;
  echo "<h2>" . $user->profile_stateassn_name . "</h2><br />";
  //echo "<pre>";
  //print_r($user);
  //echo "</pre>";
  if ($user->roles[5] == "park owner") {
    $toReplace = array("/users/", "/user/");
    //$profileID = str_replace($toReplace, "", $_SERVER['REQUEST_URI']); 
    //$uid = getParkUID($user->name);
	//$uid = getParkUID($profileID);
    $park = getParkDetails($user->uid);
    if ($park) { 
	  // Change tabs on profile pages
	?>
	<script type="text/javascript">
	var $gca = jQuery.noConflict();
    $gca(document).ready(function() {
	  $gca(".primary .view").hide();
      $gca(".primary .edit").html("<a href='/user/" + <?php echo $uid; ?> + "/edit'>Account Information</a></li><li class='view-park'><a href='/<?php echo $park["alias"]; ?>'>View Park</a></li><li class='edit-park'><a href='/node/<?php echo $park["nid"]; ?>/edit'>Edit Park</a>");
    });
	</script>
    <div id="park_profile_edit_link">
      <h3><?php echo $park["title"]; ?></h3>
	  <a href="/<?php echo $park["alias"]; ?>">View Park</a> | <a href="/node/<?php echo $park["nid"]; ?>/edit">Edit Park</a><br />
    </div>
  <? } else {
      echo "No park found for this user.";
	}
  } else {
    if (!$user->roles[7]) {
      echo "No park is owned by this user.";
    }
  }
  if ($user->roles[7] || ($user->roles[4] && isset($_REQUEST["assoc"]))) {
    if ($user->roles[4] && isset($_REQUEST["assoc"])) {
      $assoc = $_REQUEST["assoc"];
      $uid = getParkUID($assoc);
      $camps = getStateParks($assoc, $uid);
	} else {
      $camps = getStateParks($user->profile_stateassnID, $user->uid);
	}
	//echo "<h2>State Association Parks</h2>";
	echo "<table id='state-table' class='tablesorter'>";
	echo "<thead>";
	echo "<tr><th>Park Name</th><th>State</th><th>Status</th><th></th></tr>\n";
	echo "</thead>\n<tbody>";
	$campTitles = array();
	foreach ($camps as $camp) {
	  if (!in_array($camp["title"], $campTitles)) {
	    echo "<tr><td><a href='/" . getParkAlias($camp["nid"]) . "'>" . $camp["title"] . "</a></td><td>" . $camp["province"] . "</td><td>" . $camp["status"] . "</td><td><a href='/node/" . $camp["nid"] . "/edit'>Edit</a></td></tr>\n";
		$campTitles[] = $camp["title"];
	  }
	}
	echo "</tbody>";
	echo "</table>";
	//echo "<pre>";
	//print_r($user);
	//echo "</pre>";
  }
  ?>
  <pre>
  </pre>
  <?php //print $user_profile; ?>
</div>

<?php
function getStateParks($said, $suid) {
  //echo $said . "<br />";
  $query = db_query("SELECT n.nid, n.title, l.province FROM {node} n, field_data_field_camp_state_assnid c1, field_data_field_camp_stateassn_user c2, {location} l, {location_instance} li WHERE n.nid = c1.entity_id AND n.nid = c2.entity_id AND l.lid = li.lid AND li.nid = n.nid AND n.status = 1 AND (c1.field_camp_state_assnid_value = :said OR c2.field_camp_stateassn_user_uid = :suid) ORDER BY n.title ASC", array(':said' => $said, ':suid' => $suid));
  $x = 0;
  while ($row = $query->fetchAssoc()) {

    $result[$x]["nid"] = $row["nid"];
	  $result[$x]["title"] = $row["title"];
	  $result[$x]["province"] = $row["province"];
	  $result[$x]["status"] = getStatus($row["nid"]);
	  $x++;

  }
  return $result;
}

function getStatus($nid) {
  $query = db_query("SELECT field_camp_status_value FROM field_data_field_camp_status WHERE entity_id = :nid LIMIT 1", array(':nid' => $nid));
  while ($row = $query->fetchAssoc()) {
    $result = $row["field_camp_status_value"];
  }
  if ($result) {
    return $result;
  }
  return;
}


function getParkDetails($uid) {
  $query = db_query("SELECT title, nid FROM {node} WHERE uid = :uid AND type = 'camp' LIMIT 1", array(':uid' => $uid));
  while ($row = db_fetch_array($query)) {
    $result["nid"] = $row["nid"];
	$result["title"] = $row["title"];
	$result["alias"] = getParkAlias($row["nid"]);
  }
  return $result;
}

function getParkAlias($nid) {
  $src = "node/" . $nid;
  $query = db_query("SELECT alias FROM {url_alias} WHERE source = :src LIMIT 1", array(':src' => $src));
  while ($row = $query->fetchAssoc()) {
    $result = $row["alias"];
  }
  return $result;
}

function getParkUID($profileID) {
  $query = db_query("SELECT uid FROM {users} WHERE name = :profileID LIMIT 1", array(':profileID' => $profileID));
  while ($row = $query->fetchAssoc()) {
    $uid = $row["uid"];
  }
  return $uid;
}
?>
