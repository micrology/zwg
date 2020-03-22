<?php

/***********************************************
   Zurich Water Game: internet version
   Author:  Nigel Gilbert and the FIRMA project
   --------------------------------------------
   display-log.php
   
   Displays a panel showing the actions logged
   
   Version 1.0  6 August 2001
   Version 1.1  1 September 2001
   Version 2.1  7 April 2002

**********************************************/


$item_limit = 100;  /* display up to this number from the end of the log */

/* filter out anything with a level of detail below the log_level 
unless the player is the bank (which sees everything) 
However, include log_level - 1 for items that are for this particular player */

$levelminusone = $log_level - 1;
if ($playobj->id != 'bank') $filter = "WHERE detail >= '$levelminusone'";
	
$query = new Query("SELECT id, name, action, detail,
					to_char(time, 'HH24:MI on DD Mon') as timestring
					FROM log
					$filter
					ORDER BY id DESC
					LIMIT $item_limit");
?>
<div id="diarylayer" style="position:absolute; left:320px; top:40px; 
		width:410px; height:340px; z-index:2; visibility:hidden;  
		background-color:white">
  	<p class=actiontext>diary of events</p> 
	<div id="log" style="position:absolute; left:0px; top:20px; 
		width:400px; height: 300; overflow: auto">
      	<table width="90%" border="0" cellspacing="2" cellpadding="0">
<?php 
	while($query->next_rec()) {
		$log_time = $query->field('timestring');
		$log_name = $query->field('name');
		$log_action = $query->field('action');
		$log_detail = $query->field('detail');
		if ($log_detail > $log_level or $log_name == $player) {
			echo "<tr><td colspan=3 valign=top bgcolor=white>
			    <span class=smalltext>$log_time</span></td>
				<td width=76 valign=top bgcolor=white>
				<span class=smalltext>";
			echo "&nbsp;". display_player_name($log_name) . "&nbsp;";
			echo "</span></td><td valign=top bgcolor=white>
				<span class=smalltext>$log_action</span></td>\n</tr>\n";
		}
    }
				
?>
		</table>
	</div>
</div>
