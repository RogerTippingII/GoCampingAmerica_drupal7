<?php
require_once(__DIR__ . "/../../gca-search/repositories/ParkRepository.php");
$vocabs = array("amenities", "credit-cards", "recreation", "lifestyles", "site-options", "services", "affiliation");
$terms = ParkRepository::GetTaxonomies();
?>


<div id="mini-advanced-search-widget" class="ui-corners-all hide">
  <div id="state-breadcrumb"><a href="/findpark">Find a Park</a> -> <?php echo $_REQUEST["state"]; ?></div>
  <div id="asw-left" class="ui-corners-all">
    <div id="asw-tabs" class="fap-tab-1">
      <img src="/sites/all/themes/gca_new_interior/images/spacer.gif" width="114" height="26" class="fap-tab-general"><img src="/sites/all/themes/gca_new_interior/images/spacer.gif" width="114" height="26" class="fap-tab-parkname"><!-- <img src="/sites/all/themes/gca_new_interior/images/spacer.gif" width="114" height="26" class="fap-tab-landmarks" /> -->
    </div>
    <div id="asw-location" class="asw-tabs-box">
      <div id="location-label">Enter Location</div>
      <div id="location-label-note">Enter an address, city, state or landmark name</div>
      <input type="text" name="location" id="search-location-mini" class="ui-corners-all" />
      <div id="distance-wrapper">
        <select name="distance" id="search-distance-mini">
          <option value=10>Within 10 miles</option>
          <option value=15>Within 15 miles</option>
          <option value=20>Within 20 miles</option>
          <option value=25>Within 25 miles</option>
          <option value=50>Within 50 miles</option>
          <option value=75>Within 75 miles</option>
          <option value=100>Within 100 miles</option>
          <option value=150>Within 150 miles</option>
          <option value=200>Within 200 miles</option>
        </select>
      </div> <!-- distance-wrapper -->
      <div class="gca-search-box"><img src="/sites/all/themes/gca_new_interior/images/fap_search.gif" /></div>
      <div id="gca-search-throbber"><img src="/sites/default/files/indicator.gif" width="16" height="16" alt="Searching..." /></div>
    </div> <!-- asw-location -->
    <div id="asw-parkname" class="asw-tabs-box hide">
      <div id="state-id" class="hide"></div>
      <div id="location-label" style="margin:0;">Search by Park Name</div>
      <div id="location-label-note">Enter a Park Name</div>
      <input type="text" name="park" id="search-park" class="ui-corners-all">
      <div class="fap-search-box-park"  style="margin:5px 0 0 0;"><img src="/sites/all/themes/gca_new_interior/images/fap_search.gif" /></div>
      <div id="gca-search-throbber" class="hide"><img src="/sites/default/files/indicator.gif" width="16" height="16" alt="Searching..." /></div>
    </div> <!-- asw-parkname -->
  </div> <!-- asw-left -->
  <div id="asw-center" class="ui-corners-all">
    <div id="asw-center-left">
      <div class="asw-center-tab active" style="border-top:1px solid lightGrey;border-top-left-radius: 6px 6px;" rel="#mini-advanced-search-widget #amenities">Amenities</div>
      <div class="asw-center-tab" rel="#mini-advanced-search-widget #credit-cards">Credit Cards</div>
      <div class="asw-center-tab" rel="#mini-advanced-search-widget #lifestyles">Lifestyles</div>
      <div class="asw-center-tab" rel="#mini-advanced-search-widget #site-options">Site Options</div>
      <div class="asw-center-tab" rel="#mini-advanced-search-widget #recreation">Recreation</div>
      <div class="asw-center-tab" rel="#mini-advanced-search-widget #services">Services</div>
      <div class="asw-center-tab" style="border-bottom-left-radius: 6px 6px;" rel="#mini-advanced-search-widget #affiliation">Affiliation</div>
    </div>
    <div id="asw-filters" class="asw-center-right"><?php
      foreach ($vocabs as $vocab) {
        switch ($vocab) {
          case "affiliation":
            $y = 1;
            break;
          case "amenities":
            $y = 2;
            break;
          case "credit-cards":
            $y = 3;
            break;
          case "lifestyles":
            $y = 6;
            break;
          case "site-options":
            $y = 18;
            break;
          case "recreation":
            $y = 5;
            break;
          case "services":
            $y = 17;
            break;
          default:
            $y = 0;
            break;
        }
        echo '<div class="filter-wrapper';
        if ($y != 2) {
          echo ' hide';
        }
        echo '" id="' . $vocab . '" '; //class="filter-div'
        echo ">\n";
        echo '<div class="gca-search-column">';
        foreach ($terms as $key => $value) {
          if($value["vid"] == $y){
            echo "<li class='checkbox checkbox" . $value["tid"] . "' rel='" . $value["tid"] . "'><img src='/sites/images/gca_checkbox_off.gif' width='19' height='15' /> " . $value["term_name"] . "</li>\n";
          }
        } // terms foreach
        echo "</div><br clear='all' /></div>\n";
      } ?>
    </div>
  </div>
  <br clear="all" />
</div>