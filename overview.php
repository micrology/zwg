<?php

/***********************************************
   Zurich Water Game: internet version
   Author:  Nigel Gilbert and the FIRMA project
   --------------------------------------------
   display-log.php
   
   Displays panels showing the actions of
   each player
   
   Version 2.1  7 April 2002

**********************************************/
include("constants.php");
include("common.php");
include("query.php");
include("objects.php");

open_database();

$item_limit = 100;  /* display up to this number from the end of the log */

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<META HTTP-EQUIV="expires" CONTENT="now">
<META HTTP-EQUIV="pragma" CONTENT="no-cache">
<META HTTP-EQUIV="REFRESH" CONTENT="60">
<TITLE>Overview of the Zurich Water Game</TITLE>
<link rel="stylesheet" href="zwg.css">
</HEAD>
<body bgcolor="#ffffff">
<?php	
$query = new Query("SELECT id, name, action, 
					to_char(time, 'HH24:MI') as timestring
					FROM log
					WHERE detail > 1
					ORDER BY id DESC
					LIMIT $item_limit");
					
foreach ($zurich->players as $player => $playobj) {
	display_panel($player, $playobj, $query, $i++);
}

display_scales();

html_footer();


function display_panel($player, $playobj, $query, $panel_no) {
	/* display one panel */
	
	$left = 20 + ($panel_no % 4) * 200;
	$top = 20 + floor($panel_no / 4) * 240;
	echo "<div id=\"panel$panel_no\" style=\"position:absolute; left:{$left}px; top:{$top}px; 
		width:180px; height:210px; z-index:2; background-color:white;
		border: thin dotted #000000;\">\n";
	echo "<center>". display_player_name($player) . "<BR><span class=smalltext>\n";
	if ($playobj->loggedin) echo "(logged in)"; else echo "(not logged in)";
	echo "</span><P class=smalltext>Balance: " . CURRSYM . $playobj->account . "\n";
 	echo "Overdraft: " . CURRSYM . $playobj->debtor() . "</center></P>\n";
 	echo "<div id=\"inner$panel_no\" style=\"position:absolute; left:2px; top:50px; 
		width:170px; height:150px; overflow: auto\">\n";
	echo "<table width=\"100%\" border=0 cellspacing=2 cellpadding=0>\n";
	while($query->next_rec()) {
		$log_name = $query->field('name');
		$robot = FALSE;
		if ($log_name{0} =='*') {
				$robot = TRUE;
				$log_name = substr($log_name, 1);
		}
		if ($log_name != $player) continue;
		$log_time = $query->field('timestring');
		$log_action = $query->field('action');
		echo "<tr valign=top><td><span class=smalltext>$log_time</span></td>";
		echo "<td><span class=smalltext>";
		if ($robot) echo "<font color=gray>";
		echo $log_action;
		if ($robot) echo "</font>";
		echo "</span></td>\n</tr>\n";
    }
	echo "</table></div></div>";
}

function display_scales() {
	/* same as usual, but no rating scale */
	
	global $zurich;
?>	
	<div id="Scales" style="position:absolute; left:0px; top:500px; width:800px;
         height:200px; background-color:white; z-index:7"> 
  <center>
    <table border=0 cellspacing=0 cellpadding=0>
<tr align=center>
    <td width=90><span class="smalltext">water demand</span></td>
    <td width=90><span class="smalltext">water supply</span></td>
    <td width=90><span class="smalltext">water price</span></td>
    <td width=90><span class="smalltext">water quality</span></td>
	<td width=90><span class="smalltext">political popularity</span></td>
    <td width=90><span class="smalltext">lake water quality</span></td>
	<td width=90><span class="smalltext">environmental awareness</span></td>
</tr>
<tr align=center>
<?php 
show_scale($zurich->water_demand/10, $zurich->water_demand, 'bar', 
	'water_demand_scale_help');
show_scale($zurich->water_supply/10, $zurich->water_supply, 'bar', 
	'water_supply_scale_help');
show_scale($zurich->water_price, $zurich->water_price, 'bar', 
	'water_price_scale_help');
show_scale($zurich->water_quality, $zurich->water_quality, 'bar', 
	'water_quality_scale_help');
show_scale($zurich->political_popularity, $zurich->political_popularity, 'bar', 
	'pol_pop_scale_help');
show_scale($zurich->lake_quality, $zurich->lake_quality, 'bar', 
	'lake_quality_scale_help');
show_scale($zurich->env_awareness, $zurich->env_awareness, 'bar',
	'env_awareness_scale_help');
echo "<td><p class=smalltext>Display updates once a minute<br><form method=post action=overview.php> 
		<input type=image name=submit src=\"images/updatenow.png\"
alt=\"Press to update details\" border=0></form></p></td>";
echo "</tr></table></center>\n";
}

function show_scale($scaled_val, $val, $icon, $help) {
	/* display a bar made up of $icon to show value of $val */
	
	$scaled_val=round($scaled_val);
	if ($scaled_val > 10) $scaled_val = 10;
	if ($scaled_val < 0 ) $scaled_val = 0;		// be safe!
	echo "<td><a href=\"#\">
				<img SRC=\"images/$icon$scaled_val.gif\" 
					ALT=\"Value=$val\" TITLE=\"Value=$val\" 
					width=15 height=50 border=0> $val</a></td>\n";
}
