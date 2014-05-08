<?php /* page.tpl.php for GCA_New_300_Ad_Home */ ?>
<?php
include_once(__DIR__ .'/../../../../scripts/Mobile_Detect.php');

$detect = new Mobile_Detect();

if ($detect->isMobile()) {
  $mobileMsg = "mobile detected";
  // Check for cookie that indicates user wants full site instead of mobile site
  if (isset($_COOKIE["mbl"])) {
    if ($_COOKIE["mbl"] != "no") {
      $mobileMsg .= " | no cookie found";
      header('Location: http://mobile.gocampingamerica.com/');
    } else {
      if (isset($_REQUEST["m"])) {
        // User is coming from mobile site and requested regular site
	    $expTime = mktime() + 86400;
	    setcookie("mbl", "no", $expTime);
	    $mobileMsg .= " | cookie found";
      }
    }
  } else {
    if (!isset($_REQUEST["m"])) {
      header('Location: http://mobile.gocampingamerica.com/');
    }
  }
}

?>
<!DOCTYPE html>
<html lang="<?php echo $language->language ?>" dir="<?php echo $language->dir ?>">

<head>
  <?php print $head ?>
  <title><?php print $head_title ?></title>
  <?php print $styles ?>
  <link href='http://fonts.googleapis.com/css?family=PT+Sans+Narrow' rel='stylesheet' type='text/css'>
  <link href='http://fonts.googleapis.com/css?family=Raleway:100' rel='stylesheet' type='text/css'>
  <?php print $scripts ?>
  <meta name="google-site-verification" content="uV16LQUNoMB-U4AaDyST8Sh3u97LAwKBS2h3yweC2x0" />
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js" type="text/javascript"></script>
  <!--[if lt IE 9]>
    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
</head>

<body class="<?php print $body_classes; ?>">
  <div id="dark-overlay-front" class="hide"></div>
    <div id="youtube-wrapper">
      <div id="youtube-close" class="hide"><a name="youtube-video"></a><img src="/sites/all/themes/gca_new/images/icon-close.png" align="right" id="youtube-close-button" /></div>
	  <div id="youtube-embed" class="hide"></div>
    </div>
  <!-- </div> -->
  <div id="header-wrapper" class="clearfix">
    <div id="header-content" class="clearfix">
      <div id="header-left"><?php print render($page['headerLeft']); ?></div><!-- /#header-left -->
      <div id="header-right">
        <div id="header-nav">
          <div id="header-home-link"><img src="/sites/all/themes/gca_new/images/home_icon.png" alt="Home" /></div>

          <?php displayNav($user); ?>
        </div> <!-- #/header-nav -->
        <div id="header-body">
          <?php print render($page['headerRight']);?>
        </div> <!-- #/header-body -->
      </div><!-- /#header-right -->
      <div id="header-right-cap"><img src="/sites/all/themes/gca_new/images/home_nav_corner.gif" width="15" height="65" /></div><!-- /#header-right-cap -->
    </div> <!-- /#header-content -->
  </div> <!-- /#header-wrapper -->
  <div id="divider-bar">
    <div id="divider-content">
      <a href="/findpark"><img src="/sites/all/themes/gca_new/images/hp_find_a_park.gif" width="383" height="48" alt="Find a Park" /></a>
    </div>
  </div> <!-- /#divider-bar -->
  <div id="body-wrapper" class="clearfix">
    <div id="body-content" class="clearfix">
      <?php if ($page['sidebar_first']): ?>
      <div id="body-left">
        <?php print render($page['sidebar_first']); ?>
      </div><!-- /#body-left -->
      <?php endif; ?>
      <div id="body-center">
        <?php print $messages; ?>
        <?php if ($tabs): ?><div class="tabs-wrapper clearfix"><?php print render($tabs);?></div><?php endif; ?>
        <?php print render($page["help"]); ?>
        <?php print render($page["contentTop"]); ?>
        <?php print render($page["content"]); ?>
      </div>
      <?php if ($page['sidebar_second']): ?>
        <div id="body-right">
          <?php print render($page['sidebar_second']); ?>
      </div><!-- /#body-right -->
      <?php endif; ?>
    </div> <!-- /#body-content -->
  </div> <!-- /#body-wrapper -->
  <div id="footer-wrapper" class="clearfix">
    <div id="footer-content" class="clearfix">
      <div id="footer-left"><?php if ($page['footerLeft']): print render($page['footerLeft']); endif; ?></div> <!-- /#footer-left -->
      <div id="footer-right"><?php if ($page['footerRight']): print render($page['footerRight']); endif; ?></div> <!-- /#footer-right --><!-- /#footer-right -->
    </div>  <!-- /#footer-content -->
  </div> <!-- /#footer-wrapper -->
  <div id="copyright-wrapper">
    <div id="copyright-content" class="clearfix">
      © Copyright <?php echo date("Y", mktime()); ?> Go Camping America
    </div> <!-- /#copyright-content -->
  </div> <!-- /#copyright-wrapper -->
  <?php print $closure ?>
  <?php print $page_bottom; ?>
</body>
</html>
<?php
function displayNav($user) {
  $links = getNavLinks(0);
  echo "<ul id='nav' class='links primary-links clearfix'>";    
  foreach ($links as $link) {
    // Display links, but only do the appropriate Park Owner Links
	// 3490 = Log in
    // 3605 = Park owner links
    // 3638 = State association links
    if ($link["mlid"] != 3490 && $link["mlid"] != 3605 && $link["mlid"] != 3638 && $link["mlid"] != 3779) {
	  showLink($link);
    } else {
	  echo "<!-- test " . $link['mlid'] . " -->";
	  if ($user->roles[1] && $link["mlid"] == 3490) {
	    // User is anonymous. Show log in link
		showLink($link);
	  }
	  if ($user->roles[7] && $link["mlid"] == 3638 && $user->name != "35003") {
	    // User is State (not California). Show state assn menu w/o "add deal"
	    showLink($link);
	  }
	  if ($user->roles[7] && $link["mlid"] == 3779 && $user->name == "35003") {
	    // User is State (California). Show state assn menu with "add deal".
	    showLink($link);
	  }
	  if (($user->roles[4] || $user->roles[5] || $user->roles[6]) && $link["mlid"] == 3605) {
	    // User is dev, park or gca.
		showLink($link);
	  }
	}
  }
  echo "</li>";
  echo "</ul>";
}

function showLink($link) {
  echo "<li id='link" . $link["mlid"] . "' class='level1'><a href='";
  if (substr(drupal_get_path_alias($link["path"]), 0, 4) != "http") {
    echo "";
  }
  echo url(drupal_get_path_alias($link["path"])) . "'>" . $link["title"] . "</a>";
  if (count($link["children"]) != 0) {
    echo "<ul class='header-subnav' style='position:absolute;z-index:2;background:#bdd39e;'>";
    foreach ($link["children"] as $sublink) {
      echo "<li><a href='" . url(drupal_get_path_alias($sublink["path"])) . "'>" . $sublink["title"] . "</a>";
      if (count($sublink["children"]) != 0) {
        echo "<ul>";
        foreach($sublink["children"] as $trilink) {
          echo "<li><a href='" . url(drupal_get_path_alias($trilink["path"])) . "'>" . $trilink["title"] . "</a></li>";
        }
        echo "</ul>";
      }
      echo "</li>";
    }
    echo "</ul>";
  }
}

function getNavLinks($parent) {
  $query = db_query("SELECT mlid, link_path, link_title, has_children FROM menu_links WHERE menu_name = 'main-menu' AND plid = :parent AND hidden = 0 ORDER BY weight ASC", array("parent" => $parent));
  $x = 0;
  while ($row = $query->fetchObject()) {
    $link[$x]["mlid"] = $row->mlid;
    $link[$x]["path"] = $row->link_path;
    $link[$x]["title"] = $row->link_title;
    if ($row->has_children != 0) {
      $link[$x]["children"] = getNavLinks($link[$x]["mlid"]);
    }
    $x++;
  }
  return $link;
}
?>