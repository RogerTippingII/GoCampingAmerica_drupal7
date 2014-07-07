<?php
global $user;

$pageNid = getNID($user->uid);
$dest = "/node/" . $pageNid . "/edit";
?>
  <script type="text/javascript">
    window.location = "<?php echo $dest; ?>";
  </script>

<?php

function getNID($uid) {
  $query = db_query("SELECT nid FROM {node} WHERE type = 'camp' AND uid = :uid ORDER BY vid DESC LIMIT 1", array(':uid' => $uid));
  while ($row = $query->fetchAssoc()) {
    $result = $row["nid"];
  }
  return $result;
}

?>