<?php
  $nodeInfo = node_load($node->nid);
//  kprint_r($node);
//  kprint_r($nodeInfo);
  $tier = $nodeInfo->field_park_tier[LANGUAGE_NONE][0]["value"];
//  echo "<pre>";
//  dprint_r($node);
//  dprint_r($nodeInfo);
//  dprint_r($user);
//  echo "</pre>";
  if ($nodeInfo->uid == $user->uid) { ?>
  <script type="text/javascript">
    var $gca = jQuery.noConflict();
    $gca(document).ready(function() {
	  $gca("#body-content .primary .edit").html("<a href='/node/<?php echo $node->nid; ?>/edit'>Edit</a></li><li class='account-information'><a href='/users/<?php echo $user->name; ?>'>Back to Log In Area</a>");
	});
  </script>
  <?php }

  //$socialInfo = getFBT($node->nid);
  $path = url('node/' . $node->nid);
  $stateTemp = explode("/", $path);
  $state = ucwords(str_replace("-", " ", $stateTemp[2]));
  $stateAbbrev = getAbbreviation($state);

//  $tempFeatures = json_decode(json_encode($nodeInfo->taxonomy), true);
  $validCats = array(6, 3, 2, 5, 11, 1, 17, 18);

  $rs = db_query("SELECT ti.tid FROM taxonomy_index ti WHERE ti.nid = :nid", array(":nid" => $node->nid));
  $node->taxonomy = array();
  while($row = $rs->fetchAssoc()){
    $term = taxonomy_term_load($row["tid"]);
    $node->taxonomy[$term->tid] = $term;
  }

  $rs = db_query("SELECT ti.tid FROM taxonomy_index ti
                  JOIN taxonomy_term_data ttd ON ti.tid = ttd.tid
                  WHERE ti.nid = :nid AND ttd.vid IN ". '('. implode(',', $validCats) . ')',
    array(":nid" => $node->nid));
  $features = array();

  while($row = $rs->fetchAssoc()){
    $features[] = taxonomy_term_load($row["tid"]);
  }


//  foreach ($tempFeatures as $key => $value) {
//    if (in_array($value["vid"], $validCats)) {
//	  $features[$key] = $value;
//	}
//  }

  if ($features) {
    foreach ($features as $cat) {
      $feature[$cat->vid][] = $cat->name;
    }
    foreach ($feature as $key=>$value) {
      $featureCats[] = $key;
    }
    $featureCats = array_unique($featureCats);
    foreach ($featureCats as $cat) {
      $temp = taxonomy_vocabulary_load($cat);
      $vocabs[] = $temp->name;
    }
    $feature = array_merge($feature);
  }
  // Query for state association promos the park has opted in for
  if ($nodeInfo->field_park_state_assn_optin[0]["value"] == "on" && $nodeInfo->field_camp_state_assnid[0]["value"]) {
    $assnUID = getUID($nodeInfo->field_camp_state_assnid[0]["value"]);
	$timeNow = mktime();
	//echo "<div style='font-size:0.7em;color:#CCC;'>";
	//echo $timeNow . " | ";
    $query = db_query("SELECT DISTINCT n.nid FROM {node} n WHERE n.type = 'deal' AND n.uid = :assnUID", array("assnUID" => $assnUID));

	while ($row = $query->fetchAssoc()) {
	  //echo $row["nid"] . " ";
	  $couponVID = getNewestVID($row["nid"]);
	  //echo "v" . $couponVID . " ";
	  $query2 = db_query("SELECT field_deal_start_value, field_deal_end_value FROM {content_type_deal} WHERE nid = :nid AND vid = :vid", array('nid' => $row["nid"], 'vid' => $couponVID));
	  while ($row2 = $query2->fetchAssoc()) {
	    if (strtotime($row2["field_deal_start_value"]) < $timeNow && strtotime($row2["field_deal_end_value"]) > $timeNow) {
		  //echo " match ";
		  $coupon[] = $row["nid"];
		}
	  }
	}
	//echo "</div>";
  }


  // Query individual park promotions and events to determine whether to display the tabs
  $query = db_query("SELECT nid FROM {node} WHERE type = 'deal' AND uid = :uid", array('uid' => $node->uid));
  while ($row = $query->fetchObject()) {
    $coupon[] = $row->nid;
  }
  $query = db_query("SELECT n.nid FROM {node} n, {content_type_events} cte WHERE n.nid = cte.nid AND n.type = 'events' AND n.uid = :uid", array('uid' => $node->uid));
  while ($row = $query->fetchObject()) {
    $eventInfo[] = node_load($row->nid);
  }

  $activeTab = $_REQUEST["tab"];
  if (!$activeTab || $activeTab == "") {
    $activeTab = "info";
  }
?>
<div id="dark-overlay" class="hide"></div>
<?php if (!$page): ?>
  <article id="node-<?php print $node->nid; ?>" class="node<?php if ($sticky) { print ' sticky'; } ?><?php if (!$status) { print ' node-unpublished'; } ?> clearfix">
<?php endif; ?>

  <?php if ($picture || $submitted || !$page): ?>
    <?php if (!$page): ?>
      <header>
	<?php endif; ?>

      <?php print $picture ?>

	  <?php if (!$page): ?>
        <h2><a href="<?php print $node_url ?>" title="<?php print $title ?>"><?php print $title ?></a></h2>
      <?php endif; ?>

	  <?php if ($submitted): ?>
        <span class="submitted"><?php print $submitted; ?></span>
      <?php endif; ?>

    <?php if (!$page): ?>
      </header>
	<?php endif; ?>
  <?php endif;?>

  <div class="content">
    <div id="breadcrumb"><a href="/findpark">Parks</a> &gt; <a href="/findpark?p=overview&id=<?php echo $stateAbbrev; ?>&state=<?php echo $state; ?>"><?php echo $state; ?></a> &gt; <?php echo $node->title; ?></div><div id="search-again"><a href="/findpark"><img src="/sites/all/themes/gca_new_interior/images/search_again.gif" width="89" height="35" alt="Search Again" /></a></div><br clear="all" />
    <div id="park-tabs">
      <ul>
        <li <?php if ($activeTab == "info") { echo 'class="selected" '; } ?> rel="#park-info" tab="info">Info</li>
		<?php
	    if ($nodeInfo->field_camp_guestreview_id[LANGUAGE_NONE][0]["value"] && $nodeInfo->field_park_guest_reviews_optin[LANGUAGE_NONE][0]["value"] == 1) { ?>
        <li <?php if ($activeTab == "reviews") { echo 'class="selected" '; } ?> rel="#park-reviews" tab="reviews">Reviews</li>
        <?php } ?>
		<?php if (count($coupon)) { ?>
        <li <?php if ($activeTab == "promotions") { echo 'class="selected" '; } ?> rel="#park-promotions" tab="promotions">Promotions</li>
        <?php } ?>
        <?php if (count($eventInfo)) { ?>
        <li <?php if ($activeTab == "events") { echo 'class="selected" '; } ?> rel="#park-events" tab="events">Events</li>
        <?php } ?>
        <?php if (count($node->field_camp_video_url) && ($tier > 2 || $nodeInfo->field_camp_swamp_city[LANGUAGE_NONE][0]["value"] == "on")) { ?>
        <li <?php if ($activeTab == "videos") { echo 'class="selected" '; } ?> rel="#park-videos" tab="videos">Videos</li>
        <?php } ?>
        <?php if ($node->field_camp_slideshow[LANGUAGE_NONE][0][uri]) { ?>
        <li <?php if ($activeTab == "photos") { echo 'class="selected" '; } ?> rel="#park-photos" tab="photos">Photos</li>
        <?php } ?>
        <?php if ($node->field_camp_email[LANGUAGE_NONE][0]['email']) { ?>
        <li <?php if ($activeTab == "contact") { echo 'class="selected" '; } ?> rel="#park-contact" tab="contact">Contact Us</li>
        <?php } ?>
      </ul>
    </div>
    <div id="park-tabs-content">
      <div id="park-info" class="park-tab-content<?php if ($activeTab != 'info') { echo ' hide'; } ?>">
        <div id="park-info-left">
          <div id="park-info-name" class="ui-corners-all">
            <h3><?php echo $node->title; ?></h3>
            <div id="park-info-address">
              <?php echo $node->field_location[LANGUAGE_NONE][0][street] ."<br />\n" .
              $node->field_location[LANGUAGE_NONE][0][city] . ", " . $node->field_location[LANGUAGE_NONE][0][province] . " " . $node->field_location[LANGUAGE_NONE][0][postal_code] . "<br />\n"; ?>

            </div>
            <?php if ($node->field_camp_phone[LANGUAGE_NONE][0]['number']) { ?>
            <div id="park-phone">
              <?php if (strlen($node->field_camp_phone[LANGUAGE_NONE][0]['number']) == 10) {
                $phonenumber = "(" . substr($node->field_camp_phone[LANGUAGE_NONE][0]['number'], 0, 3) . ") " . substr($node->field_camp_phone[LANGUAGE_NONE][0]['number'], 3, 3) . "-" . substr($node->field_camp_phone[LANGUAGE_NONE][0]['number'], 6, 4);
              } else {
                $phonenumber = $node->field_camp_phone[LANGUAGE_NONE][0]['number'];
              } ?>
              <?php echo $phonenumber; ?>
            </div>
            <?php } ?>
			<?php if ($node->field_camp_tollfree_phone[LANGUAGE_NONE][0]['number']) { ?>
            <div id="park-phone">
              <?php if (strlen($node->field_camp_tollfree_phone[LANGUAGE_NONE][0]["number"]) == 10) {
                $phonenumber = "(" . substr($node->field_camp_tollfree_phone[LANGUAGE_NONE][0]['number'], 0, 3) . ") " . substr($node->field_camp_tollfree_phone[LANGUAGE_NONE][0]['number'], 3, 3) . "-" . substr($node->field_camp_tollfree_phone[LANGUAGE_NONE][0]['number'], 6, 4);
              } else {
                $phonenumber = $node->field_camp_tollfree_phone[LANGUAGE_NONE][0]['number'];
              } ?>
              <?php echo $phonenumber; ?>
            </div>
            <?php } ?>
            <?php if ($node->field_camp_email[LANGUAGE_NONE][0]['email']) { ?>
            <div id="park-email">
			  <a href="?tab=contact">Contact Us</a>
            </div>
            <?php } ?>
            <?php if ($node->field_camp_website[LANGUAGE_NONE][0]['url']): ?>
            <div id="park-website_url">
            	<a href="<?php print $node->field_camp_website[LANGUAGE_NONE][0]['url'] ?>" target="new">Visit Our Website</a>
            </div>
            <?php endif; ?>
			<?php if ($node->field_camp_reservation_website[LANGUAGE_NONE][0]["url"]) {
			  echo "<div id='park-reservation'><a href='" . $node->field_camp_reservation_website[LANGUAGE_NONE][0]["url"] . "'>Make Reservation</a></div>";
			} ?>
			<?php if ($node->field_park_facebook[LANGUAGE_NONE][0]["url"] || $node->field_park_twitter[LANGUAGE_NONE][0]["url"]) { ?>
			<div id="park-social-media">
			  <?php if ($node->field_park_facebook[LANGUAGE_NONE][0]["url"]) { ?>
			  <a href="<?php echo $node->field_park_facebook[LANGUAGE_NONE][0]["url"]; ?>" target="new"><img src="/sites/all/themes/gca_new_interior/images/icon_facebook_sm.gif" alt="Visit us on Facebook" style="margin:5px 5px 0 0;float:left;" /></a>
			  <?php } ?>
			  <?php if ($node->field_park_twitter[LANGUAGE_NONE][0]["url"]) { ?>
			  <a href="<?php echo $node->field_park_twitter[LANGUAGE_NONE][0]["url"]; ?>" target="new"><img src="/sites/all/themes/gca_new_interior/images/icon_twitter_sm.gif" alt="Visit us on Twitter" style="margin-top:5px;float:left;" /></a>
			  <?php } ?>
			  <br clear="all" />
			</div>
			<?php }
			if ($node->taxonomy[2383]->name == "Open Year Round") {
			  echo "Open Year Round<br />";
			} else {
			  if ($node->field_park_date_open[LANGUAGE_NONE][0]["value"] && $node->field_park_date_open_day[LANGUAGE_NONE][0]["value"]) {
			    echo "<b>Opens:</b> " . $node->field_park_date_open[LANGUAGE_NONE][0]["value"] . " " . $node->field_park_date_open_day[LANGUAGE_NONE][0]["value"] . "<br />";
			  }
			  if ($node->field_park_date_closed_month[LANGUAGE_NONE][0]["value"] && $node->field_park_date_closed_day[0]["value"]) {
			    echo "<b>Closes:</b> " . $node->field_park_date_closed_month[LANGUAGE_NONE][0]["value"] . " " . $node->field_park_date_closed_day[LANGUAGE_NONE][0]["value"] . "<br />";
		      }
			} ?>
            <div id="park-info-google-link">
              <?php $googleString = str_replace(" ", "+", $node->field_location[LANGUAGE_NONE][0][street] . ", " . $node->field_location[0][city] . " " . $node->field_location[LANGUAGE_NONE][0][province]); ?>
              See map: <a href="http://www.google.com/maps?q=<?php echo $googleString; ?>&t=h&z=16" target="_blank">Google Maps</a><br />
            </div>
          </div> <!-- /#park-info-name -->
          <div id="park-info-map" class="ui-corners-all">
            <a href="http://www.google.com/maps?q=<?php echo $googleString; ?>&t=h&z=16" target="_blank"><img src="http://maps.googleapis.com/maps/api/staticmap?center=<?php echo $node->field_location[LANGUAGE_NONE][0][latitude] ?>,<?php echo $node->field_location[LANGUAGE_NONE][0][longitude] ?>&zoom=11&size=200x200&
markers=color:blue%7Clabel:%7C<?php echo $node->field_location[LANGUAGE_NONE][0][latitude] ?>,<?php echo $node->field_location[LANGUAGE_NONE][0][longitude] ?>&sensor=false" /></a>
          </div> <!-- /#park-info-map -->
        </div> <!-- /#park-info-left -->
        <div id="park-info-right">
          <div id="park-info-body">
		    <?php if ($nodeInfo->field_planit_green[LANGUAGE_NONE][0]["value"] == "yes") { ?>
		    <div id="planit-green"><a href="/plan-it-green"><img src="/sites/all/themes/gca_new_interior/images/ARVC-Plan-ItGreen-Badge.gif" width="90" height="33" alt="Plan-It Green Friendly Park" /></a></div>
			<?php }
            echo "<div id='park-description'>" . $node->field_park_description[LANGUAGE_NONE][0]["safe_value"] . "</div>";
			if ($node->field_camp_directions[LANGUAGE_NONE][0]["safe_value"]) {
			  echo "<div id='park-directions'><b>Directions:</b> " . $node->field_camp_directions[LANGUAGE_NONE][0]["safe_value"] . "</div>";
			}
			if ($node->field_camp_rules[LANGUAGE_NONE][0]["safe_value"]) {
			  echo "<div id='park-rules'><b>Park Rules:</b> " . $node->field_camp_rules[LANGUAGE_NONE][0]["safe_value"] . "</div>";
			}
			if ($node->field_camp_local_interest[LANGUAGE_NONE][0]["safe_value"]) {
			  echo "<div id='park-local-interest'><b>Local Interest:</b> " . $node->field_camp_local_interest[LANGUAGE_NONE][0]["safe_value"] . "</div>";
			}
			?>
          </div> <!-- /#park-info-body -->
		  <!-- <div id="park-info-tabs">
            <ul>
    	      <li class="active" rel="#park-info-amenities">Park Info</li>
			  <li rel="#park-info-state">State Overview</li>
		    </ul>
			<br clear="all" />
		  </div> -->
		  <!-- /#park-info-tabs -->
          <div id="park-info-amenities">
            <h2>Features/Specifications</h2>
            <div class="column1">
              <?php
              $limit = ceil(count($vocabs)/2);
              for ($i = 0; $i < $limit; $i++) {
                echo "<div class='title'>" . str_replace("Park" , "", $vocabs[$i]) . "</div>";
                if ($vocabs[$i] != "Park Services") {
                  foreach ($feature[$i] as $item) {
                    echo "<li>" . $item . "</li>";
                  }
				} else {
				  foreach ($feature[$i] as $item) {
				    echo "<li>" . $item . "</li>";
				  }
				}
              }
              ?>
            </div>
            <div class="column2">
              <?php
              for ($i = $limit; $i < count($vocabs); $i++) {
                echo "<div class='title'>" . str_replace("Park" , "", $vocabs[$i]) . "</div>";
				if ($vocabs[$i] != "Park Services") {
                  foreach ($feature[$i] as $item) {
                    echo "<li>" . $item . "</li>";
                  }
				} else {
				  $r = 0;
				  foreach ($feature[$i] as $item) {
				    echo "<li>" . $item . "</li>";
				    /*
				    echo "<li style='padding:3px;";
					if ($r == 1) {
					  echo "background-color:#EFEFEF;";
					}
					echo "'><table style='width:200px;margin-bottom:0;'><tr><td>" . $item . "</td><td style='width:70px;text-align:right;'>" . getQty($nodeInfo, $item) . "</td></tr></table></li>";
					if ($r == 0) {
					  $r = 1;
					} else {
					  $r = 0;
					}
					*/
				  }
				}
              }
              ?>
            </div>
            <br clear="all" />
          </div> <!-- /#park-info-amenities -->
		  <div id="park-info-state">
		    <h3>State Information</h3>
            <?php
              $id = $node->field_location[LANGUAGE_NONE][0]["province"];
              $state = $node->field_location[LANGUAGE_NONE][0]["province_name"];
			  $resources = getResources($state);


              $provinces = array("Alberta", "British Columbia", "Manitoba", "New Brunswick", "Newfoundland and Labrador", "Northwest Territories", "Nova Scotia", "Nunavut", "Ontario", "Prince Edward Island", "Quebec", "Saskatchewan", "Yukon");
              if (in_array($state, $provinces)) {
                $overview = "Province Overview";
              } else {
                $overview = "State Overview";
              }

            ?>
            <ul id="states-navigation">
              <li><a href="/findpark?p=overview&id=<?php echo $id ?>&state=<?php echo $state ?>" id="nav-overview"><?php echo $overview; ?></a></li>
              <!-- <li><a href="" id="nav-calendar">Events Calendar</a></li> -->
              <li><a href="/findpark?p=attractions&id=<?php echo $id ?>&state=<?php echo $state ?>" id="nav-attractions">Tourist Attractions</a></li>
              <!-- <li><a href="/findpark?p=overview&id=<?php echo $id ?>&state=<?php echo $state ?>" id="nav-outdoors">Outdoors Activities</a></li> -->
              <li><a href="/findpark?p=rules&id=<?php echo $id ?>&state=<?php echo $state ?>" id="nav-rules">Rules of the Road</a></li>
              <!-- <li><a href="/findpark?p=links&id=<?php echo $id ?>&state=<?php echo $state ?>" id="nav-links">Useful Links</a></li> -->
            </ul>
            <?php
			if ($resources[0][title] != "") { ?>
            <div id="states-resources">Additional Resources</div>
            <ul id="additional-resources">
			<?php
            foreach ($resources as $resource) {
              echo "<li><a href='" . $resource[url] . "'>" . $resource[title] . "</a></li>";
            }
            ?></ul>
            <?php }

			?>
		  </div> <!-- /#park-info-state -->
          <br clear="all" />
		  <?php if ($nodeInfo->field_planit_green[LANGUAGE_NONE][1]["value"] == "no") { ?>
		  <div id="planit-info-text">This arvc member has taken the Plan-It Green Pledge to do its best to enhance green practices and awareness in their park.</div>
		  <?php } ?>
        </div> <!-- /#park-info-right -->
        <br clear="all" />
      </div> <!-- /#park-info -->
	  <?php
	  if ($nodeInfo->field_park_guest_reviews_optin[LANGUAGE_NONE][0]["value"] == 1) { ?>
      <div id="park-reviews" class="park-tab-content<?php if ($activeTab != 'reviews') { echo ' hide'; } ?>">
	    <style type="text/css">
        ul#review-tabs {
          margin:0;
        padding:0;
        //border-bottom:1px solid #ccc;
        height:25px;
        }
        #review-tabs li {
          float:left;
        padding:5px 15px;
        margin-right:5px;
        border-top: 1px solid #ccc;
        border-right: 1px solid #ccc;
        border-left: 1px solid #ccc;
        background-color:#CCC;
        text-align:center;
        font-weight:bold;
        cursor:pointer;
        list-style:none;
        }
        #review-tabs li.active {
          color:#FFF;
        background-color:#7DB02A;
        }
		  </style>
	    <ul id="review-tabs">
        <li rel="#recent-reviews" class="active ui-corners-all">Recent Reviews</li>
        <li rel=".review-park" class="ui-corners-all">Submit Your Review</li>
		  </ul><br clear="all" />
        <!-- <b><a href="#reviewPark" class="ui-corners-all" style="background:#7DB02A;color:#FFF;text-align:center;padding:5px;width:250px;">Review this park</a></b><br /><br /> -->
		<!-- Recent Reviews -->
      <div id="recent-reviews" class="active">
        <h2>Recent Reviews</h2>
        <iframe id="MostRecentReviewsSinglePropertyIFrame" frameborder="0" scrolling="no" src="http://travel.guestrated.com/Widget/Pages/MostRecentReviewsSingleProperty.aspx?custtypeid=8&amp;portalid=518&amp;customerid=<?php echo $node->field_camp_guestreview_id[LANGUAGE_NONE][0][value]; ?>" width="800px" height="1600px" allowtransparency="true"></iframe>
      </div> <!-- /recent-reviews -->

        <div class="review-park" style="display:none;">
		  <h2>Review This Park</h2>
		  <!-- Review Park Form -->
          <IFRAME id="ctl00_Main_myFrame" width="600" height="1400" scrolling="no" frameborder="0" src="http://survey.guestrated.com/TakeSurvey.aspx?SurveyID=m2KH552&amp;customerID=<?php echo $node->field_camp_guestreview_id[LANGUAGE_NONE][0][value]; ?>&amp;portalID=61"></IFRAME>
		</div> <!-- /review-park -->

        <br clear="all" />
      </div> <!-- /#park-reviews -->
	  <?php } ?>
      <div id="park-promotions" class="park-tab-content<?php if ($activeTab != 'promotions') { echo ' hide'; } ?>">
        <?php
        if (count($coupon)) {
          foreach ($coupon as $promo) {
            $couponInfo = node_load($promo); ?>
            <div class="couponCode">
  <div class="field field-type-filefield field-field-image couponImage">
    <div class="field-items">
      <div class="field-item"><img src="/<?php echo $couponInfo->field_deal_image[LANGUAGE_NONE][0]['filepath'] ?>" alt="" class="imagecache imagecache-coupon_logo imagecache-default imagecache-coupon_logo_default" width="153" height="193" /></div>
    </div>
  </div>
  <div class="couponData">
    <div class="couponTitle"><?php echo $couponInfo->title ?></div>
    <div class="couponDtDescrWrap">
      <div class="couponDescr" id="couponDescr">
        <h2>Rules &amp; Regulations</h2>
        <?php echo $couponInfo->field_deal_description[LANGUAGE_NONE][0]['value']; ?>
	  </div>
      <div class="field field-type-date field-field-startdate couponDate">
        <div class="field-item">
		Expires<br />
		<?php echo date("n/j/Y", strtotime($couponInfo->field_deal_end[LANGUAGE_NONE][0]['value'])); ?>
		</div>
      </div>
    </div>
  </div>
</div> <?php
		  }
        } else {
          echo "There are no current promotions for this park.";
        }
        ?>
        <br clear="all" />
      </div> <!-- /#park-promotions -->
      <div id="park-events" class="park-tab-content<?php if ($activeTab != 'events') { echo ' hide'; } ?>">
        <?php
		if ($eventInfo) {
        foreach ($eventInfo as $event) { ?>
          <div class="event-wrapper">
            <div class="event-title"><?php echo $event->title ?></div>
            <div class="event-date"><?php echo date("g:i a D, F j, Y", strtotime($event->field_startdate[0][value])) . " - " . date("g:i a D, F j, Y", strtotime($event->field_startdate[0][value2])) ?></div>
            <div class="event-description"><?php echo $event->field_eventdesp[0][value] ?></div>
            <br clear="all" />
          </div>
        <?php }
        }
		?>

        <br clear="all" />
      </div> <!-- /#park-events -->
      <div id="park-videos" class="park-tab-content<?php if ($activeTab != 'videos') { echo ' hide'; } ?>">
        <script type="text/javascript">
          $gca(document).ready(function() {
            $gca(".park-video-playlist-item").click(function() {
              $gca(".park-video-player-title").html($gca(this).attr("title"));
              var embedPath = "http://www.youtube.com/embed/" + $gca(this).attr("rel");
              $gca(".videopage-iframe").attr("src", embedPath);
            });
          });
        </script>
        <?php
        $videos = $nodeInfo->field_camp_video_url;
		//print_r($videos);
        if ($videos[LANGUAGE_NONE][0]["url"] != "") {
		  //echo "Called.<br />";
		  $toReplace = array("http://www.youtube.com/watch?v=", "https://www.youtube.com/watch?v=");
		  $videos[LANGUAGE_NONE][0]["id"] = str_replace($toReplace, "", $videos[LANGUAGE_NONE][0]["url"]);
		  //echo $videos[0]["id"] . "<br />";
		  if (strpos($videos[LANGUAGE_NONE][0]["id"], "&") > 0) {
		    $videos[LANGUAGE_NONE][0]["id"] = substr($videos[LANGUAGE_NONE][0]["id"], 0, strpos($videos[LANGUAGE_NONE][0]["id"], "&"));
          }
        }
        if ($videos[LANGUAGE_NONE][0]["url"] != "") {
        ?>
        <div id="park-video-wrapper" class="ui-corners-all">
          <div class='park-video-player'>
            <div class='park-video-player-title'><?php echo $videos[LANGUAGE_NONE][0]["title"] ?></div>
            <div class='park-video-player-embed'><iframe class="videopage-iframe" width="717" height="436" src="http://www.youtube.com/embed/<?php echo $videos[0]["id"] ?>" frameborder="0" allowfullscreen></iframe></div>
          </div>
          <?php
          if (count($videos) > 0 && ($tier > 2 || $nodeInfo->field_camp_swamp_city[LANGUAGE_NONE][0]["value"])) {
            $end = round(count($videos) / 2);
            echo "<div id='park-video-playlist'>";
            echo "<div class='left park-playlist-col col1'>";
            echo "<div class='park-video-playlist-item' rel='" . $videos[LANGUAGE_NONE][0]["id"] . "' title='" . addslashes($videos[LANGUAGE_NONE][0]["title"]) . "'><img src='http://i3.ytimg.com/vi/" . $videos[LANGUAGE_NONE][0]["id"] . "/default.jpg' width='120' height='90' align='left' />" . $videos[LANGUAGE_NONE][0]["title"] . "</div>";
            echo "</div>";
            echo "<div class='left park-playlist-col'>";
			/*
            for ($i = $end; $i < count($videos); $i++) {
              echo "<div class='park-video-playlist-item' rel='" . $videos[$i]["id"] . "' title='" . addslashes($videos[$i]["title"]) . "'><img src='http://i3.ytimg.com/vi/" . $videos[$i]["id"] . "/default.jpg' width='120' height='90' align='left' />" . $videos[$i]["title"] . "</div>";
            }
			*/
            echo "</div>";
            echo "<br clear='all' /></div>";
          }
          ?>
          <br clear="all" />
          </div> <!-- park-video-wrapper -->
          <?php
          } else {
            echo "No videos have been uploaded yet for this park.";
          }

        ?>
        <br clear="all" />
      </div> <!-- /#park-videos -->
      <div id="park-photos" class="park-tab-content<?php if ($activeTab != 'photos') { echo ' hide'; } ?>">
      <?php
        if ($node->field_camp_slideshow[LANGUAGE_NONE][0][uri]) {
		  switch ($tier) {
			  case 1:
			    $photoLimit = 3;
				break;
			  case 2:
			    $photoLimit = 12;
				break;
			  case 3:
			    $photoLimit = 24;
				break;
			  case 4:
			    $photoLimit = 24;
				break;
			  case 6:
			    $photoLimit = 12;
				break;
			  case 7:
			    $photoLimit = 24;
				break;
			  case 8:
			    $photoLimit = 24;
				break;
			  default:
			    $photoLimit = 3;
				break;
			}
		  $column = 1;
          for ($i = 0; $i < $photoLimit; $i++) {
		    if ($node->field_camp_slideshow[LANGUAGE_NONE][$i][uri]) {
              $imageSize = getimagesize(file_create_url($node->field_camp_slideshow[LANGUAGE_NONE][$i][uri]));
              $width = $imageSize[0];
              $height = $imageSize[1];
              if ($width > $height) {
                $width = 150;
                $height = ($imageSize[1] * $width) / $imageSize[0];
              } else {
                $height = 150;
                $width = ($imageSize[0] * $height) / $imageSize[1];
              }
              echo "<div class='photo-thumbnail' rel='#photo-" . $i . "'>";
              echo "<a href='#photo-slideshow'><img src='" . file_create_url($node->field_camp_slideshow[LANGUAGE_NONE][$i][uri]) . "' width='" . $width . "' height='" . $height . "' /></a>";
              echo "</div>";
			  if ($column == 4) {
			    echo "<br clear='all' />";
				$column = 1;
			  } else {
			    $column++;
			  }
			}
          }
          echo "<br clear='all' />";
          // Generate modal for slideshow
          echo "<div id='photo-slideshow-wrapper' class='hide'>";
          echo "<div id='photo-slideshow' class='ui-corners-all'>";
          echo "<img src='/sites/all/themes/gca_new/images/icon-close.png' align='right' class='slideshow-close' />";
          $x = 0;
		  $picCount = 0;
		  foreach ($node->field_camp_slideshow[LANGUAGE_NONE] as $pic) {
		    if ($pic["uri"]) {
			  $picCount++;
			}
		  }
		  for ($i = 0; $i < $photoLimit; $i++) {
		    if ($node->field_camp_slideshow[LANGUAGE_NONE][$i]["uri"]) {
          //foreach ($node->field_camp_slideshow as $photo) {
            echo "<div class='photo-wrapper hide";
            echo "' id='photo-" . $x . "'>";
            echo "<img src='/sites/all/themes/gca_new_interior/images/photo_previous.gif' class='photo-arrow' style='float:left;margin:175px 20px 0 0;' rel='#photo-";
            if ($x == 0) {
              echo ($picCount - 1);
            } else {
              echo $x - 1;
            }
            echo "' />";
            echo "<div class='park-photo' style='float:left;text-align:center;' >";
            $imageSize = getimagesize(file_create_url($node->field_camp_slideshow[LANGUAGE_NONE][$i][uri]));
            $width = $imageSize[0];
            $height = $imageSize[1];
            if ($imageSize[0] > 600) {
              $width = 600;
              $height = ($imageSize[1] * $width) / $imageSize[0];
            }
            $paddingTop = 0;
            if ($height < 410) {
              $paddingTop = floor((420 - $height) / 2);
            }
            echo "<img src='" . file_create_url($node->field_camp_slideshow[LANGUAGE_NONE][$i][uri]) . "' style='padding-top:" . $paddingTop . "px;margin:0 auto;' width='" . $width . "' height='" . $height . "' /><br />";
			if ($node->field_camp_slideshow[LANGUAGE_NONE][$i]["data"]["alt"]) {
			  echo $node->field_camp_slideshow[LANGUAGE_NONE][$i]["data"]["alt"] . "<br clear='all' />";
            }
			echo "<br clear='all' /></div>";
            echo "<img src='/sites/all/themes/gca_new_interior/images/photo_next.gif' style='float:left;margin:175px 0 0 20px;' class='photo-arrow' rel='#photo-";
            if ($x == ($picCount - 1)) {
              echo "0";
            } else {
              echo $x + 1;
            }
            echo " ' />";
            echo "<br clear='all' /></div>";
            $x++;
			}
          }
        echo "</div><br clear='all' /></div>";
        } ?>
      </div> <!-- /#park-photos -->
      <div id="park-contact" class="park-tab-content<?php if ($activeTab != 'contact') { echo ' hide'; } ?>">
        <h3>Contact <?php echo $title; ?></h3><br />
        <form method='post' action='/send-email'>
        Your Email: <input name='email' type='text' /><br />
        <input name='subject' type='hidden' value='Contact from GoCampingAmerica.com' />
        <input name='pid' type='hidden' value='<?php echo $node->nid; ?>' />
        Message:<br />
        <textarea name='message' rows='15' cols='40'>
        </textarea><br />
        <?php
          require($_SERVER["DOCUMENT_ROOT"] . '/scripts/recaptchalib.php');
          $publickey = "6Lc0ss4SAAAAAKf_5VZZVg7hXR1zCojXDTmADubT"; // you got this from the signup page
          echo recaptcha_get_html($publickey);
        ?>
        <input type='submit' />
        </form>
      </div>
    </div>
	<?php
	//echo "<pre style='display:none;'>";
	//print_r($node);
	//echo "</pre>";
	?>
  </div>


  <?php if (!empty($terms) || !empty($links)): ?>
    <footer>
      <?php if ($terms): ?>
        <div class="terms">
          <span><?php print t('Tags: ') ?></span><?php print $terms ?>
        </div>
      <?php endif;?>
      <?php if ($links): ?>
        <div class="links">
          <?php print $links; ?>
        </div>
      <?php endif; ?>
    </footer>
  <?php endif;?>

<?php if (!$page): ?>
  </article> <!-- /.node -->
<?php endif;?>

<?php
function getFBT($nid) {
  $query = db_query("SELECT field_park_facebook_url, field_park_twitter_url FROM {content_type_camp} WHERE nid = :nid", array('nid' => $nid));
  while ($row = $query->fetchAssoc()) {
	$result["facebook"] = $row["field_park_facebook_url"];
	$result["twitter"] = $row["field_park_twitter_url"];
  }
  return $result;
}

function getAbbreviation($state) {
  $states = array_flip(array('AL'=>"Alabama",
'AK'=>"Alaska",
'AZ'=>"Arizona",
'AR'=>"Arkansas",
'CA'=>"California",
'CO'=>"Colorado",
'CT'=>"Connecticut",
'DE'=>"Delaware",
'DC'=>"District Of Columbia",
'FL'=>"Florida",
'GA'=>"Georgia",
'HI'=>"Hawaii",
'ID'=>"Idaho",
'IL'=>"Illinois",
'IN'=>"Indiana",
'IA'=>"Iowa",
'KS'=>"Kansas",
'KY'=>"Kentucky",
'LA'=>"Louisiana",
'ME'=>"Maine",
'MD'=>"Maryland",
'MA'=>"Massachusetts",
'MI'=>"Michigan",
'MN'=>"Minnesota",
'MS'=>"Mississippi",
'MO'=>"Missouri",
'MT'=>"Montana",
'NE'=>"Nebraska",
'NV'=>"Nevada",
'NH'=>"New Hampshire",
'NJ'=>"New Jersey",
'NM'=>"New Mexico",
'NY'=>"New York",
'NC'=>"North Carolina",
'ND'=>"North Dakota",
'OH'=>"Ohio",
'OK'=>"Oklahoma",
'OR'=>"Oregon",
'PA'=>"Pennsylvania",
'PR'=>"Puerto Rico",
'RI'=>"Rhode Island",
'SC'=>"South Carolina",
'SD'=>"South Dakota",
'TN'=>"Tennessee",
'TX'=>"Texas",
'UT'=>"Utah",
'VT'=>"Vermont",
'VA'=>"Virginia",
'WA'=>"Washington",
'WV'=>"West Virginia",
'WI'=>"Wisconsin",
'WY'=>"Wyoming"));
  return $states[$state];
}

function getQty($nodeInfo, $term) {
  switch ($term) {
    case "20 Amp Service":
	  return $nodeInfo->field_camp20amp[0][value];
	  break;
	case "30 Amp Service":
	  return $nodeInfo->field_camp30amp[0][value];
	  break;
	case "50 Amp Service":
	  return $nodeInfo->field_camp50amp[0][value];
	  break;
	case "Electric & Water":
	  return $nodeInfo->field_camp_electric_water[0][value];
	  break;
	case "Electric":
	  return $nodeInfo->field_camp_electrical[0][value];
	  break;
	case "Pull-Throughs":
	  return $nodeInfo->field_camp_pull_throughs[0][value];
	  break;
	case "Rental Cabins":
	  return $nodeInfo->field_camp_rental_cabins[0][value];
	  break;
	case "Rental Trailers":
	  return $nodeInfo->field_camp_rental_trailers[0][value];
	  break;
	case "Seasonal":
	  return $nodeInfo->field_camp_seasonal[0][value];
	  break;
	case "Sideouts":
	  return $nodeInfo->field_camp_sideouts[0][value];
	  break;
	case "Tents":
	  return $nodeInfo->field_camp_tents[0][value];
	  break;
	case "No Hookups":
	  return $nodeInfo->field_camp_no_hookups[0][value];
	  break;
	case "Full Hookups":
	  return $nodeInfo->field_camp_full_hookups[0][value];
	  break;
    default:
	  return "Yes";
	  break;
  }
}

function getResources($state) {
  $query = db_query("SELECT sr.delta, sr.field_state_resources_url, sr.field_state_resources_title FROM {node} n, {content_field_state_resources} sr WHERE n.nid = sr.nid AND n.title = ':title' ORDER BY sr.delta ASC", array('title' => $state));
  $x = 0;
  while ($row = $query->fetchObject()) {
    $resources[$x][url] = $row->field_state_resources_url;
    $resources[$x][title] = $row->field_state_resources_title;
    $x++;
  }
  return $resources;
}

function getUID($name) {
  $query = db_query("SELECT uid FROM {users} WHERE name = ':title' LIMIT 1", array('title' => $name));
  while ($row = $query->fetchAssoc()) {
    $result = $row["uid"];
  }
  return $result;
}

function getNewestVID($nid) {
  $query = db_query("SELECT vid FROM {node} WHERE nid = :nid ORDER BY vid DESC LIMIT 1", array('nid' => $nid));
  while ($row = $queryfetchAssoc()) {
    $result = $row["vid"];
  }
  return $result;
}
?>