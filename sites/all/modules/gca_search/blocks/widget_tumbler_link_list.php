<style type="text/css">
  ol {
    list-style:none;
    margin:0;
    padding:0;
  }
  .tumblr_title, .tumblr_link {
    font-size:1.2em;
    font-weight:bold;
  }
  .tumblr_post {
    background-color:#FFF;
    border:1px solid #CCC;
    border-bottom-left-radius: 6px 6px;
    border-bottom-right-radius: 6px 6px;
    border-top-left-radius: 6px 6px;
    border-top-right-radius: 6px 6px;
    padding:20px;
    margin-bottom:10px;
    clear:both;
  }
  .tumblr_video, .tumblr_photo {
    float:left;
    margin:0 20px 10px 0;
  }
</style>
<div style="font-size:1.6em;font-weight:bold;margin-bottom:20px;"><a href="http://gocampingamerica.tumblr.com/">GCA Links via Tumblr</a></div>
<div style="font-size:1.2em;font-weight:bold;margin-bottom:12px;"><p>A list of links, photos and videos to inspire new adventures. This list is powered by our <a href="http://gocampingamerica.tumblr.com/">Tumblr account.</a></p></div>
<div id="tumblr_data">
  <?php
  $toReplace = array("document.write('", "');", "0x22");
  $replacement = array("", "", "test");
  $tumblrScript = str_replace($toReplace, $replacement, clean_up(file_get_contents("http://gocampingamerica.tumblr.com/js")));
  echo $tumblrScript;
  ?>
</div>
<div style="font-size:1.2em;font-weight:bold;margin-bottom:12px;"><p>See more links, photos and videos on our <a href="http://gocampingamerica.tumblr.com/">Tumblr account.</a></p></div>
<?php
function clean_up($str){
  $str = stripslashes($str);
  $str = str_replace(array("x22", "x3c", "x3e", "x0a", "x26", "#8220;", "#8221;", "\"#8217;", "\"#039;", "</li>"), array('"', "<", ">", "", "\"", "", "", "'", "'", "<br clear=\"all\"></li>"), $str);
  return $str;
}
?>