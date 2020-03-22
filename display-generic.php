<?php

/***********************************************
   Zurich Water Game: internet version
   Author:  Nigel Gilbert and the FIRMA project
   --------------------------------------------
   display-generic.php
   
   Displays the generic choices available and the reasons
   for action box for all players (except the bank)
   
   Version 1.0  6 August 2001
   Version 1.1  1 September 2001
   Version 2.0  10 November 2001
   Version 2.1  7 April 2002

**********************************************/


if ($player != 'bank') {
	item('advertise', "advertise (select topic)", "<br><select name=state>
	    <option selected value=political_popularity>Political popularity</option>
	    <option value=env_awareness>Environmental awareness</option>
	    </select>", 
	    'adverthelp');
	
	item('borrow_from_bank', "borrow from the bank " . CURRSYM, 
	    "<input type=text name=amount maxlength=7 size=7>",
	    'borrowhelp');
	
	if ($playobj->debtor()) {
		item('repay_bank', "repay the bank " . CURRSYM, "<input type=text name=repayment 
		    maxlength=7 size=7>", 'repayhelp');
	}
}

if ($player != 'bank') {
?>

<!--display the input box for player's reasons for action-->

<tr>
	<td colspan=4>
		<img src="images/reasons_for_actions.gif" width=250 height=18>
	</td>
</tr>
<tr>
	<td colspan=3 align=left>
		<textarea name="reasons" cols=25 rows=2> <?php echo $reasons ?></textarea>
	<td align=right>
		<input type=image name="submit" src="images/button_go.gif" value="go" width=27 height=27 border=0>
	</td>
</tr>
<?php
	}
	else // bank - no reasons for action box
		{
?>
<tr>
	<td colspan=4 align=right>
		<input type=image name=submit src="images/button_go.gif" value="go" width=27 height=27 border=0>
	</td>
</tr>
<?php
	}
?>
</table>
</form>
</div>

