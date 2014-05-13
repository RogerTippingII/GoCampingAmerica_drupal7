

<div id="loading" style="display:none;font-weight:bold;"><img src="/img/gca_loader.gif" /><br />LOADING ...</div>
<?php
// Set up page to receive variables passed from the Find a Park widget on the home page or state overview pages

if (isset($_REQUEST['fap'])) {
  // If FAP is set, the request is coming from either the home page or a state overview page and the variables are being passed in via the url 
  ?>

  <script type="text/javascript">
    $gca("#gca-search-throbber").show();
    $gca("#loading").show();
  </script>

  <?php

  if (isset($_REQUEST['l'])) {

    // If "l" is set, the search is location-based. Loop through the states and look for a match with the location value. If the search location was a state by itself (and without any filters chosen), reload and show the state overview page

    $state_list = array('AL'=>"Alabama", 'AK'=>"Alaska", 'AZ'=>"Arizona", 'AR'=>"Arkansas", 'CA'=>"California", 'CO'=>"Colorado", 'CT'=>"Connecticut", 'DE'=>"Delaware", 'DC'=>"District Of Columbia", 'FL'=>"Florida", 'GA'=>"Georgia", 'HI'=>"Hawaii", 'ID'=>"Idaho", 'IL'=>"Illinois", 'IN'=>"Indiana", 'IA'=>"Iowa", 'KS'=>"Kansas", 'KY'=>"Kentucky", 'LA'=>"Louisiana", 'ME'=>"Maine", 'MD'=>"Maryland", 'MA'=>"Massachusetts", 'MI'=>"Michigan", 'MN'=>"Minnesota", 'MS'=>"Mississippi", 'MO'=>"Missouri", 'MT'=>"Montana", 'NE'=>"Nebraska", 'NV'=>"Nevada", 'NH'=>"New Hampshire", 'NJ'=>"New Jersey", 'NM'=>"New Mexico", 'NY'=>"New York", 'NC'=>"North Carolina", 'ND'=>"North Dakota", 'OH'=>"Ohio", 'OK'=>"Oklahoma", 'OR'=>"Oregon", 'PA'=>"Pennsylvania", 'RI'=>"Rhode Island", 'SC'=>"South Carolina", 'SD'=>"South Dakota", 'TN'=>"Tennessee", 'TX'=>"Texas", 'UT'=>"Utah", 'VT'=>"Vermont", 'VA'=>"Virginia", 'WA'=>"Washington", 'WV'=>"West Virginia", 'WI'=>"Wisconsin", 'WY'=>"Wyoming", 'AB'=>"Alberta", 'BC'=>"British Columbia", 'MB'=>"Manitoba", 'NB'=>"New Brunswick", 'NL'=>"Newfoundland", 'NT'=>"Northwest Territories", 'NS'=>"Nova Scotia", 'NU'=>"Nunavut", 'ON'=>"Ontario", 'PE'=>"Prince Edward Island", 'QC'=>"Quebec", 'SK'=>"Saskatchewan", 'YT'=>"Yukon");

    foreach($state_list as $key=>$value) {
      if (strtolower($_REQUEST["l"]) == strtolower($value)) {
        // A match was found. Reload the page as the corresponding state overview page.
        ?>
        <script type="text/javascript">
          window.location = "/findpark?p=overview&id=<?php echo $key; ?>&state=<?php echo $value; ?>";
        </script>
      <? }
    }

    // The location was more specific that just a state name. Continue on.
    $location = str_replace(" ", "+", preg_replace("/[^a-zA-Z0-9\s]/", "", $_REQUEST['l']));

  }

  if (isset($_REQUEST['r'])) {

    // What distance was specified?
    $radius = preg_replace("/[^0-9]/", "", $_REQUEST['r']);

  }

  if (isset($_REQUEST['o'])) {

    // Determine what kind of search: l = location-based; p = park-name based
    $olCode = preg_replace("/[^a-z]/", "", $_REQUEST['o']);

  }

  if (isset($_REQUEST['p'])) {

    // Was a park name specified instead of a location?
    $park = urlencode($_REQUEST['p']);

    //Show ads ?>
    <script type="text/javascript">
      $gca("#search-ads").show();
    </script>

  <?php }

  ?>
  <script type="text/javascript">
    <!--
    <?php
      if (!$park) {
        // If search was run for a specific location (not a park name or standalone state)...
    ?>
    //alert("Search is location based.");
    var urlStringMap = "/scripts/search_results.php?<?php echo $olCode; ?>=\"<?php echo $location; ?>\"&r=<?php echo $radius; ?>&smap=1";
    <?php
      } else {

        // Search was for park name, not a location
    ?>
    <?php if (isset($_REQUEST["sid"])) {
      // If a state ID (sid) was passed, the park name search came from a state overview page
    ?>
    var urlStringMap = "/park-search?p=\"<?php echo $park; ?>\"&sid=<?php echo $_REQUEST["sid"]; ?>";

    <?php } else {

    // Because no sid was passed, the park name search came from either the home page or the Find a Park page
    ?>
    var urlStringMap = "/park-search?p=<?php echo $park; ?>";
    //alert("urlStringMap = " + urlStringMap);

    <?php } // end if isset sid ?>

    $gca('#states-navigation').hide();

    <?php } // end if !$park ?>

    //alert("urlStringMap: " + urlStringMap);
    var tags = <? echo "\"". ((isset($_REQUEST["t"]))?$_REQUEST["t"]:"") ."\"";?>;
    var urlString = urlStringMap.replace("smap=1", "smap=0") + "&t="+ tags +" #search-results";


    //alert("urlString = " + urlString);

    $gca(function() {

      <?php if (!$park) { ?>
      $gca('#states-wrapper, #state-start-intro, #states-navigation').hide();

      // If the search was for a location, not a park name load the big map
      // Park-name searches don't show maps
      // Load the results from the park-search page
      // Show the ads (which are hidden until a search is run)

      $gca('#gca-search-results').load(urlString, function() {
        $gca('#gca-search-results').show();
        $gca('#search-ads').show();
        $gca('#asw-right').html("<iframe width='314' height='187' src='" + urlStringMap + "' frameborder='0' scrolling='no'></iframe>");
        $gca('#asw-enlarge').show();
        $gca('#search-map-wrapper').html("<iframe width='690' height='350' src='" + urlStringMap + "#large-map' frameborder='0' scrolling='no'></iframe>");
        $gca('#gca-search-throbber').hide();
        $gca("#loading").hide();
        $gca("#footer-leaderboard #search-ads").show();
      });


      <?php } else { ?>
      $gca('#gca-search-results').load(urlString, function() {
        $gca("#gca-search-throbber").hide();
        $gca("#loading").hide();
        $gca("#footer-leaderboard #search-ads").show();
      }).show();
      <?php } // end if !$park ?>
    });
    -->
  </script>
<?php
} // end if isset "fap"

?>

<script type="text/javascript">
  function showLoc(searchType) {
    console.log("showLoc called.");
    var location;
    var states = {
      AL: "Alabama",
      AK: "Alaska",
      AK: "Alaska",
      AZ: "Arizona",
      AR: "Arkansas",
      CA: "California",
      CO: "Colorado",
      CT: "Connecticut",
      DE: "Delaware",
      DC: "District Of Columbia",
      FL: "Florida",
      GA: "Georgia",
      HI: "Hawaii",
      ID: "Idaho",
      IL: "Illinois",
      IN: "Indiana",
      IA: "Iowa",
      KS: "Kansas",
      KY: "Kentucky",
      LA: "Louisiana",
      ME: "Maine",
      MD: "Maryland",
      MA: "Massachusetts",
      MI: "Michigan",
      MN: "Minnesota",
      MS: "Mississippi",
      MO: "Missouri",
      MT: "Montana",
      NE: "Nebraska",
      NV: "Nevada",
      NH: "New Hampshire",
      NJ: "New Jersey",
      NM: "New Mexico",
      NY: "New York",
      NC: "North Carolina",
      ND: "North Dakota",
      OH: "Ohio",
      OK: "Oklahoma",
      OR: "Oregon",
      PA: "Pennsylvania",
      RI: "Rhode Island",
      SC: "South Carolina",
      SD: "South Dakota",
      TN: "Tennessee",
      TX: "Texas",
      UT: "Utah",
      VT: "Vermont",
      VA: "Virginia",
      WA: "Washington",
      WV: "West Virginia",
      WI: "Wisconsin",
      WY: "Wyoming",
      AB: "Alberta",
      BC: "British Columbia",
      MB: "Manitoba",
      NB: "New Brunswick",
      NL: "Newfoundland",
      NT: "Northwest Territories",
      NS: "Nova Scotia",
      NU: "Nunavut",
      ON: "Ontario",
      PE: "Prince Edward Island",
      QC: "Quebec",
      SK: "Saskatchewan",
      YT: "Yukon"
    };
    for(var i in states) {

      // If the location is a state by itself, without tags selected, redirect to state page
      if ($gca('#search-location').val().toLowerCase() == states[i].toLowerCase() && !$gca('#gca-search-terms').html()) {
        window.location = "/findpark?p=overview&id=" + i + "&state=" + states[i];

      }
    }

    if ($gca('#search-location').val() || $gca('#gca-landmark').html() || $gca('#search-location-mini').val()) {
      if ($gca('#search-location').val()) {
        var optin = "yes";
        if ($gca('#optin:checked').val() != "yes") {
          optin = "no";
        }
        location = $gca('#search-location').val().replace(/[^\w\s]|_/g, "").replace(/\s+/g, "+");
        var radius = $gca('#search-distance').val();
        var olCode = "l"; // set flag for location-based search
      } else if ($gca('#search-location-mini').val()) {
        var stateSearch = "<?php echo $_REQUEST['id'] ?>";
        var optin = "yes";
        location = $gca('#search-location-mini').val().replace(/[^\w\s]|_/g, "").replace(/\s+/g, "+");
        location = location + "+" + stateSearch;
        var radius = $gca('#search-distance-mini').val();
        var olCode = "l";
      } else {
        location = $gca('#gca-landmark').html();
        var radius = $gca('#search-landmark-distance').val();
        var olCode = "c";
      }
    } else {
      // No location has been specified so treat the search as a nationwide search
      var optin = "yes";
      if ($gca('#optin:checked').val() != "yes") {
        optin = "no";
      }
      var state = "<?php echo $_REQUEST['state'] ?>";
      if ($gca('#search-location-mini').val() == "" && state) {
        location = state.replace(/[^\w\s]|_/g, "").replace(/\s+/g, "+");
      } else {
        location = "salina+kansas";
      }
      var radius = 3000;
      var olCode = "l";
    }

    // Define URL that will be used to load the results from the "search results query" page
    var urlString = "/scripts/search_results.php?t=\"" + $gca('#gca-search-terms').html() + "\"&" + olCode + "=\"" + location + "\"&r=" + radius + "&optin=" + optin + "&state=" + stateSearch + "&type=" + searchType;
    console.log("urlString: " + urlString);
    // Define URL that will be used to load the maps
    var urlStringSmallMap = "/scripts/search_results.php?t=\"" + $gca('#gca-search-terms').html() + "\"&" + olCode + "=\"" + location + "\"&r=" + radius + "&optin=" + optin + "&state=" + stateSearch + "&type=" + searchType + "&smap=1";

    // Load textual search results (non-map results)
    $gca('#gca-search-results').load(urlString + " #search-results", function() {
      $gca("#gca-search-results").show();
      $gca("#gca-search-throbber").hide();
      $gca("#loading").hide();
      $gca("#mini-advanced-search-widget #gca-search-throbber").hide();
      if (searchType == 1) {
        // If searchType == 1, it means we're on a state page.
        $gca('#search-map-state').html("<iframe width='690' height='350' src='" + urlString + "&smap=1&hide=small#large-map' frameborder='0' scrolling='no'></iframe>");
        //$gca('#asw-enlarge').show();
      } else {
        // Load the small map on the main Find a Park page
        $gca('#asw-right').html("<iframe width='314' height='187' src='" + urlStringSmallMap + " #search-map-small' frameborder='0' scrolling='no'></iframe>");
        $gca('#asw-enlarge').show();
        // Load large map
        $gca('#search-map-wrapper').html("<iframe width='690' height='350' src='" + urlString + "&smap=1&hide=small #large-map' frameborder='0' scrolling='no'></iframe>");
      }
    });

    $gca('#states-wrapper').hide();
    $gca('#state-start-intro').hide();
  }

  function hideMainSearch(state) {
    $gca("#full-width-content").hide();
    $gca("#mini-advanced-search-widget #location-label").html("Search " + state + " Parks");
    $gca("#mini-advanced-search-widget").show();
  }

  function replaceHeaderImg() {
    $gca(".imagefield-field_top_photo").attr("src", $gca("#state-header-img").html());
  }

</script>

<?php
$vocabs = array("affiliation", "amenities", "credit-cards", "lifestyles", "site-options", "recreation", "services");
$terms = getTaxonomies($vocabs);
?>
<a name="fap"></a>
<div id="gca-search-terms" class="hide"></div>
<div id="gca-landmark" class="hide"></div>
<input id="gca-search-longlat" value="" type="hidden" />
<a name="large-results-map"></a><div id="search-map-wrapper" class="ui-corners-all hide"></div>
<div id="gca-search-results" class="ui-corners-all hide"></div>
<a name="search-results"></a>
<?php if (!isset($_REQUEST['p'])) { ?>
  <!--
  <div id="state-start-intro">
      <p>Go Camping America makes it easy to find your next camping adventure. Use the map to search state-by-state or search by city. Already have a park in mind? Search by name or keyword!</p>
    <br />
    <p><b>4 steps to finding the perfect park:</b></p>
    <ol>
      <li>Search by state. Using the map to the left, select a state and view all its parks.</li>
      <li>Find parks by city. Choose the state, select a city and search within a certain radius.</li>
      <li>Locate a specific park by typing in the campground name.</li>
      <li>Use keywords to find what you’re looking for.</li>
    </ol>
    <br />
    <p>Choose your search method and let Go Camping America do the work for you!</p>
  </div>
  -->
<?php } ?>
<div id="states-wrapper">

<?php

$variables = getVariables();
$districtType = getDistrictType($variables[state]);

if ($variables[page] == "start") {
setFindParkTitle();
setPageTitle("Find a Park Using Our Campground Search"); ?>
<div id="state-start">
  <div id="state-master-map"><a name="countriesmap"></a>
    <?php //displayMasterMap(); ?></div>


  <?php } elseif ($variables[page] == "overview") {
    showStatetabs();
    echo "<div id='state-header-img' class='hide'>" . getHeaderImg($variables[state]) . "</div>"; ?>
    <script type="text/javascript">
      <!--
      hideMainSearch("<?php echo $variables[state] ?>");
      replaceHeaderImg();
      -->
    </script>
  <?php
  echo "<div id='state-overview'>\n";
  if ($districtType == "province") {
    setPageTitle($variables[state] . " Province Overview");
    setStateID($variables["id"]);
  } else {
    setPageTitle($variables[state] . " State Overview");
    setStateID($variables["id"]);
  }
  echo showStateOverview($variables[state]);
  echo "</div> <!-- /#state-overview -->";
  echo "<div id='state-attractions' style='display:none;'>\n";
  echo "<h2>Tourist Attractions</h2>";
  showAttractions($variables[state]);
  echo "</div>";
  echo "<div id='state-rules' style='display:none;'>\n";
  echo "<h2>Rules of the Road</h2>";
  showRules($variables[state]);
  echo "</div>";
  echo "<div id='state-links' style='display:none;'>\n";
  echo "<h2>Additional Resources</h2>";
  showLinks($variables[state]);
  echo "</div>";

  // Show ads ?>
    <script type="text/javascript">
      $gca(document).ready(function() {
        $gca("#search-ads").show();
      });
    </script>
  <?php
  echo "<div id='state-parks'>";
  $parks = listStateParks($_REQUEST['id']);
  $featuredParks = getStateFeatured($parks);
  //$featuredParks = getFeaturedParks($_REQUEST['id']);

  // Show featured parks first
  if (count($featuredParks)) { ?>
    <script type="text/javascript">
      console.log("Featured parks found.");
    </script>
    <?php
    echo "<pre style='display:none;'>";
    print_r($featuredParks);
    echo "</pre>";
    $colLimit = round(count($featuredParks) / 2);
    echo "<div id='state-featured-parks'>";
    echo "<table id='state-featured-parks-table'><tbody><tr><td valign='top'>";
    for ($i = 0; $i < $colLimit; $i++) {
      echo "<p><b><a href='/" . getResultAlias($featuredParks[$i]["nid"]) . "'>" . $featuredParks[$i]["title"] . "</a></b> <span class='featured-title'>FEATURED</span><br />";
      echo $featuredParks[$i]["city"] . ", " . $featuredParks[$i]["state"] . "<br />";
      echo "<div style='margin-top:7px;font-weight:bold;font-size:0.9em;'>" . $featuredParks[$i]["promo"] . "</div></p>";
    }
    echo "</td><td valign='top'>";
    for ($i = $colLimit; $i < count($featuredParks); $i++) {
      echo "<p><b><a href='/" . getResultAlias($featuredParks[$i]["nid"]) . "'>" . $featuredParks[$i]["title"] . "</a></b> <span class='featured-title'>FEATURED</span><br />";
      echo $featuredParks[$i]["city"] . ", " . $featuredParks[$i]["state"] . "</p>";
    }
    echo "</td></tr></tbody></table>";
    echo "</div>";
  }

    // Show all non-featured parks
    if (count($parks)) {
      $colLimit = round(count($parks) / 2);
      echo "<table id='state-not-featured-parks'><tbody><tr><td valign='top'>";
      for ($i = 0; $i < $colLimit; $i++) {
        echo "<p><b><a href='/" . getResultAlias($parks[$i]["nid"]) . "'>" . $parks[$i]["title"] . "</a></b>";
        $checkForDeal = checkForDeal($parks[$i]["nid"]);
        if ($checkForDeal == 1) {
          echo " <span class='featured-title'>SPECIAL DEAL</span>";
        }
        echo "<br />";
        echo $parks[$i]["city"] . ", " . $parks[$i]["state"] . "</p>";
      }
      echo "</td><td valign='top'>";
      for ($i = $colLimit; $i < count($parks); $i++) {
        echo "<p><b><a href='/" . getResultAlias($parks[$i]["nid"]) . "'>" . $parks[$i]["title"] . "</a></b>";
        $checkForDeal = checkForDeal($parks[$i]["nid"]);
        if ($checkForDeal == 1) {
          echo " <span class='featured-title'>SPECIAL DEAL</span>";
        }
        echo "<br />";
        echo $parks[$i]["city"] . ", " . $parks[$i]["state"] . "</p>";
      }
      echo "</td></tr></tbody></table>";
    } else {
      echo "No parks have been entered for this area.";
    }
    echo "</div>";
  } elseif ($variables[page] == "attractions") {
    showStatetabs();
    echo "<div id='state-header-img' class='hide'>" . getHeaderImg($variables[state]) . "</div>"; ?>
    <script type="text/javascript">
      <!--
      hideMainSearch("<?php echo $variables[state] ?>");
      replaceHeaderImg();
      -->
    </script>
    <?php
    echo "<div id='state-attractions'>\n";
    setPageTitle($variables[state] . " Tourist Attractions");
    //setNav($variables[id], $variables[state], $districtType);  
    showAttractions($variables[state]);
    echo "</div>";
  } elseif ($variables[page] == "rules") {
    showStatetabs();
    echo "<div id='state-header-img' class='hide'>" . getHeaderImg($variables[state]) . "</div>"; ?>
    <script type="text/javascript">
      <!--
      hideMainSearch("<?php echo $variables[state] ?>");
      replaceHeaderImg();
      -->
    </script>
    <?php
    echo "<div id='state-rules'>\n";
    showStateName($variables[state]);
    setPageTitle($variables[state] . " Rules of the Road");
    setNav($variables[id], $variables[state], $districtType);
    showRules($variables[state]);
    echo "</div>";
  } elseif ($variables[page] == "links") {
    showStatetabs();
    echo "<div id='state-header-img' class='hide'>" . getHeaderImg($variables[state]) . "</div>"; ?>
    <script type="text/javascript">
      <!--
      hideMainSearch("<?php echo $variables[state] ?>");
      replaceHeaderImg();
      -->
    </script>
    <?php
    echo "<div id='state-links'>\n";
    setPageTitle($variables[state] . " Useful Links");
    //setNav($variables[id], $variables[state], $districtType);  
    showLinks($variables[state]);
    echo "</div>";
  } ?>

</div>
<?php

function getTaxonomies($vocabs) {
  //$x = 1;
  //$vocabs = array("affiliation", "amenities", "credit-cards", "lifestyles", "site-options", "recreation", "services");
  foreach ($vocabs as $vocab) {
    switch ($vocab) {
      case "affiliation":
        $x = 1;
        break;
      case "amenities":
        $x = 2;
        break;
      case "credit-cards":
        $x = 3;
        break;
      case "lifestyles":
        $x = 6;
        break;
      case "site-options":
        $x = 18;
        break;
      case "recreation":
        $x = 5;
        break;
      case "services":
        $x = 17;
        break;
      default:
        $x = 0;
        break;
    }
    $query = db_query("SELECT * from {taxonomy_term_data} WHERE vid = :vid", array("vid" => $x));
    while ($row = $query->fetchObject()) {
      $numParks = checkForParks($row->tid);
      if ($numParks == 1) {
        $terms[$x][$row->tid][tid] = $row->tid;
        $terms[$x][$row->tid][name] = $row->name;
      }
    }
    //$x++;
  }
  return $terms;
}

function checkForParks($tid) {
  $query = db_query("SELECT DISTINCT nid FROM {taxonomy_index} WHERE tid = :tid", array("tid" => $tid));

  while ($row = $query->fetchObject()) {
    return 1;
  }
  return 0;
}

function getTidNodes($tid) {
  $query = db_query ("SELECT nid FROM {term_node} WHERE tid = %d", $tid);
  while ($row = db_fetch_object($query)) {
    $parks[] = $row->nid;
  }
  return $parks;
}

function getLandmarks() {
  $query = db_query("SELECT n.nid, n.title, ctl.field_landmark_latitude_value as latitude, ctl.field_landmark_longitude_value as longitude FROM {node} n, {content_type_landmarks} ctl WHERE n.nid = ctl.nid AND n.type = 'landmarks' AND n.status = 1 ORDER BY n.title ASC");
  $x = 0;
  while ($row = db_fetch_object($query)) {
    $landmarks[$x][nid] = $row->nid;
    $landmarks[$x][title] = $row->title;
    $landmarks[$x][coords] = $row->longitude . "|" . $row->latitude;
    $x++;
  }
  return $landmarks;
}

// Former FaP functions

function getVariables() {
  if (isset($_REQUEST['p'])) {
    $variable[page] = preg_replace("/[^a-z]/", "", $_REQUEST['p']);
  } else {
    $variable[page] = 'start';
  }
  if (isset($_REQUEST['id'])) {
    $variable[id] = preg_replace("/[^A-Z]/", "", $_REQUEST['id']);
  }
  if (isset($_REQUEST['state'])) {
    $variable[state] = preg_replace("/[^a-zA-Z\s]/", "", $_REQUEST['state']);
  }
  return $variable;
}

function showStateName($state) { ?>
  <script type="text/javascript">
    <!--
    $gca('#page-subtitle').html("<?php echo $state; ?>");
    $gca('title').html("<?php echo $state; ?> Campsite, RV Park & Campground Search");
    -->
  </script>
<?php }

function setStateID($id) { ?>
  <script type="text/javascript">
    <!--
    $gca("#state-id").html("<?php echo $id; ?>");
    -->
  </script>
<?php }

function setFindParkTitle() { ?>
  <script type="text/javascript">
    <!--
    $gca('title').html("Find a Campground, Campsite or RV Park");
    -->
  </script>
<?php }

function setPageTitle($title) { ?>
  <script type="text/javascript">
    <!--
    $gca('h1.title').html("<?php echo $title; ?>");
    -->
  </script>
<?php }

function setNav($id, $state, $districtType) { ?>
  <script type="text/javascript">
    <!--
    <?php if ($districtType == "province") { ?>
    $gca('#nav-overview').html("Province Overview");
    <?php } ?>
    $gca('#nav-overview').attr("href", "?p=overview&id=<?php echo $id; ?>&state=<?php echo $state; ?>");
    $gca('#nav-attractions').attr("href", "?p=attractions&id=<?php echo $id; ?>&state=<?php echo $state; ?>");
    $gca('#nav-rules').attr("href", "?p=rules&id=<?php echo $id; ?>&state=<?php echo $state; ?>");
    $gca('#nav-links').attr("href", "?p=links&id=<?php echo $id; ?>&state=<?php echo $state; ?>");
    -->
  </script>
<?php }

function showStateOverview($state) {
  $query =new EntityFieldQuery();
  $query->entityCondition('entity_type', 'node')
    ->entityCondition('bundle', 'state')
    ->entityCondition('title', $state)
    ->range(0, 1);

  $result = $query->execute();

  if(isset($result)){
    $nid = array_keys($result["node"])[0];

    $node = node_load($nid);

    $overview = $node->field_state_overview[LANGUAGE_NONE][0]["value"];
  }

  if ($overview) {
    echo $overview;
  } else {
    echo "No overview has been set for this state.";
  }
}

function showAttractions($state) {

  $query =new EntityFieldQuery();
  $query->entityCondition('entity_type', 'node')
    ->entityCondition('bundle', 'state')
    ->entityCondition('title', $state)
    ->range(0, 1);

  $result = $query->execute();

  if(isset($result)){
    $nid = array_keys($result["node"])[0];

    $node = node_load($nid);

    $attractions = $node->field_state_attractions[LANGUAGE_NONE][0]["value"];
  }

  if ($attractions) {
    echo $attractions;
  } else {
    echo "No attractions have been set for this state.";
  }
}

function showRules($state) {
  $query =new EntityFieldQuery();
  $query->entityCondition('entity_type', 'node')
    ->entityCondition('bundle', 'state')
    ->entityCondition('title', $state)
    ->range(0, 1);

  $result = $query->execute();

  if(isset($result)){
    $nid = array_keys($result["node"])[0];

    $node = node_load($nid);

    $rules = $node->field_state_rules[LANGUAGE_NONE][0]["value"];
  }

  if ($rules) {
    echo $rules;
  } else {
    echo "Rules of the Road has not been set for this state.";
  }
}

function showLinks($state) {
  $query =new EntityFieldQuery();
  $query->entityCondition('entity_type', 'node')
    ->entityCondition('bundle', 'state')
    ->entityCondition('title', $state)
    ->range(0, 1);

  $result = $query->execute();

  if(isset($result)){
    $nid = array_keys($result["node"])[0];

    $node = node_load($nid);

    $useful = $node->field_state_useful[LANGUAGE_NONE][0]["value"];
  }

  if ($useful) {
    echo $useful;
  } else {
    echo "Useful Links have not been set for this state.";
  }
}

function displaySearchMaster() { ?>

  <div id="state-master-search-wrapper">
    <div id="state-master-search">
      <b>Enter a Location</b><br />
      <input type="text" name="location" id="search-location" class="ui-corners-all" />
      <select name="distance" id="search-distance">
        <option value=10>&lt; 10 mi</option>
        <option value=15>&lt; 15 mi</option>
        <option value=20>&lt; 20 mi</option>
        <option value=25>&lt; 25 mi</option>
        <option value=50>&lt; 50 mi</option>
        <option value=75>&lt; 75 mi</option>
        <option value=100>&lt; 100 mi</option>
        <option value=150>&lt; 150 mi</option>
        <option value=200>&lt; 200 mi</option>
      </select>
      <div class="fap-search-box-location">Search</div>
      <br clear="all" />
      <div id="state-search-or">- OR -</div>
      <b>Search by Campground Name or Keyword</b><br />
      <input type="text" name="location" id="fap-search-park" class="ui-corners-all" />
      <div class="fap-search-box-park">Search</div>
      <br clear="all" />
      <div id="fap-advanced"><a href="/advanced-search-page">Advanced Search</a></div>
    </div>
  </div>

<?php }

function displayMasterMap() { ?>
<?php }

function getDistrictType($state) {
  $provinces = array("Alberta", "British Columbia", "Manitoba", "New Brunswick", "Newfoundland and Labrador", "Northwest Territories", "Nova Scotia", "Nunavut", "Ontario", "Prince Edward Island", "Quebec", "Saskatchewan", "Yukon");
  if (in_array($state, $provinces)) {
    return "province";
  } else {
    return "state";
  }
}

function getHeaderImg($state) {
  $query =new EntityFieldQuery();
  $query->entityCondition('entity_type', 'node')
    ->entityCondition('bundle', 'state')
    ->entityCondition('title', $state)
    ->range(0, 1);

  $result = $query->execute();

  if(isset($result)){
    $nid = array_keys($result["node"])[0];

    $node = node_load($nid);

    return $node->field_state_header_image[LANGUAGE_NONE][0]["filename"];
  }
}

function listStateParks($stateAbbrev) {
  $camps = array();

  $query = db_query("
    SELECT node.nid
    FROM node
    JOIN content_field_location cfl ON cfl.vid = node.vid
    JOIN location l ON l.lid = cfl.field_location_lid
    WHERE node.type = 'camp' AND l.province = :state",
    array(":state" => $stateAbbrev)
  );


  $nids = array();
  while($row = $query->fetchObject()){
    $nids[] = (int)$row->nid;
  }


  if(count($nids) > 0){
    $camps = entity_load('node', $nids);
  }

  $x = 0;
  foreach($camps as $nid => $camp){
    $result[$x]["nid"] = $nid;
    $result[$x]["title"] = $camp->title;
    $result[$x]["city"] = $camp->field_location[LANGUAGE_NONE][0]["city"];
    $result[$x]["state"] = $camp->field_location[LANGUAGE_NONE][0]["province"];
    $x++;
  }
  return $result;
}

function isLatest($nid) {
  $result = 0;
  $query = db_query("SELECT field_camp_status_value FROM {content_type_camp} WHERE nid = %d ORDER BY vid DESC LIMIT 1", $nid);
  while ($row = db_fetch_array($query)) {
    if ($row["field_camp_status_value"] == "Active" || $row["field_camp_status_value"] == "active") {
      $result = 1;
    }
  }
  return $result;
}

function getPromoText($nid, $vid) {
  $query = db_query("SELECT field_camp_promo_text_value FROM {content_type_camp} WHERE nid = %d AND vid = %d LIMIT 1", $nid, $vid);
  while ($row = db_fetch_array($query)) {
    $result = $row["field_camp_promo_text_value"];
  }
  if ($result) {
    return $result;
  }
  return;
}

function getStateFeatured($parks) {
  $x = 0;
  foreach ($parks as $park) {
    $nodeInfo = node_load($park["nid"]);
    if ($nodeInfo->field_park_tier[0]["value"] > 3) {
      $result[$x]["nid"] = $nodeInfo->nid;
      $result[$x]["title"] = $nodeInfo->title;
      $result[$x]["city"] = $nodeInfo->field_location[0]["city"];
      $result[$x]["state"] = $nodeInfo->field_location[0]["province"];
      if ($nodeInfo->field_park_tier[0]["value"] > 3) {
        $result[$x]["promo"] = getPromoText($nodeInfo->nid, $nodeInfo->vid);
      }
      $x++;
    }
  }
  if ($result) {
    return $result;
  }
  return;
}

function getFeaturedParks($stateAbbrev) {
  $query = db_query("SELECT DISTINCT n.nid, n.title, l.city, l.province FROM {node} n, {location} l, {location_instance} li, {content_type_camp} c WHERE n.nid = li.nid AND n.nid = c.nid AND li.lid = l.lid AND n.type = 'camp' AND l.province = '%s' AND c.field_camp_status_value = 'Active' ORDER BY n.title ASC", $stateAbbrev);
  $x = 0;
  while ($row = db_fetch_object($query)) {

    // Make sure the data is from the latest vid

    if (checkVid($row->nid) == 1) {
      $result[$x]["nid"] = $row->nid;
      $result[$x]["title"] = $row->title;
      $result[$x]["city"] = $row->city;
      $result[$x]["state"] = $row->province;
      $x++;
    }
  }
  return $result;
}

function checkVid($nid) {
  $query = db_query("SELECT field_park_tier_value FROM {content_type_camp} WHERE nid = %d ORDER BY vid DESC LIMIT 1", $nid);
  $result = 0;
  while ($row = db_fetch_array($query)) {
    if ($row["field_park_tier_value"] == 4) {
      $result = 1;
    }
  }
  return $result;
}

function getResultAlias($nid) {
  $target = "node/" . $nid;
  $query = db_query("SELECT alias FROM {url_alias} WHERE source =:target", array('target' => $target));
  while ($row = $query->fetchObject()) {
    $result = $row->dst;
  }
  if (!$result) {
    return $target;
  } else {
    return $result;
  }
}

function showAllStates() { ?>

  <!-- <?php displaySearchMaster(); ?> -->
  <!-- <h3>Park & Campground Listings</h3>
  <div id="sitemap">
    <div id="state-left">
      <ul class="states">
        <li> <a href="?p=overview&id=AL&state=Alabama">Alabama Parks</a></li>
        <li> <a href="?p=overview&id=AK&state=Alaska">Alaska Parks</a></li>
        <li> <a href="?p=overview&id=AZ&state=Arizona">Arizona Parks</a></li>
        <li> <a href="?p=overview&id=AR&state=Arkansas">Arkansas Parks</a></li>
        <li> <a href="?p=overview&id=CA&state=California">California Parks</a></li>
        <li> <a href="?p=overview&id=CO&state=Colorado">Colorado Parks</a></li>
        <li> <a href="?p=overview&id=CT&state=Connecticut">Connecticut Parks</a></li>
        <li> <a href="?p=overview&id=DE&state=Delaware">Delaware Parks</a></li>
        <li> <a href="?p=overview&id=FL&state=Florida">Florida Parks</a></li>
        <li> <a href="?p=overview&id=GA&state=Georgia">Georgia Parks</a></li>
        <li> <a href="?p=overview&id=HI&state=Hawaii">Hawaii Parks</a></li>
        <li> <a href="?p=overview&id=ID&state=Idaho">Idaho Parks</a></li>
        <li> <a href="?p=overview&id=IL&state=Illinois">Illinois Parks</a></li>
        <li> <a href="?p=overview&id=IN&state=Indiana">Indiana Parks</a></li>
        <li> <a href="?p=overview&id=IA&state=Iowa">Iowa Parks</a></li>
        <li> <a href="?p=overview&id=KS&state=Kansas">Kansas Parks</a></li>
        <li> <a href="?p=overview&id=KY&state=Kentucky">Kentucky Parks</a></li>
      </ul>
    </div>
    <div id="state-center">
      <ul class="states">
        <li> <a href="?p=overview&id=LA&state=Louisiana">Louisiana Parks</a></li>
        <li> <a href="?p=overview&id=ME&state=Maine">Maine Parks</a></li>
        <li> <a href="?p=overview&id=MD&state=Maryland">Maryland Parks</a></li>
        <li> <a href="?p=overview&id=MA&state=Massachusetts">Massachusetts Parks</a></li>
        <li> <a href="?p=overview&id=MI&state=Michigan">Michigan Parks</a></li>
        <li> <a href="?p=overview&id=MN&state=Minnesota">Minnesota Parks</a></li>
        <li> <a href="?p=overview&id=MS&state=Mississippi">Mississippi Parks</a></li>
        <li> <a href="?p=overview&id=MO&state=Missouri">Missouri Parks</a></li>
        <li> <a href="?p=overview&id=MT&state=Montana">Montana Parks</a></li>
        <li> <a href="?p=overview&id=NE&state=Nebraska">Nebraska Parks</a></li>
        <li> <a href="?p=overview&id=NV&state=Nevada">Nevada Parks</a></li>
        <li> <a href="?p=overview&id=NH&state=New+Hampshire">New Hampshire Parks</a></li>
        <li> <a href="?p=overview&id=NJ&state=New+Jersey">New Jersey Parks</a></li>
        <li> <a href="?p=overview&id=NM&state=New+Mexico">New Mexico Parks</a></li>
        <li> <a href="?p=overview&id=NY&state=New+York">New York Parks</a></li>
        <li> <a href="?p=overview&id=NC&state=North+Carolina">North Carolina Parks</a></li>
        <li> <a href="?p=overview&id=ND&state=North+Dakota">North Dakota Parks</a></li>
      </ul>
    </div>
    <div id="state-right">
      <ul class="states">
        <li> <a href="?p=overview&id=OH&state=Ohio">Ohio Parks</a></li>
        <li> <a href="?p=overview&id=OK&state=Oklahoma">Oklahoma Parks</a></li>
        <li> <a href="?p=overview&id=OR&state=Oregon">Oregon Parks</a></li>
        <li> <a href="?p=overview&id=PA&state=Pennsylvania">Pennsylvania Parks</a></li>
        <li> <a href="?p=overview&id=RI&state=Rhode+Island">Rhode Island Parks</a></li>
        <li> <a href="?p=overview&id=SC&state=South+Carolina">South Carolina Parks</a></li>
        <li> <a href="?p=overview&id=SD&state=South+Dakota">South Dakota Parks</a></li>
        <li> <a href="?p=overview&id=TN&state=Tennessee">Tennessee Parks</a></li>
        <li> <a href="?p=overview&id=TX&state=Texas">Texas Parks</a></li>
        <li> <a href="?p=overview&id=UT&state=Utah">Utah Parks</a></li>
        <li> <a href="?p=overview&id=VT&state=Vermont">Vermont Parks</a></li>
        <li> <a href="?p=overview&id=VA&state=Virginia">Virginia Parks</a></li>
        <li> <a href="?p=overview&id=WA&state=Washington">Washington Parks</a></li>
        <li> <a href="?p=overview&id=WV&state=West+Virginia">West Virginia Parks</a></li>
        <li> <a href="?p=overview&id=WI&state=Wisconsin">Wisconsin Parks</a></li>
        <li> <a href="?p=overview&id=WY&state=Wyoming">Wyoming Parks</a></li>
      </ul>
    </div>
  </div>
  <br clear="all" />
  <table id="canadaParks">
    <tr>
      <td style="width:48%;padding-right:15px;" valign="top">
        <a href="?p=overview&id=AB&state=Alberta">Alberta Parks</a><br />
        <a href="?p=overview&id=BC&state=British+Columbia">British Columbia Parks</a><br />
        <a href="?p=overview&id=MB&state=Manitoba">Manitoba Parks</a><br />
        <a href="?p=overview&id=NB&state=New+Brunswick">New Brunswick Parks</a><br />
        <a href="?p=overview&id=NL&state=Newfoundland+and+Labrador">Newfoundland and Labrador Parks</a><br />
        <a href="?p=overview&id=NT&state=Northwest+Territories">Northwest Territories Parks</a><br />
        <a href="?p=overview&id=NS&state=Nova+Scotia">Nova Scotia Parks</a><br />
      </td>
      <td valign="top">
        <a href="?p=overview&id=NU&state=Nunavut">Nunavut Parks</a><br />
        <a href="?p=overview&id=ON&state=Ontario">Ontario Parks</a><br />
        <a href="?p=overview&id=PE&state=Prince+Edward+Island">Prince Edward Island Parks</a><br />
        <a href="?p=overview&id=QC&state=Quebec">Quebec Parks</a><br />
        <a href="?p=overview&id=SK&state=Saskatchewan">Saskatchewan Parks</a><br />
        <a href="?p=overview&id=YT&state=Yukon">Yukon Parks</a><br />
    </tr>
  </table>
  </div> -->

<?php }

function showStateTabs() { ?>
<br clear="all" />
<div id="state-tabs">
  <ul>
    <li class="active" rel="#state-overview">Overview</li>
    <li rel="#state-attractions">Tourist Attractions</li>
    <li rel="#state-rules">Rules of the Road</li>
    <li rel="#state-links">Additional Resources</li>
  </ul>
  <br clear="all" />
</div>
<?php }

function checkForDeal($nid) {
  $camp = node_load($nid);

  if ($camp->field_park_state_assn_optin[LANGUAGE_NONE][0]["value"] = "on") {

    // Check whether the state association has an active deal
    $assnDeal = checkAssnDeal($camp->field_camp_state_assnid[LANGUAGE_NONE][0]["value"]);
    if ($assnDeal == 1) {
      return 1;
    }
  }

  return 0;
}

function checkAssnDeal($assnID) {
  //echo "<span style='color:#ccc;font-size:0.7em;'> cad</span>";
  $userID = getUserID($assnID);
  $query = db_query("SELECT nid FROM {node} WHERE type = 'deal' AND uid = :uid", array('uid' => $userID));
  $result = 0;
  while ($row = $query->fetchAssoc()) {
    //echo "<span style='color:#ccc;font-size:0.7em;'> dl</span>";
    $times = getTimes($row["nid"]);
    //echo "<span style='color:#ccc;font-size:0.7em;'> " . $row['nid'] . " </span>";
    $startTime = strtotime(str_replace("T", " ", $times["start"]));
    $endTime = strtotime(str_replace("T", " ", $times["end"]));
    $timeNow = mktime();
    //echo "<span style='color:#ccc;font-size:0.7em;'> $startTime|$endTime|$timeNow</span>";

    if ($startTime < $timeNow && $endTime > $timeNow) {
      //echo "<span style='color:#ccc;font-size:0.7em;'> y</span>";
      $result = 1;
    }
  }
  return $result;
}

function getTimes($nid) {
  $query = db_query("SELECT field_deal_start_value, field_deal_end_value FROM {content_type_deal} WHERE nid = %d ORDER BY vid DESC LIMIT 1" , $nid);
  while ($row = db_fetch_array($query)) {
    $result["start"] = $row["field_deal_start_value"];
    $result["end"] = $row["field_deal_end_value"];
  }
  return $result;
}

function getUserID($name) {
  $query = db_query("SELECT uid FROM {users} WHERE name = :name LIMIT 1", array('name' => $name));
  while ($row = $query->fetchAssoc()) {
    $result = $row["uid"];
  }
  return $result;
}

?>