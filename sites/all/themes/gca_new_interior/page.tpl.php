<?php 
//error_reporting(0);
?>

<!--<!DOCTYPE html>-->
<!--<html lang="--><?php //echo $language->language ?><!--" dir="--><?php //echo $language->dir ?><!--">-->
<body class="<?php print $body_classes; ?> <?php echo $node->field_page_body_class[0]["value"]; ?>">
  <?php print $page_top; ?>
  <div id="role" style="display:none;">
  <?php
  if ($user->roles[7] == "state") {
    echo "state";
  } elseif ($user->roles[5] == "park owner") {
    echo "park";
  } elseif ($user->roles[6] == "gca") {
    echo "gca";
  }
  ?>
  </div>
  <div id="dark-overlay" class="hide"></div>
  <div id="header-wrapper" class="clearfix">
    <div id="header-content" class="clearfix">
      <div id="header-left">
        <a href="/"><img src="/sites/all/themes/gca_new_interior/images/spacer.gif" width="193" height="240" alt="Home" /></a>
      </div><!-- /#header-left -->
      <div id="header-right">
        <div id="header-nav">
          <div id="header-home-link">
            <a href="/"><img src="/sites/all/themes/gca_new_interior/images/home_icon.png" alt="Home" /></a>
          </div>
          <?php displayNav($user); ?>
        </div> <!-- #/header-nav -->
        <div id="header-body">
          <?php print render($page['headerRight']);?>
        </div> <!-- #/header-body -->
      </div><!-- /#header-right -->
      <div id="header-right-cap"><img src="/sites/all/themes/gca_new_interior/images/home_nav_corner.gif" width="15" height="65" /></div><!-- /#header-right-cap -->
    </div> <!-- /#header-content -->
  </div> <!-- /#header-wrapper -->
  <div id="divider-bar">
    <div id="divider-content"><?php if (!empty($title)): ?><h1 class="title" id="page-title"><?php print $title ?></h1><?php endif; ?>
    </div>
  </div> <!-- /#divider-bar -->
  <div id="body-wrapper" class="clearfix">
    <?php if (!empty($page['full'])): ?><div id="full-width-content" class="clearfix"><?php print render($page['full']); ?></div><?php endif; ?>
    <div id="body-content" class="clearfix">
      <?php if ($page['sidebar_first']): ?>
      <div id="body-left">
        <?php print render($page['sidebar_first']); ?>
      </div><!-- /#body-left -->
      <?php endif; ?>
      <div id="body-center">
        <?php print $messages; ?>
<!--        --><?php //if ($tabs): ?><!--<div class="tabs-wrapper clearfix">--><?php //print render($tabs);?><!--</div>--><?php //endif; ?>
        <?php print render($page["help"]); ?>
        <?php print render($page["contentTop"]); ?>
        <?php print render($page["content"]); ?>
      </div>
	  <!-- /#body-center -->
      <?php if ($page['sidebar_second']): ?>
      <div id="body-right">
        <?php print render($page['sidebar_second']); ?>
      </div><!-- /#body-right -->
      <?php endif; ?>
    </div> <!-- /#body-content -->
  </div> <!-- /#body-wrapper -->
  <div id="footer-wrapper" class="clearfix">
    <div id="footer-content" class="clearfix">
	  <img src="/sites/all/themes/gca_new_300_ad/images/footer_rule.gif" width="960" height="2" style="padding:20px 20px 20px 20px;" />
	  <div id="footer-leaderboard">
		<?php print render($page["leaderboard"]); ?>Nthem
	  </div> <!-- /footer-leaderboard -->
      <div id="footer-left"><?php if ($page['footerLeft']): print render($page['footerLeft']); endif; ?></div> <!-- /#footer-left -->
      <div id="footer-right"><?php if ($page['footerRight']): print render($page['footerRight']); endif; ?></div> <!-- /#footer-right -->
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
	// 3779 = State association links (w/deal creation permission)
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
  $rows = db_query('SELECT mlid, link_path, link_title, has_children FROM menu_links WHERE menu_name = \'main-menu\' AND plid = :parent AND hidden = 0 ORDER BY weight ASC', array(':parent' => $parent));

  $x = 0;
  while($row = $rows->fetchObject()) {
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