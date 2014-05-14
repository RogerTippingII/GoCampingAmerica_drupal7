<script type="text/javascript">

	function getgeocodeaddition() {
	  var geocodeaddition = "";
	  var tlocation = jQuery('#search-location-widget').val().replace(/[^\w\s]|_/g, "").replace(/\s+/g, "+");
		if (tlocation != '') {
			getCoordinates(tlocation);
		  if (coordinates_lat != null && coordinates_lng != null) {
		  	geocodeaddition = "&geocode_lat=" + coordinates_lat + "&geocode_lng=" + coordinates_lng;
		  }
		}
		return geocodeaddition;
	}
	
  $gca(document).ready(function() {
// Find a Park search

    $gca('.fap-search-box-location-widget').click(function() {
      if(!$gca('#search-location-widget').val()) {
        alert("Please enter a location.");
      } else {
      	geocodeaddition = getgeocodeaddition();
        var urlString = "/findpark?fap=1&l=" + $gca('#search-location-widget').val() + "&r=" + $gca('#search-distance').val() + "&o=l" + geocodeaddition + "#search-area";
        _gaq.push(['_trackEvent', 'Buttons', 'Widget Search Button', 'Clicked',, false]);
        window.location = urlString;
      }
    });

    $gca(".fap-search-box-widget").click(function() {
      if ($gca("#search-location-widget").val()) {
      	geocodeaddition = getgeocodeaddition();
        var urlString = "/findpark?fap=1&l=" + $gca('#search-location-widget').val() + "&r=" + $gca('#search-distance').val() + "&o=l" + geocodeaddition + "#search-area";
        _gaq.push(['_trackEvent', 'Buttons', 'Widget Search Button', 'Clicked',, false]);
        window.location = urlString;
      } else if ($gca("#search-location-park-widget").val()) {
        var urlString = "/findpark?fap=1&p=" + $gca('#search-location-park-widget').val() + "&o=p#search-area";
        _gaq.push(['_trackEvent', 'Buttons', 'Search by Park Name', 'Clicked',, false]);
        window.location = urlString;
      } else {
        alert("Please enter either a location or a park name.");
      }
    });

    $gca('.fap-search-box-park-widget').click(function() {
      if(!$gca('#fap-search-park-widget').val()) {
        alert("Please enter a park name or keyword.");
      } else {
        var urlString = "/findpark?fap=1&p=" + $gca('#fap-search-park-widget').val() + "&o=p#search-area";
        _gaq.push(['_trackEvent', 'Buttons', 'Search by Park Name', 'Clicked',, false]);
        window.location = urlString;
      }
    });

    $gca("input#search-location-widget").bind("keydown", function(event) {
      // track enter key
      var keycode = (event.keyCode ? event.keyCode : (event.which ? event.which : event.charCode));
      if (keycode == 13) { // keycode for enter key
        // force the 'Enter Key' to implicitly click the Update button
        if(!$gca('#search-location-widget').val()) {
          alert("Please enter a location.");
        } else {
        	geocodeaddition = getgeocodeaddition();
          var urlString = "/findpark?fap=1&l=" + $gca('#search-location-widget').val() + "&r=" + $gca('#search-distance').val() + "&o=l" + geocodeaddition + "#search-area";
          _gaq.push(['_trackEvent', 'Buttons', 'Widget Search Button', 'Clicked',, false]);
          window.location = urlString;
          return false;
        }
      } else  {
        return true;
      }
    });
    $gca("input#search-location-park-widget").bind("keydown", function(event) {
      // track enter key
      var keycode = (event.keyCode ? event.keyCode : (event.which ? event.which : event.charCode));
      if (keycode == 13) { // keycode for enter key
        // force the 'Enter Key' to implicitly click the Update button
        if ($gca("#search-location-widget").val()) {
        	geocodeaddition = getgeocodeaddition();
          var urlString = "/findpark?fap=1&l=" + $gca('#search-location-widget').val() + "&r=" + $gca('#search-distance').val() + "&o=l" + geocodeaddition + "#search-area";
          _gaq.push(['_trackEvent', 'Buttons', 'Widget Search Button', 'Clicked',, false]);
          window.location = urlString;
        } else if ($gca("#search-location-park-widget").val()) {
          var urlString = "/findpark?fap=1&p=" + $gca('#search-location-park-widget').val() + "&o=p#search-area";
          _gaq.push(['_trackEvent', 'Buttons', 'Search by Park Name', 'Clicked',, false]);
          window.location = urlString;
        } else {
          alert("Please enter either a location or a park name.");
        }
      }
    });
  });
</script>
<style type="text/css">
  #advanced-search-widget {
    margin-left:10px;
    height:120px;
  }
  #search-location-park-widget {
    border:1px solid #CCC;
    float:left;
    margin-right:7px;
  }
</style>
<div id="advanced-search-widget" class="home-search">
  <div id="asw-left">
    <div id="asw-tabs" class="fap-tab-1">
      <img src="/sites/all/themes/gca_new_interior/images/spacer.gif" width="114" height="26" class="fap-tab-general" /><img src="/sites/all/themes/gca_new_interior/images/spacer.gif" width="114" height="26" class="fap-tab-parkname" /><!-- <img src="/sites/all/themes/gca_new_interior/images/spacer.gif" width="114" height="26" class="fap-tab-landmarks" /> -->
    </div>
    <div id="asw-location" class="asw-tabs-box">
      <div class="search-label"><b>Enter Search Terms</b></div>
      <div class="search-label" style="font-size:0.8em;margin-bottom:7px;">Enter an address, city, state or landmark name</div>
      <input type="text" name="location" id="search-location-widget" class="ui-corners-all" />
      <div id="distance-wrapper" style="padding-top:4px;margin-right:5px;">
        <select name="distance" id="search-distance">
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
      <!-- <div id="official-optin" class="search-label"><input type="checkbox" name="official" value="yes" id="optin" /> Show only parks honoring GCA promotions</div> -->
    </div> <!-- asw-location -->
    <div id="asw-parkname" class="asw-tabs-box hide">
      <div class="search-label"><b>Enter a Park Name</b></div>
      <input type="text" name="park" id="search-location-park-widget" class="ui-corners-all" style="width:275px;" />
    </div> <!-- asw-parkname -->
    <div id="asw-landmarks" class="asw-tabs-box hide">
      <!-- <div id="location-label">Select a Landmark</div> -->
      <div id="landmark-wrapper">
        <div class="search-label">Select a Featured Landmark</div>
        <ul id="search-landmarks" class="ui-corners-all"><?php
          foreach (getLandmarks() as $landmark) {
            echo "<li rel='" . $landmark[coords] . "'>" . $landmark[title] . "</li>";
          }
          ?></ul>
        <div id="distance-wrapper">
          <select name="distance" id="search-landmark-distance">
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
      </div> <!-- landmark wrapper -->
    </div> <!-- asw-landmarks -->
    <div id="search-box" style="margin-top:10px;">
      <img src="/sites/all/themes/gca_new_interior/images/fap_search.gif" class="fap-search-box-widget" style="cursor:pointer;float:left;margin-right:5px;" /><a href="/findpark" style="float:left;padding-top:10px;">Advanced</a>
    </div>
  </div> <!-- asw-left -->
  <br clear="all" />
</div> <!-- adv search widget -->
<?php

function getLandmarks() {
  $query = db_query("SELECT n.nid, n.title, ctl.field_landmark_latitude_value as latitude, ctl.field_landmark_longitude_value as longitude FROM {node} n, {content_type_landmarks} ctl WHERE n.nid = ctl.nid AND n.type = 'landmarks' AND n.status = 1 ORDER BY n.title ASC");
  $x = 0;
  while ($row = $query->fetchObject()) {
    $landmarks[$x][nid] = $row->nid;
    $landmarks[$x][title] = $row->title;
    $landmarks[$x][coords] = $row->longitude . "|" . $row->latitude;
    $x++;
  }
  return $landmarks;
}

?>