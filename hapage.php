<?php

/***********************************************
   Zurich Water Game: internet version
   Author:  Nigel Gilbert and the FIRMA project
   --------------------------------------------
   hapage.php
   
   the HTML that is shown to the Housing Associations
   
   Version 1.0  6 August 2001
   Version 1.1  1 September 2001
   Version 2.0  4 November 2001
   Version 2.1  7 April 2002
   
**********************************************/

/*=============================================

     Display table of my stock of houses
     
 =============================================*/

?>
<div id="table" style="position:absolute; left:5px; top:30px; width:300px; 
        height:50px; z-index:2"> 
    <img src="images/title_age_state_houses.gif" width="140" height="12">
  <table border="0" cellspacing="0" cellpadding="0">
    <tr> 

<?php

foreach ($playobj->houses as $h) {
	echo "<td width=53 align=center>";
	if ($h->age >= $h->max_age) {
		echo "<img src=\"images/house_dead.gif\" width=42 height=35></td>\n";
	}
	else {
		$image =  ($h->type == 'normal' ? "house.gif" : "house_water_saving.gif");
		echo "<img src=\"images/$image\" width=42 height=35>";
		echo "<span class=smalltext>$h->age</span></td>\n";
	}
}
echo "</tr><tr>";
foreach ($playobj->houses as $k => $f) {
	echo "<td width=53 align=center><span class=actiontext>$k</span></td>";
}
echo "</tr></table></div>\n";

/*=============================================

     Display player specific action choices
     
 =============================================*/

include("action-top.php");
echo "<p>";
item('request_sanitary_system', "get quotes for a sanitary system",
    "<br>
    <input selected name=type type=radio value=normal>Normal
    <input name=type type=radio value=\"water saving\">Water saving<p>",
    'wstquotehelp');
