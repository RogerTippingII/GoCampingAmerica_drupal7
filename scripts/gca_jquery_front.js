var $gca = jQuery.noConflict();
$gca(document).ready(function() {

  var slideNo = 1;
  var slideshow = setInterval(function() {
    var slideName = "#slide" + slideNo;
    var mainName = "#main" + slideNo;
    $gca(".slideshow").addClass("hide");
    $gca(slideName).removeClass("hide");
    if (slideNo == 3) {
      slideNo = 0;
    } else {
      slideNo++;
    }
  }, 5000);
 
  $gca(".slideshow-change").click(function() {
    $gca(".slideshow").addClass("hide");
    var targetSlide = $gca(this).attr("rel");
    $gca(targetSlide).removeClass("hide");
    clearInterval(slideshow);
  });
  
  $gca(".activate-youtube").click(function() {
    var htmlString = '<object width="500" height="308"><param name="movie" value="http://www.youtube.com/v/' + $gca(this).attr("rel") + '?version=3&amp;hl=en_US&amp;rel=0&amp;controls=1&amp;showinfo=0"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/' + $gca(this).attr("rel") + '?version=3&amp;hl=en_US&amp;rel=0&amp;controls=1&amp;showinfo=0&amp;autoplay=1" type="application/x-shockwave-flash" width="500" height="308" allowscriptaccess="always" allowfullscreen="true"></embed></object>';
    $gca("#youtube-embed").html(htmlString);
    $gca("#youtube-close").removeClass("hide");
    $gca("#youtube-embed").removeClass("hide");
    $gca("#dark-overlay-front").removeClass("hide");
  });
  
  $gca("#dark-overlay-front, #modal-close-front").live("click", function() {
    clearInterval(slideshow);
    $gca("#youtube-embed").html("");
    $gca("#youtube-embed").addClass("hide");
    $gca("#youtube-close").addClass("hide");
    $gca("#dark-overlay-front").addClass("hide");
    $gca("#search-map-large-wrapper").addClass("hide");
  });
  
  $gca("#youtube-close-button").live("click", function() {
    $gca("#youtube-embed").html("");
    $gca("#youtube-embed").addClass("hide");
    $gca("#youtube-close").addClass("hide");
    $gca("#dark-overlay-front").addClass("hide");
  });
  
  // Find a Park search
  
  $gca('.fap-search-box-location').click(function() {
    if(!$gca('#search-location').val()) {
      alert("Please enter a location.");
    } else {
      var urlString = "/findpark?fap=1&l=" + $gca('#search-location').val() + "&r=" + $gca('#search-distance').val() + "&o=l#search-area";
	  _gaq.push(['_trackEvent', 'Buttons', 'Clicked', 'Home Search Button',, false]);
      window.location = urlString;
    }
  });
  
  $gca(".home-search .fap-search-box").click(function() {
    if ($gca("#search-location").val()) {
			getCoordinates($gca("#search-location").val());
		  var geocodeaddition = "";
		  if (coordinates_lat != null && coordinates_lng != null) {
		  	geocodeaddition = "&geocode_lat=" + coordinates_lat + "&geocode_lng=" + coordinates_lng;
		  }
      var urlString = "/findpark?fap=1&l=" + $gca('#search-location').val() + "&r=" + $gca('#search-distance').val() + "&o=l" + geocodeaddition + "#search-area";
	  _gaq.push(['_trackEvent', 'Buttons', 'Clicked', 'Home Search Button',, false]);
      window.location = urlString;
    } else if ($gca("#search-location-park").val()) {
      var urlString = "/findpark?fap=1&p=" + $gca('#search-location-park').val() + "&o=p#search-area";
	  _gaq.push(['_trackEvent', 'Buttons', 'Clicked', 'Search by Park Name',, false]);
      window.location = urlString;
    } else {
      alert("Please enter either a location or a park name.");
    }
  });
  
  $gca('.fap-search-box-park').click(function() {
    if(!$gca('#fap-search-park').val()) {
      alert("Please enter a park name or keyword.");
    } else {
      var urlString = "/findpark?fap=1&p=" + $gca('#fap-search-park').val() + "&o=p#search-area";
	  _gaq.push(['_trackEvent', 'Buttons', 'Clicked', 'Search by Park Name',, false]);
      window.location = urlString;
    }
  });
  
  $gca(".activate-map-front").click(function() {
    $gca("#dark-overlay-front").removeClass("hide");
    $gca("#search-map-large-wrapper").removeClass("hide");
  });
  
  $gca("input#search-location").bind("keydown", function(event) {
      // track enter key
      var keycode = (event.keyCode ? event.keyCode : (event.which ? event.which : event.charCode));
      if (keycode == 13) { // keycode for enter key
         // force the 'Enter Key' to implicitly click the Update button
         if(!$gca('#search-location').val()) {
           alert("Please enter a location.");
         } else {
           getCoordinates($gca("#search-location").val());
           var geocodeaddition = "";
           if (coordinates_lat != null && coordinates_lng != null) {
             geocodeaddition = "&geocode_lat=" + coordinates_lat + "&geocode_lng=" + coordinates_lng;
           }

           var urlString = "/findpark?fap=1&l=" + $gca('#search-location').val() + "&r=" + $gca('#search-distance').val() + "&o=l" + geocodeaddition + "#search-area";
		   _gaq.push(['_trackEvent', 'Buttons', 'Clicked', 'Home Search Button',, false]);
           window.location = urlString;
		   return false;
         }
      } else  {
         return true;
      }
   });
   $gca("input#search-location-park").bind("keydown", function(event) {
      // track enter key
      var keycode = (event.keyCode ? event.keyCode : (event.which ? event.which : event.charCode));
      if (keycode == 13) { // keycode for enter key
         // force the 'Enter Key' to implicitly click the Update button
         if ($gca("#search-location").val()) {
           var urlString = "/findpark?fap=1&l=" + $gca('#search-location').val() + "&r=" + $gca('#search-distance').val() + "&o=l#search-area";
           _gaq.push(['_trackEvent', 'Buttons', 'Clicked', 'Search by Park Name',, false]);
		   window.location = urlString;
         } else if ($gca("#search-location-park").val()) {
           var urlString = "/findpark?fap=1&p=" + $gca('#search-location-park').val() + "&o=p#search-area";
		   _gaq.push(['_trackEvent', 'Buttons', 'Clicked', 'Search by Park Name',, false]);
           window.location = urlString;
         } else {
           alert("Please enter either a location or a park name.");
         }
      }
   });
  
});
