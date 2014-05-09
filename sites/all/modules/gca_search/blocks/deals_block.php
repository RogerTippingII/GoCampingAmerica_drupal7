<?php
if (isset($_REQUEST["p"]) && isset($_REQUEST["state"])) {

  if ($_REQUEST["p"] == "overview" && $_REQUEST["state"] == "California") {

    $deals = blockGetDeals(5829);

    if ($deals) {
      ?>

      <div id="assn-deal-block" class="ui-corners-all">
        <div class="assn-deal-header">SPECIAL DEALS</div>
        <div class="assn-deal-body">
          <?php
          foreach ($deals as $deal) {
            $dealInfo = blockDealInfo($deal);
            echo "<div class='assn-deal-indy-wrapper'><a href='/" . $dealInfo["alias"] . "'>" . $dealInfo["title"] . "</a></div>";
          }
          ?>
        </div>
      </div>

    <?php
    }
  }
}

function blockGetDeals($uid) {
  $query = db_query("SELECT DISTINCT nid FROM {node} WHERE type = 'deal' AND uid = %d ORDER BY vid DESC", $uid);
  while ($row = db_fetch_array($query)) {
    $tempResult[] = $row["nid"];
  }
  foreach ($tempResult as $dealNid) {
    $times = blockGetTimes($dealNid);
    $startTime = strtotime(str_replace("T", " ", $times["start"]));
    $endTime = strtotime(str_replace("T", " ", $times["end"]));
    $timeNow = mktime();
    if ($startTime < $timeNow && $endTime > $timeNow) {
      $result[] = $dealNid;
    }
  }
  return $result;
}

function blockGetTimes($nid) {
  $query = db_query("SELECT field_deal_start_value, field_deal_end_value FROM {content_type_deal} WHERE nid = %d ORDER BY vid DESC LIMIT 1" , $nid);
  while ($row = db_fetch_array($query)) {
    $result["start"] = $row["field_deal_start_value"];
    $result["end"] = $row["field_deal_end_value"];
  }
  return $result;
}

function blockDealInfo($nid) {
  $query = db_query("SELECT title FROM {node} WHERE nid = %d ORDER BY vid DESC LIMIT 1", $nid);
  while ($row = db_fetch_array($query)) {
    $result["alias"] = blockGetAlias($nid);
    $result["title"] = $row["title"];
  }
  return $result;
}

function blockGetAlias($nid) {
  $src = "node/" . $nid;
  $query = db_query("SELECT dst FROM {url_alias} WHERE src = '%s' LIMIT 1", $src);
  while ($row = db_fetch_array($query)) {
    $result = $row["dst"];
  }
  return $result;
}
?>