<?php
/**
 * Created by IntelliJ IDEA.
 * User: patricepaquette
 * Date: 2014-03-25
 * Time: 1:05 AM
 */
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML+RDFa 1.0//EN"
  "http://www.w3.org/MarkUp/DTD/xhtml-rdfa-1.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php print $language->language; ?>" version="XHTML+RDFa 1.0" dir="<?php print $language->dir; ?>"<?php print $rdf_namespaces; ?>>

<head profile="<?php print $grddl_profile; ?>">
  <?php print $head; ?>
  <title><?php print $head_title; ?></title>
  <?php print $styles; ?>
  <meta name="google-site-verification" content="uV16LQUNoMB-U4AaDyST8Sh3u97LAwKBS2h3yweC2x0" />
  <link href='http://fonts.googleapis.com/css?family=PT+Sans+Narrow' rel='stylesheet' type='text/css'>
  <link href='http://fonts.googleapis.com/css?family=Raleway:100' rel='stylesheet' type='text/css'>
  <link type="text/css" rel="stylesheet" media="all" href="/sites/all/themes/gca_new_interior/specific.css" />
  <link type="text/css" rel="stylesheet" media="all" href="/scripts/tablesorter/themes/green/style.css" />
  <link rel="stylesheet" href="http://static.jquery.com/ui/css/demo-docs-theme/ui.theme.css" type="text/css" media="all">
  <link href="/scripts/datatable/media/css/demo_page.css" rel="stylesheet" />
  <link href="/scripts/datatable/media/css/demo_table.css" rel="stylesheet" />

  <?php print $scripts; ?>
  <?php $random = rand(0,10000000); ?>
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script>
  <script src="/scripts/jquery-1.6.2.js" type="text/javascript"></script>

  <script type="text/javascript" src="<? echo url("/scripts/gca_jquery.js"); ?>"></script>
  <script type="text/javascript" src="<? echo url("/scripts/gca_jquery_front.js"); ?>"></script>
  <script type="text/javascript" "/scripts/jquery.gmap-1.1.0-min.js"></script>
  <script type="text/javascript" src="/scripts/datatable/media/js/jquery.dataTables.js"></script>
  <script type="text/javascript" src="/scripts/tablesorter/jquery.tablesorter.js"></script>


  <script type="text/javascript">
    var $gca = jQuery.noConflict();
    $gca(document).ready(function() {
      $gca("#state-table").tablesorter();
      $gca(document).ajaxComplete(function() {
        formatTable();
        copyResultsRange();
      });

      function copyResultsRange() {
        $gca("#results-range-top").html($gca("#results-table_info").html());
      }

      function formatTable() {
        $gca("#results-table").dataTable({
          "tPaginate": true,
          "sPaginationType": "full_numbers",
          "iDisplayLength": 20,
          "sDom": '<"wrapper"lfptip>',
          "aaSorting": [[ 4, "asc" ]]
        });
      }
    });
  </script>
</head>
<body class="<?php print $classes; ?>" <?php print $attributes;?>>
<div id="skip-link">
  <a href="#main-content" class="element-invisible element-focusable"><?php print t('Skip to main content'); ?></a>
</div>
<?php print $page_top; ?>
<?php print $page; ?>
<?php print $page_bottom; ?>
</body>
</html>