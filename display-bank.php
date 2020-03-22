<?php

/***********************************************
   Zurich Water Game: internet version
   Author:  Nigel Gilbert and the FIRMA project
   --------------------------------------------
   display-bank.php
   
   Displays  the tab for the player's bank balance (and system messages)
   
   Version 1.0  6 August 2001
   Version 1.1  1 September 2001
   Version 2.0  10 November 2001
   Version 2.1  7 April 2002

**********************************************/

?>
</span>
</div>
<!--	 display bank balance.  Has to be done AFTER actions have been carried out -->
<div id="balance" style="position:absolute; left:10px; top:10px; width:300px; 
		height:50px; background-color:white">
	
<table border=0 cellspacing=0 cellpadding=0>
	<tr>
		<td colspan=2>
			<p class="actiontext">current bank details<br></p>
		</td>
	</tr>
	<tr>
		<td>
			<p class="graytext">current bank balance: </p>
		</td>
		<td>
			<p class="graytext">
			<?php
				if ($playobj->account == 0) {echo "----";}
				else {echo CURRSYM . $playobj->account;}
			?>
			</p>
		</td>
	</tr>
	<?php
		if ($playobj->debtor()) {
			echo "<tr><td><p class=graytext>borrowed from bank: </p></td>
			<td><p class=graytext>" . CURRSYM . $playobj->debtor() . 
			" at " . $playobj->interest_rate() * 100 . "% pa interest</p></td></tr>";
		}
	?>
</table>
</div>
</div>
		
<!--display data about other players-->

<div id="datalayer" style="position:absolute; left:310px; top:40px; width:440px; 
		height:330px; background-color:white; z-index:2; visibility:hidden">
<table border=0 cellpadding=0 cellspacing=10>
<tr>
<td>&nbsp;</td><td colspan=2 align=center><span class=actiontext>time of</span></td>
</tr>
<tr>
<td align=center><span class=actiontext>player</span></td>
<td align=center><span class=actiontext>starting</span></td>
<td align=center><span class=actiontext>last action</span></td>
<td align=center><span class=actiontext>balance</span></td></tr>
<?php
	foreach ($zurich->players as $p) {
		if (!isset($p->id)) continue;
	    if ($p->id == 'bank') continue;
	    echo "<tr><td><span class=subaction>". display_player_name($p->id) . "</span></td>\n";
	    if ($p->loggedin) echo "<td><span class=subaction>$p->loggedin</span></td>\n";
	    else echo "<td align=center><span class=subaction>[Not online]</span></td>\n";
	    if ($p->last_time) {
	    	$last_action = date("g:ia D, d M", $p->last_time);
	    	echo "<td><span class=subaction>$last_action</span></td>\n";
	    }
	    else echo "<td><br></td>";
	    echo "<td align=right><span class=subaction>$p->account</span></td>\n";
	    echo "<td align=left><span class=actiontext>";
	    for ($i=0; $i < $p->rating()/2; $i++) echo "*";
	    echo "</span></td>\n";    
	    echo "</tr>";
	    }
?>
</table>
</div>
