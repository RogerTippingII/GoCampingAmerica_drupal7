<?php 
error_reporting(0);
//global $node;
?>

<!DOCTYPE html>
<html lang="<?php echo $language->language ?>" dir="<?php echo $language->dir ?>">

<head>
  <?php print $head ?>
  <title><?php print $head_title ?></title>
  <?php print $styles ?>
  <meta name="google-site-verification" content="uV16LQUNoMB-U4AaDyST8Sh3u97LAwKBS2h3yweC2x0" />
  <link href='http://fonts.googleapis.com/css?family=PT+Sans+Narrow' rel='stylesheet' type='text/css'>
  <link href='http://fonts.googleapis.com/css?family=Raleway:100' rel='stylesheet' type='text/css'>
  <link type="text/css" rel="stylesheet" media="all" href="/sites/all/themes/gca_new_interior/specific.css" />
  <link rel="stylesheet" href="http://static.jquery.com/ui/css/demo-docs-theme/ui.theme.css" type="text/css" media="all">
  <link rel="stylesheet" href="/sites/all/themes/common.css" type="text/css" media="all">
  <?php print $scripts ?>
  <script type="text/javascript">var switchTo5x=true;</script>
  <script type="text/javascript" src="http://w.sharethis.com/button/buttons.js"></script>
  <script type="text/javascript">stLight.options({publisher: "a96b3a4e-3904-4614-b324-ee46c4871385", doNotHash: false, doNotCopy: false, hashAddressBar: false});</script>
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js" type="text/javascript"></script>
  <script type="text/javascript">
    $(document).ready(function() {
	  $(".views-row").each(function() {
	    var imgWidth = $(this).find(".imagefield-field_blog_image").children("img").attr("width");
		var imgHeight = ($(this).find(".imagefield-field_blog_image").children("img").attr("height") * 125) / imgWidth;
		if (imgWidth && imgHeight) {
		  $(this).find(".views-field-field-blog-youtube-value").hide();
		  $(this).find(".imagefield-field_blog_image").children("img").attr("width", 125);
		  $(this).find(".imagefield-field_blog_image").children("img").attr("height", imgHeight);
		}
	  });
	});
  </script>
  <?php $random = rand(0,10000000); ?>
  <script type="text/javascript" src="/scripts/gca_jquery.js?r=<?php echo $random; ?>"></script>

  <!--[if lt IE 9]>
    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
  <script type="text/javascript">
  
  </script>
</head>

<body class="<?php print $body_classes; ?> <?php echo $node->field_page_body_class[0]["value"]; ?>">
  <div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=140359456133081";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
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
          <?php print render($page['headerRight']); ?>
        </div> <!-- #/header-body -->
      </div><!-- /#header-right -->
      <div id="header-right-cap"><img src="/sites/all/themes/gca_new_interior/images/home_nav_corner.gif" width="15" height="65" /></div><!-- /#header-right-cap -->
    </div> <!-- /#header-content -->
  </div> <!-- /#header-wrapper -->
  <div id="divider-bar">
    <div id="divider-content">
	<?php if ($node->type == "blog_post") {
	  echo "<h1 class='title' id='page-title'>" . getBlogCategory($node->field_blog_category[0]["value"]) . "&nbsp;&nbsp;<a href='/scripts/rss.php?c=" . $node->field_blog_category[0]["value"] . "'><img src='/sites/default/files/feed-icon-14x14.png' style='float:right;margin-right:32px;margin-top:12px;'	  /></a></h1>";
	} elseif  ($_SERVER["REQUEST_URI"] == "/blog") {
	  echo "<h1 class='title' id='page-title'>Blog&nbsp;&nbsp;<a href='/scripts/rss.php'><img src='/sites/default/files/feed-icon-14x14.png' style='float:right;margin-right:32px;margin-top:12px;'	  /></a></h1>";
	} else {
	  if (!empty($title)): ?><h1 class="title" id="page-title"><?php print $title ?></h1><?php endif; 
	}?>
    </div>
  </div> <!-- /#divider-bar -->
  <div id="body-wrapper" class="clearfix">
    <?php if (!empty($full)): ?><div id="full-width-content" class="clearfix"><?php print $full; ?></div><?php endif; ?>
    <div id="body-content" class="clearfix">
      <?php if ($page['sidebar_first']): ?>
      <div id="body-left">
        <?php print render($page['sidebar_first']); ?>
      </div><!-- /#body-left -->
      <?php endif; ?>
      <div id="body-center">
	    <br />
        <?php print $messages; ?>
        <?php if ($tabs): ?><div class="tabs-wrapper clearfix"><?php print render($tabs); ?></div><?php endif; ?>
        <?php if ($page['help']): print $help; endif; ?>
        <?php if ($page['contentTop']): print $contentTop; endif; ?>
        <?php if ($page['content']): print render($page['content']); endif; ?>
		<?php if ($node->type == "blog_post") { ?>
		<div class="blog-social-links">
		  <span class='st_sharethis_vcount' displayText='ShareThis'></span>
          <span class='st_facebook_vcount' displayText='Facebook'></span>
          <span class='st_twitter_vcount' displayText='Tweet'></span>
          <span class='st_linkedin_vcount' displayText='LinkedIn'></span>
          <span class='st_pinterest_vcount' displayText='Pinterest'></span>
          <span class='st_email_vcount' displayText='Email'></span>
		</div>
		<div class="blog-comments">
		  <div id="disqus_thread"></div>
    <script type="text/javascript">
        /* * * CONFIGURATION VARIABLES: EDIT BEFORE PASTING INTO YOUR WEBPAGE * * */
        var disqus_shortname = 'gocampingamericacom'; // required: replace example with your forum shortname

        /* * * DON'T EDIT BELOW THIS LINE * * */
        (function() {
            var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
            dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
            (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
        })();
    </script>
    <noscript>Please enable JavaScript to view the <a href="http://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
    <a href="http://disqus.com" class="dsq-brlink">comments powered by <span class="logo-disqus">Disqus</span></a>
    
		</div>
		<?php } ?>
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
	    <!-- <img src="/sites/all/themes/gca_new_300_ad/images/leaderboard.gif" width="728" height="90" /> -->

		<?php print render($page['leaderboard']); ?>
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
  $query = db_query('SELECT mlid, link_path, link_title, has_children FROM menu_links WHERE menu_name = \'main-menu\' AND plid = :parent AND hidden = 0 ORDER BY weight ASC', array(':parent' => $parent));
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

function getBlogCategory($value) {
  $query = db_query("SELECT name FROM {taxonomy_term_data} WHERE tid = :val LIMIT 1", array('val' => $value));
  while ($row = $query->fetchObject()) {
    $result = $row["name"];
  }
  if (isset($result)) {
    return $result;
  }
  return;
}
?>