<?php
/* Big Brother GPS Receving script - jay at summet.com */

/*  Configuration parameters	*/

$logfile = "/home/shonen/seawombles.x50.com/position.cur" ;
$maxDataPoints = "1000";


/*************************************************************/
/* Get the position data from the POST parameters */
$lat = $_POST["latitude"];
$lon = $_POST["longitude"];
$acc = $_POST["accuracy"];
$secret = $_POST["secret"];

/* Load the original file contents. */
$lines = file($logfile);


/* Write the position data to a file for the map script */
 if ($lat && $lon && $acc) {
    $fcur = fopen($logfile, "w");

    $time = time();
    $out = "$time:$lat:$lon:$acc\n";

    fputs($fcur, $out);

   /* write the old data under the new data */
	 /* TODO: Limit length of old data */
   for($i=0; $i < count($lines); $i++){
     if ($i < $maxDataPoints) {
       $out = "$lines[$i]";
       fputs($fcur,$out);
     }
   }
    
    fclose($fcur);
    print ("Got your location!");
}

?>
