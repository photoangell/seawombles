<?php
/* PHP file to generate a Google Map using version 3 of the javascript API.
  It will place a marker where the last location update was, and draw polylines
  to all other location updates. You can specify how many location updates
  are kept in the bbg_recv.php file.
  jay@summet.com
*/
date_default_timezone_set('Europe/London');
$name = "Sea Wombles";
$logfile = "/home/shonen/seawombles.x50.com/position.cur";

function speed($lat1, $lon1, $lat2, $lon2, $time1, $time2, $unit) { 
  $theta = $lon1 - $lon2; 
  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)); 
  $dist = acos($dist); $dist = rad2deg($dist); 
  $miles = $dist * 60 * 1.1515; 
  $unit = strtoupper($unit); 
  if ($unit == "K") { 
    $miles = ($miles * 1.609344); } 
  else if ($unit == "N") { 
    $miles = ($miles * 0.8684); } 
  
  
  
  $speed = $time2 - $time1;
  $speed = 3600 / $speed;
  $speed = $speed * $miles;
  $speed = round($speed, 1);
  
  return $speed;
  } 
  

/**********************************************************************/
/* We assume the first line exists...if it doesn't we can't show anything */

$lines = file($logfile);

if( count($lines) < 1){
print "Error! data file $logfile empty.";
die();
}

  //$p = $lines[count($lines)-1];
$p = $lines[0]; 
$p = explode(":", $p);

$time = $p[0];
$lat = $p[1];
$lon = $p[2];
$acc = $p[3];

$acc = (int)$acc;
$pos = "$lat,$lon";
$time = strftime("%Y-%m-%d %H:%M:%S", $time);
$utime = urlencode($time);
$uname = urlencode($name);
$toptime = '';


?>

<html>
<head>
<title>Tracking the Sea Wombles as they attempt the Great River Race</title>
<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
<!-- meta http-equiv='refresh' content='30' / -->
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript">
  function initialize() {
    var latlng = new google.maps.LatLng(<?=$lat?>,<?=$lon?>);
    var myOptions = {
      zoom: 13,
      center: new google.maps.LatLng('51.484229','-0.180934'),
      mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
  var boaticon = {
    url: "http://seawombles.x50.com/Animated-row-boat-with-oars-rowing.gif",
    anchor: new google.maps.Point(32, 32)
  }
    var marker = new google.maps.Marker({
      position: latlng, 
      map: map, 
      title:"<?=$name?>", 
    icon: boaticon,
    optimized: false
  });
    var previousLocations = [
<?php
 /*Draw a polyline to each previous data point*/
 for($i=0; $i < count($lines); $i++) {
    $p = $lines[$i];
    $p = explode(":", $p);
    $time = $p[0];
    $lat = $p[1];
    $lon = $p[2];
    $acc = $p[3];
    $acc = (int)$acc;
    $time = strftime("%Y-%m-%d %H:%M:%S", $time);
  if ($toptime == '') {
    $toptime = $time;
    $toplat = round($lat, 3);
    $toplon = round($lon, 3);
    $topacc = $acc;
  }
    echo "new google.maps.LatLng($lat,$lon),\n" ;
 }
?>
  ]; /* end array of previous positions */

  
<?php

 /*Draw markers to each data point*/
 $oldnattime = '';
 for($i=0; $i < (count($lines) ); $i++) {
  $p = $lines[$i];
    $p = explode(":", $p);
  $lat = $p[1];
    $lon = $p[2];
  $time = $p[0];
  $nattime = $time;
  $time = strftime("%H:%M:%S", $time);
  
  if ($oldnattime != '') { 
    $spd = speed($lat, $lon, $oldlat, $oldlon, $nattime, $oldnattime, 'N');  
  } else {
    $spd= '';
  }
  echo " var marker$i = new google.maps.Marker({ \n";
    echo "position: new google.maps.LatLng($lat,$lon), \n";
    echo "title: 'marker $i at $time at $spd knots',\n";
    echo "map: map\n";
  echo "}); \n";
  $oldlat = $lat;
  $oldlon = $lon;
  $oldnattime = $nattime;
 }
?> 




 var prevPos = new google.maps.Polyline( {
   path: previousLocations,
   strokeColor: "#FF0000",
   strokeOpacity: 1.0,
   strokeWeight: 5,
   map: map,
   geodesic: true
  });
  

  } // end Initialize

</script>
<style type="text/css">
  Body {background: #26ade4; /* Old browsers */
background: -moz-linear-gradient(top,  #26ade4 0%, #b2d8e8 100%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#26ade4), color-stop(100%,#b2d8e8)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(top,  #26ade4 0%,#b2d8e8 100%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(top,  #26ade4 0%,#b2d8e8 100%); /* Opera 11.10+ */
background: -ms-linear-gradient(top,  #26ade4 0%,#b2d8e8 100%); /* IE10+ */
background: linear-gradient(to bottom,  #26ade4 0%,#b2d8e8 100%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#26ade4', endColorstr='#b2d8e8',GradientType=0 ); /* IE6-9 */

    }
  
  p {font-family: verdana;
    color: #ffffff;}
  h1 {font-family: verdana;}
  a:link {color:#fff;}      /* unvisited link */
  a:visited {color:#fff;}  /* visited link */
  a:hover {color:#D1E751;}  /* mouse over link */
  a:active {color:#D1E751;}  /* selected link */
  #map_canvas {width:90%; height:80%; margin-left:5%; border: 3px solid #D1E751;}
</style>
</head>
<body onload="initialize()" >
<p style="float:right; border: 1px solid white; padding: 5px; margin-right:10px;">
 RIGHT NOW?<br />
  Latitude: <?=$toplat?>  Longitude: <?=$toplon?> <br />
  <!--Speed: <?=$spd?> knts<br />-->
  Accuracy: <?=$topacc?> m<br />
  Updated: <?=$toptime?> <br />
</p>

<h1>Sea Wombles Tracker</h1>
<p>Follow the Sea Wombles on their crazy town voyage on the Great River Race. Jim, Stephen, Nik and Freddie. <br />
<a href="http://justgiving.com/seawombles">We're raising money for the RNLI too!</a></P>

  <div id="map_canvas"></div>
  <p style="text-align:center; font-size:0.6em; color:#000000;">design, code and hosting: x50.com</p>
</body>
</html>
