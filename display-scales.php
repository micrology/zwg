<?php

/***********************************************
   Zurich Water Game: internet version
   Author:  Nigel Gilbert and the FIRMA project
   --------------------------------------------
   display-scales.php
   
   Displays a panel showing the environmental
   indices
   
   Version 1.0  6 August 2001
   Version 1.1  1 September 2001
   Version 2.1  7 April 2002

**********************************************/

?>	
<div id="Scales" style="position:absolute; left:0px; top:400px; width:760px;
         height:400px; background-color:white; z-index:7"> 
  <center>
    <table border=0 cellspacing=0 cellpadding=0>
<tr align=center>
    <td width=100><span class="smalltext">your rating</span></td>
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
$rating = $playobj->rating();
show_scale($rating, $rating, 'bar', //should be 'star' when i have created some images!
	'rating_scale_help');
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
echo "</tr></table></center>\n";

/* links under the scales */

?>
<a class="smalltext" href="http://www.cpm.mmu.ac.uk/firma/">&copy;
        FIRM</a><a class="smalltext" href="main.php?newplayer=bank&checked=1">A</a>
</div>

<div id="leavebutton" style="position:absolute; left:720px; top:450px; width:120px; 
		height:25px; z-index:10">

			<form method=post action="leave.php">
				<input type="image" name="submit" src="images/leave.jpg" 
					alt="Press to leave" border="0" width="46" height="23">
			</form>
</div>

<!--Javascript to make sure that the correct tab is displayed 'on top' of the others -->

<script language="Javascript"> 
<?php
echo "
	MM_nbGroup('down','group1','${tab_to_display}','images/tab_${tab_to_display}2.gif',1);
	MM_showHideLayers('${tab_to_display}layer','','show');\n";
	
	/* if there are new public or private messages, then make the tab flash (i.e 
	substitute an animated gif */
	
if ($tab_to_display != 'public' and $new_public_msg) {
	echo "img = MM_findObj('public'); img.src = 'images/tab_public_flash';\n";
}
if ($tab_to_display != 'private' and $new_private_msg) {
	echo "img = MM_findObj('private'); img.src = 'images/tab_private_flash';\n";
}
echo "</script>";

		
function show_scale($scaled_val, $val, $icon, $help) {
	/* display a bar made up of $icon to show value of $val */
	
	$scaled_val=round($scaled_val);
	if ($scaled_val > 10) $scaled_val = 10;
	if ($scaled_val < 0 ) $scaled_val = 0;		// be safe!
	echo "<td><a href=\"#\" onMouseOver=\"displayhelp('$help')\"
				onMouseOut=\"hidehelp('$help')\">
				<img SRC=\"images/$icon$scaled_val.gif\" 
					ALT=\"Value=$val\" TITLE=\"Value=$val\" 
					width=15 height=50 border=0> $val</a></td>\n";
}
