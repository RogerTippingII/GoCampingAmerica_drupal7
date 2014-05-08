<html>
<head>
</head>
<body>
<?php
$value = $_REQUEST["id"];
$scriptPath = "http://travel.guestrated.com/Widget/SearchResultRating.ashx?custtypeid=8&portalid=3&customerid=" . $value;
//$scriptPath = "http://ratings.guestrated.com/RatingsSummary.ashx?portalid=61&customeri
d=" . $value;
?>
<script type="text/javascript" language="javascript" src="<?php echo $scriptPath; ?>"></script>
<div id="SearchResultRating" style="width:200px;height:50px; border:solid 0px red;"></div>
</body>
</html>