var $gca = jQuery.noConflict();
$gca(document).ready(function () {
    // Remove text from user login submit button so it doesn't show through the image
    $gca("#user-login #edit-submit").attr("value", "");

    // Set header background appropriate for the time of day
    var curDate = new Date();
    var hours = curDate.getHours();
    if (hours < 6 || hours >= 18) {
        // overnight
        $gca("#header").css("background-image", "url(/sites/default/themes/gca-test/images/backgrounds/header_bkgr_night.jpg)");
    } else if (hours >= 6 && hours < 12) {
        // morning
        $gca("#header").css("background-image", "url(/sites/default/themes/gca-test/images/backgrounds/header_bkgr_morning.jpg)");
    } else {
        // afternoon
        $gca("#header").css("background-image", "url(/sites/default/themes/gca-test/images/backgrounds/header_bkgr_afternoon.jpg)");
    }

    // Image rollover for state search map
    $gca("#search-map img").mouseover(function () {
        $gca(this).attr("src", "/sites/default/files/home_search_map_rollover.gif");
    });
    $gca("#search-map img").mouseout(function () {
        $gca(this).attr("src", "/sites/all/themes/gca_new/images/home_search_map.gif");
    });

    // New filter tabs on find a park page

    $gca('.asw-center-tab').live("click", function () {
        $gca('.asw-center-tab').removeClass("active");
        $gca(this).addClass("active");
        $gca('.filter-wrapper').hide();
        $gca($gca(this).attr("rel")).show();
    });

    $gca('#mini-advanced-search-widget .asw-center-tab').live("click", function () {
        $gca('#mini-advanced-search-widget .asw-center-tab').removeClass("active");
        $gca(this).addClass("active");
        $gca('#mini-advanced-search-widget .filter-wrapper').hide();
        $gca($gca(this).attr("rel")).show();
    });

    // Video Contest Submission playlist page
    $gca('.videopage-activate').click(function () {
        var targetVideo = ".video-" + $gca(this).attr("rel");
        $gca('.videopage-player').addClass('hide');
        $gca('.videopage-video').html('');
        $gca('.videopage-video').html('<iframe class="videopage-iframe" width="640" height="390" src="http://www.youtube.com/embed/' + $gca(this).attr("vid") + '" frameborder="0" allowfullscreen></iframe>');
        $gca(targetVideo).removeClass('hide');
    });

    // Tabs for GCA Search Page

    $gca('.search-tab').click(function () {
        $gca('.filter-div').addClass('hide');
        $gca($gca(this).attr("rel")).removeClass('hide');
        $gca('.search-li').removeClass('tabs-active');
        $gca('.search-li').addClass('tabs-default');
        $gca($gca(this).parent()).removeClass('tabs-default');
        $gca($gca(this).parent()).addClass('tabs-active');
        $gca($gca(this).attr("rel")).scrollbar();
    });

    $gca('#gca-search-header-tabs a').click(function () {
        $gca('.gca-search-loc-land').toggleClass("hide");
        if ($gca('.search-location-tab').hasClass("tabs-active")) {
            $gca('.search-location-tab').removeClass('tabs-active').addClass('tabs-default');
            $gca('.search-landmark-tab').removeClass('tabs-default').addClass('tabs-active');
        } else {
            $gca('.search-landmark-tab').removeClass('tabs-active').addClass('tabs-default');
            $gca('.search-location-tab').removeClass('tabs-default').addClass('tabs-active');
        }
    });

    $gca("#asw-filter").live("change", function () {
        $gca(".filter-wrapper").hide();
        $gca($gca(this).val()).show();
    });

    $gca("#asw-enlarge").live("click", function () {
        $gca("#search-map-wrapper").show();
        $gca("#asw-enlarge").toggleClass("hide");
        $gca("#asw-shrink").toggleClass("hide");
    });

    $gca("#asw-shrink").live("click", function () {
        $gca("#search-map-wrapper").hide();
        $gca("#asw-shrink").toggleClass("hide");
        $gca("#asw-enlarge").toggleClass("hide");
    });

    $gca('#search-landmarks li').click(function () {
        $gca('#search-landmarks li').removeClass("highlighted");
        $gca(this).addClass("highlighted");
        $gca('#gca-landmark').html($gca(this).attr("rel"));
        initiateSearch(0);
    });

    $gca("#advanced-search-widget #asw-tabs li").click(function () {
        $gca("#asw-tabs li").removeClass("selected");
        $gca(this).addClass("selected");
        $gca(".asw-tabs-box").hide();
        $gca($gca(this).attr("rel")).show();
    });

    $gca("#advanced-search-widget .fap-tab-general").click(function () {
        $gca(".asw-tabs-box").hide();
        $gca("#asw-tabs").removeClass();
        $gca("#asw-tabs").addClass("fap-tab-1");
        $gca("#asw-location").show();
        $gca("#search-box").removeClass();
        $gca("#search-box").addClass("gca-search-box");
    });
    $gca("#advanced-search-widget .fap-tab-parkname").click(function () {
        $gca(".asw-tabs-box").hide();
        $gca("#asw-tabs").removeClass();
        $gca("#asw-tabs").addClass("fap-tab-2");
        $gca("#asw-parkname").show();
        $gca("#search-box").removeClass();
        $gca("#search-box").addClass("gca-search-park-box");
    });
    $gca("#advanced-search-widget .fap-tab-landmarks").click(function () {
        $gca(".asw-tabs-box").hide();
        $gca("#asw-tabs").removeClass();
        $gca("#asw-tabs").addClass("fap-tab-3");
        $gca("#asw-landmarks").show();
    });

    $gca("#mini-advanced-search-widget .fap-tab-general").live("click", function () {
        $gca("#mini-advanced-search-widget .asw-tabs-box").show();
        $gca("#mini-advanced-search-widget #asw-tabs").removeClass();
        $gca("#mini-advanced-search-widget #asw-tabs").addClass("fap-tab-1");
        $gca("#mini-advanced-search-widget #asw-parkname").hide();
        $gca("#mini-advanced-search-widget #asw-location").show();
    });

    $gca("#mini-advanced-search-widget .fap-tab-parkname").live("click", function () {
        $gca("#mini-advanced-search-widget .asw-tabs-box").hide();
        $gca("#mini-advanced-search-widget #asw-tabs").removeClass();
        $gca("#mini-advanced-search-widget #asw-tabs").addClass("fap-tab-2");
        $gca("#mini-advanced-search-widget #asw-location").hide();
        $gca("#mini-advanced-search-widget #asw-parkname").show();
    });

    $gca('#mini-advanced-search-widget .fap-search-box-park').live("click", function () {
        if (!$gca('#mini-advanced-search-widget #search-park').val()) {
            alert("Please enter a park name or keyword.x");
        } else {
            var urlString = "/test/findpark?fap=1&p=" + $gca('#mini-advanced-search-widget #search-park').val() + "&o=p#search-area";
            _gaq.push(['_trackEvent', 'Buttons', 'Clicked', 'Search by Park Name', , false]);
            window.location = urlString;
        }
    });

    //

    $gca(document).ajaxComplete(function () {
        $gca(".paginate_button").live("click", function () {
            var tempString = $gca("#asw-right iframe").attr("src");
            urlSmallMap = tempString.replace(" #search-map-small", "&m=" + $gca(this).html() + " #search-map-small");
            newSmallIframe = '<iframe width="314" height="187" src="' + urlSmallMap + '" frameborder="0" scrolling="no"></iframe>';
        });
    });


    // Checkboxes on GCA Search Page


    $gca('.checkbox').click(function () {
        if ($gca(this).hasClass("highlighted")) {
            $gca(this).removeClass("highlighted");
            $gca(this).children().attr("src", "/sites/images/gca_checkbox_off.gif");
            var startString = $gca('#gca-search-terms').html();
            var toReplace = $gca(this).attr("rel") + "|";
            startString = startString.replace(toReplace, "");
            startString = startString.replace('"', "");
            $gca('#gca-search-terms').html(startString);
            //alert($gca('#gca-search-terms').html());
            //initiateSearch(0);
        } else {
            $gca(this).addClass("highlighted");
            $gca(this).children().attr("src", "/sites/images/gca_checkbox_on.gif");
            $gca('#gca-search-terms').append($gca(this).attr("rel") + "|");
            //alert($gca('#gca-search-terms').html());
            //initiateSearch(0);
        }
    });

    $gca(".removeOption").live("click", function () {
        var checkboxClass = ".checkbox" + $gca(this).attr("rel");
        $gca(checkboxClass).removeClass("highlighted");
        $gca(checkboxClass).children().attr("src", "/sites/images/gca_checkbox_off.gif");
        var startString = $gca('#gca-search-terms').html();
        var toReplace = $gca(this).attr("rel") + "|";
        startString = startString.replace(toReplace, "");
        startString = startString.replace('"', "");
        $gca('#gca-search-terms').html(startString);
        initiateSearch(0);
    });

    $gca("input#search-location").bind("keydown", function (event) {
        // track enter key
        var keycode = (event.keyCode ? event.keyCode : (event.which ? event.which : event.charCode));
        if (keycode == 13) { // keycode for enter key
            // force the 'Enter Key' to implicitly click the Update button
            _gaq.push(['_trackEvent', 'Buttons', 'Clicked', 'Find Park Search Button', , false]);
            window.location.href = "#search-area";
            initiateSearch(0);
            return false;
        } else {
            return true;
        }
    });
    $gca("input#search-location-mini").bind("keydown", function (event) {
        // track enter key
        var keycode = (event.keyCode ? event.keyCode : (event.which ? event.which : event.charCode));
        if (keycode == 13) { // keycode for enter key
            // force the 'Enter Key' to implicitly click the Update button

            initiateSearch(1);
            return false;
        } else {
            return true;
        }
    });
    $gca("#advanced-search-widget input#search-park").bind("keydown", function (event) {
        // track enter key
        var keycode = (event.keyCode ? event.keyCode : (event.which ? event.which : event.charCode));
        if (keycode == 13) { // keycode for enter key
            // force the 'Enter Key' to implicitly click the Update button
            if ($gca('#search-park').val() != "") {
                _gaq.push(['_trackEvent', 'Buttons', 'Clicked', 'Search by Park Name', , false]);
                window.location.href = "#search-area";
                $gca("#gca-search-throbber").show();
                $gca("#loading").show();
                //var park = $gca('#search-park').val().replace(/[^\w\s]|_/g, "").replace(/\s+/g, "+");
                var park = escape($gca('#search-park').val());
                var urlString = "/scripts/search_park_name.php?p=" + park + " #search-results";
                //alert(urlString);
                $gca('#gca-search-results').load(urlString,function () {
                    $gca("#gca-search-throbber").hide();
                    $gca("#loading").hide();
                }).show();
                $gca("#search-ads").show();
                $gca("#footer-leaderboard #search-ads").show();
                //alert(urlString);
            } else {
                alert('Please specify a park name.');
            }
            return false;
        } else {
            return true;
        }
    });
    $gca("#mini-advanced-search-widget input#search-park").bind("keydown", function (event) {
        // track enter key
        var keycode = (event.keyCode ? event.keyCode : (event.which ? event.which : event.charCode));
        if (keycode == 13) { // keycode for enter key
            // force the 'Enter Key' to implicitly click the Update button
            if (!$gca('#mini-advanced-search-widget #search-park').val()) {
                alert("Please enter a park name or keyword.x");
            } else {
                var urlString = "/findpark?fap=1&p=" + $gca('#mini-advanced-search-widget #search-park').val() + "&o=p#search-area";
                _gaq.push(['_trackEvent', 'Buttons', 'Clicked', 'Search by Park Name', , false]);
                window.location = urlString;
            }
        } else {
            return true;
        }
    });


    $gca('#advanced-search-widget .gca-search-box').live("click", function () {
        $gca("#loading").show();
        initiateSearch(0);
    });

    $gca('#mini-advanced-search-widget .gca-search-box').live("click", function () {
        initiateSearch(1);
    });

    $gca('#advanced-search-widget .gca-search-park-box').live("click", function () {
        if ($gca('#advanced-search-widget #search-park').val() != "") {
            $gca("#gca-search-throbber").show();
            $gca("#loading").show();
            //var park = $gca('#advanced-search-widget #search-park').val().replace(/[^\w\s]|_/g, "").replace(/\s+/g, "+");
            var park = escape($gca('#advanced-search-widget #search-park').val());
            var urlString = "/scripts/search_park_name.php?p=" + park + " #search-results";
            $gca("#state-start, #state-start-intro").hide();
            _gaq.push(['_trackEvent', 'Buttons', 'Clicked', 'Search by Park Name', , false]);
            //window.history.pushState("", "", urlString);
            $gca('#gca-search-results').load(urlString,function () {
                $gca("#gca-search-throbber").hide();
                $gca("#loading").hide();
            }).show();
            //alert(urlString);
            //Show ads
            $gca("#search-ads").show();
            $gca("#footer-leaderboard #search-ads").show();
        } else {
            alert('Please specify a park name.');
        }
    });

    $gca(".pagination-link").live('click', function () {
        var linkPath = "/scripts/search_results.php" + $gca(this).attr("rel") + " #search-results";
        var mapPath = "/scripts/search_results.php" + $gca(this).attr("rel") + "&smap=1";
        //alert(linkPath);
        $gca('#search-results').load(linkPath, function () {
            $gca('#search-map-wrapper').html("<iframe width='690' height='350' src='" + mapPath + "&hide=small#large-map' frameborder='0' scrolling='no'></iframe>");
            $gca('#asw-right').html("<iframe width='314' height='187' src='" + mapPath + "' frameborder='0' scrolling='no'></iframe>");
        });
    });

    $gca('#search-tabs-expand').click(function () {
        $gca('#search-tabs-expand').addClass("hide");
        $gca('#search-tabs-shrink').removeClass("hide");
        $gca('#gca-search-filter-wrapper').removeClass("hide");
    });
    $gca('#search-tabs-shrink').click(function () {
        $gca('#search-tabs-shrink').addClass("hide");
        $gca('#search-tabs-expand').removeClass("hide");
        $gca('#gca-search-filter-wrapper').addClass("hide");
    });

    // Find a Park search

    $gca('#advanced-search-widget .fap-search-box-location').click(function () {
        //alert('click');
        if (!$gca('#search-location').val()) {
            alert("Please enter a location.");
        } else {
            var urlString = "/test/findpark#search-top?fap=1&l=" + $gca('#search-location').val() + "&r=" + $gca('#search-distance').val() + "&o=l";
            _gaq.push(['_trackEvent', 'Buttons', 'Clicked', 'Find Park Search Button', , false]);
            window.location = urlString;
        }
    });

    $gca('#advanced-search-widget .fap-search-box-park').click(function () {
        if (!$gca('#advanced-search-widget #fap-search-park').val()) {
            alert("Please enter a park name or keyword.");
        } else {
            var urlString = "/test/findpark?fap=1&p=" + $gca('#advanced-search-widget #fap-search-park').val() + "&o=p" + "#search-area";
            //alert(urlString);
            _gaq.push(['_trackEvent', 'Buttons', 'Clicked', 'Search by Park Name', , false]);
            window.location = urlString;
        }
    });

    $gca("#park-tabs li").click(function () {
        var urlString = "?tab=" + $gca(this).attr("tab");
        window.location = urlString;
        //$gca("#park-tabs li").removeClass("selected");
        //$gca(this).addClass("selected");
        //$gca(".park-tab-content").addClass("hide");
        //$gca($gca(this).attr("rel")).removeClass("hide");
    });

    $gca(".park-video-playlist-item").click(function () {
        $gca(".park-video-player-title").html($gca(this).attr("title"));
        var embedPath = "http://www.youtube.com/embed/" + $gca(this).attr("rel");
        $gca(".videopage-iframe").attr("src", embedPath);
    });

    $gca(".photo-arrow").click(function () {
        $gca(".photo-wrapper").hide();
        $gca($gca(this).attr("rel")).show();
    });

    $gca(".photo-thumbnail").click(function () {
        $gca("#dark-overlay").show();
        $gca("#photo-slideshow-wrapper").show();
        var targetPic = $gca(this).attr("rel");
        $gca(targetPic).show();
    });

    $gca(".activate-map").click(function () {
        $gca("#dark-overlay").removeClass("hide");
        $gca("#youtube-close").removeClass("hide");
        $gca("#search-map-large").removeClass("hide");
    });

    $gca("#dark-overlay, #modal-close").live("click", function () {
        $gca("#youtube-close").addClass("hide");
        $gca("#dark-overlay").addClass("hide");
        $gca("#search-map-large").addClass("hide");
        $gca("#photo-slideshow-wrapper").addClass("hide");
    });

    $gca(".slideshow-close").click(function () {
        $gca("#dark-overlay").hide();
        $gca(".photo-wrapper").hide();
        $gca("#photo-slideshow-wrapper").hide();
    });

    if ($gca("#role").html()) {
        var role = $gca("#role").html().trim();
    } else {
        var role = "";
    }

    // GCA requested that the word Vocabulary on park edit pages be changed to Park Features. As this is a Drupal taxonomy module default, it's being changed via js.
    $gca(".vertical-tabs-list-taxonomy strong").html("Park Features");
    //$gca(".node-type-camp .vertical-tabs-list-taxonomy strong").html("Park Features");

    //GCA requested that the submenu on the profile edit page for state associations be hidden if the user is a state association.
    if (role == "state") {
        $gca(".page-user #body-center .secondary").html(" ");
    }

    //GCA requested that the "Node Weight" tab (default in Drupal) be suppressed
    //$gca(".vertical-tab-button.first").hide();
    $gca(".vertical-tabs-weight_form").hide();
    $gca(".vertical-tabs-group_contact_info").show();

    //Suppress Comment settings tab on park edit page
    $gca(".vertical-tabs-list-comment_settings").parent().hide();

    //Suppress Active Log tab on park page
    $gca(".activity-log").hide();

    //Suppress Printer version tab on park edit page
    $gca(".vertical-tabs-list-print").parent().hide();

    //Suppress Menu Settings tab on park edit page
    $gca(".vertical-tabs-list-menu").parent().hide();

    //Suppress Publishing Options tab on park edit page
    $gca(".vertical-tabs-list-options").parent().hide();

    //Suppress Revision information tab on park edit page
    if ($gca("#page-title").html() == "Create Park" || $gca("body").hasClass("node-type-state-association")) {
        $gca(".vertical-tabs-list-revision_information").parent().hide();
    }

    //Suppress wysiwyg editor on park edit pages
    if ($gca("body").hasClass("node-type-camp")) {
        $gca(".wysiwygToolbar").hide();
    }

    //Add solicitation beneath the log in form
    $gca("#user-login").append("<p></p><p>Are you a park owner but not a member? Find out how to join <a href='http://www.arvc.org' target='new'>here</a>.</p>");

    //Make Totals field readonly if user is "park owner"
    if (role == "park") {
        $gca("#edit-field-camp-totals-0-value").attr("readonly", "readonly").attr("style", "color:#CCCCCC");
    }

    // Remove tabs from park profile page for park owner users
    if (role == "park") {
        $gca(".page-user .tabs-wrapper").hide();
    }

    //On park creation form, suppress opt-in
    $gca("#edit-field-park-official-optin-value-wrapper").hide();

    //Suppress Discounts tab for GCA users
    if (role == "gca") {
        $gca(".vertical-tabs-list-group_discounts").parent().hide();
    }

    //Hide delete button on node edit form from park owner and state associations
    if (role == "park" || role == "state") {
        $gca("#edit-delete").hide();
    }

    // Hide view/edit links if logged in as State
    if (role == "state") {
        $gca(".page-user .tabs-wrapper").hide();
        $gca(".vertical-tabs-list-group_associations").parent().hide();
    }

    // Hide body field on camp creation form
    $gca("#edit-camp-node-form").each(function () {
        $gca(".body-field-wrapper").hide();
    });

    //Calculate sites fields on Park edit pages
    var rv = 0;

    rv = Math.floor(rv + $gca("#edit-field-camp-electric-water-0-value").val()) + Math.floor($gca("#edit-field-camp-electrical-0-value").val()) + Math.floor($gca("#edit-field-camp-full-hookups-0-value").val()) + Math.floor($gca("#edit-field-camp-no-hookups-0-value").val());

    $gca("#edit-field-camp-total-rv-calc-0-value").val(rv);

    var all = 0;
    if (!$gca("edit-field-camp-tents-0-value").val()) {
        $gca("edit-field-camp-tents-0-value").val("0");
    }
    all = all + rv + Math.floor($gca("#edit-field-camp-tents-0-value").val()) + Math.floor($gca("#edit-field-camp-rental-cabins-0-value").val()) + Math.floor($gca("#edit-field-camp-rental-trailers-0-value").val()) + Math.floor($gca("#edit-field-camp-teepee-0-value").val()) + Math.floor($gca("#edit-field-camp-yurts-0-value").val()) + Math.floor($gca("#edit-field-camp-other-0-value").val());

    $gca("#edit-field-camp-total-calc-0-value").val(all);
    if ($gca("#edit-field-camp-total-calc-0-value").val() == "NaN") {
        $gca("#edit-field-camp-total-calc-0-value").val("0");
    }


    // Recalculate when changes are made
    $gca("#edit-field-camp-full-hookups-0-value, #edit-field-camp-electric-water-0-value, #edit-field-camp-electrical-0-value, #edit-field-camp-no-hookups-0-value, #edit-field-camp-pull-throughs-0-value, #edit-field-camp-rental-cabins-0-value, #edit-field-camp-rental-trailers-0-value, #edit-field-camp-teepee-0-value, #edit-field-camp-yurts-0-value, #edit-field-camp-other-0-value, #edit-field-camp-tents-0-value").keyup(function () {

        var rv = Math.floor($gca("#edit-field-camp-electric-water-0-value").val()) + Math.floor($gca("#edit-field-camp-electrical-0-value").val()) + Math.floor($gca("#edit-field-camp-full-hookups-0-value").val()) + Math.floor($gca("#edit-field-camp-no-hookups-0-value").val());

        $gca("#edit-field-camp-total-rv-calc-0-value").val(rv);

        var all = rv + Math.floor($gca("#edit-field-camp-tents-0-value").val()) + Math.floor($gca("#edit-field-camp-rental-cabins-0-value").val()) + Math.floor($gca("#edit-field-camp-rental-trailers-0-value").val()) + Math.floor($gca("#edit-field-camp-teepee-0-value").val()) + Math.floor($gca("#edit-field-camp-yurts-0-value").val()) + Math.floor($gca("#edit-field-camp-other-0-value").val());

        $gca("#edit-field-camp-total-calc-0-value").val(all);

    });

    // Set "readonly" for totals fields on park edit pages
    $gca("#edit-field-camp-totals-0-value, #edit-field-camp-total-calc-0-value, #edit-field-camp-total-rv-calc-0-value").attr("readonly", "readonly").addClass("grayout");

    // Info tabs on park pages
    $gca("#park-info-tabs li").live("click", function () {
        $gca("#park-info-tabs li").removeClass("active");
        $gca(this).addClass("active");
        $gca("#park-info-amenities, #park-info-state").hide();
        var targetPane = $gca(this).attr("rel");
        $gca(targetPane).show();
    });

    // State tabs on findpark page
    $gca("#state-tabs li").live("click", function () {
        $gca("#state-tabs li").removeClass("active");
        $gca(this).addClass("active");
        $gca("#state-overview, #state-attractions, #state-rules, #state-links, #state-parks").hide();
        var targetPane = $gca(this).attr("rel");
        $gca(targetPane).show();
        if ($gca(this).attr("rel") == "#state-overview") {
            $gca("#state-parks").show();
        }
    });

    $gca("#review-tabs li").live("click", function () {
        $gca("#recent-reviews").hide();
        $gca(".review-park").hide();
        $gca("#review-tabs li").removeClass("active");
        $gca(this).addClass("active");
        var revTab = $gca(this).attr("rel");
        $gca(revTab).show();
    });

    // Add readonly to ARVC ID if user is park or state
    if ($gca("#role").html()) {
        if ($gca("#role").html().trim() == "park" || $gca("#role").html().trim() == "state") {
            $gca("#edit-field-camp-lagacy-id-0-value").attr("readonly", "readonly").addClass("grayout");
        }
    }

    function initiateSearch(searchType) {
        if (searchType == 1) {
            $gca("#mini-advanced-search-widget #gca-search-throbber").show();
        } else {
            $gca("#advanced-search-widget #gca-search-throbber").show();
            $gca("#loading").show();
        }
        // var locvalue = $gca('#search-location').attr("value");
        var urlString = "/findpark?fap=1&l=" + $gca('#search-location').val() + "&r=" + $gca('#search-distance').val() + "&t="+ $gca('#gca-search-terms').html() +"&o=l#search-area";
        window.history.pushState({}, "", urlString);

        $gca(window).trigger('GCASearchInitiated', {
            terms: $gca("#gca-search-terms").html(),
            location: $gca('#search-location').val(),
            radius: $gca('#search-distance').val(),
            searchType: searchType
        });
        if(showLoc) showLoc(searchType);
        $gca("#search-ads").show();
        $gca("#footer-leaderboard #search-ads").show();
    }

    function identifyState() {
        $gca('#page-subtitle').html($gca('#state-name').html());
    }
});
