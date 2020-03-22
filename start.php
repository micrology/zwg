<?php

/***********************************************
   Zurich Water Game: internet version
   Author:  Nigel Gilbert and the FIRMA project
   --------------------------------------------
   start.php
   
   Displays a menu of player roles for the user to choose one.
   
   Version 1.0  6 August 2001
   Version 1.1  1 September 2001
   Version 2.0  10 November 2001

**********************************************/

$player = $_COOKIE['player'];

if ($player) {
	header("Location: main.php");
}

include("constants.php");
include("common.php");
include("query.php");
include("objects.php");

db_open($database);
$zurich = db_retrieve("Zurich", "objects");

?>
<html>
<head>
<title>Welcome to the Game!</title>
<LINK REL=stylesheet HREF="zwg.css" type="text/css">
</head>
<body BGCOLOR="#FFFFFF">
<p><center><b>Click on the icon to choose a stakeholder and start playing the game:</b>
<table border=0 cellspacing=10 cellpadding=0>

<?php

foreach ($zurich->players as $p) {
    $id = $p->id;
    switch ($id) {
 	case 'water_utility':  
 	    $icon="images/wu-icon.gif";
 	    break;
 	case 'waste_water_utility':
 	    $icon="images/wwu-icon.gif";
 	    break;
 	case 'housing_assoc_1': 
 	    $icon="images/ha-icon.gif";
 	    break;
 	case 'housing_assoc_2': 
 	    $icon="images/ha-icon.gif"; 
        break;
 	case 'manufacturer_1':      
 	    $icon="images/man-icon.gif"; 
 	    break;
 	case 'manufacturer_2':      
 	    $icon="images/man-icon.gif"; 
 	    break;
 	case 'politician':      
 	    $icon="images/pol-icon.gif"; 
 	    break;
 	default:
 	    continue 2;  // next iteration of foreach
	}
    echo "<tr><td><A href=\"main.php?newplayer=$id\" target=\"_top\"><IMG SRC=\"$icon\" 
            WIDTH=55 HEIGHT=61 ALIGN=MIDDLE BORDER=0>
            </a></td>
            <td>$p->name</td>";
    if ($zurich->players[$id]->loggedin) {
    	    echo "<td><img src=\"images/tick.gif\" width=16 height=16 border=0>Already logged in</td>";
    }
    echo "</tr>\n";
} 
echo "</table></center>";
html_footer();
