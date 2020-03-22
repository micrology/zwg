<?php

/***********************************************
   Zurich Water Game: internet version
   Author:  Nigel Gilbert and the FIRMA project
   --------------------------------------------
   wspage.php
   
   the HTML that is shown to the Water Utility
   
   Version 1.0  6 August 2001
   Version 1.1  1 September 2001

**********************************************/

/*=============================================

     Display table of my stock of reservoirs
     
 =============================================*/

?>
<div id="table" style="position:absolute; left:5px; top:30px; width:300px; 
        height:50px; z-index:2"> 
    <img src="images/title_age_state_res.gif" width="140" height="12">
  <table border="0" cellspacing="0" cellpadding="0">
    <tr> 

<?php

foreach ($playobj->reservoirs as $r) {
	echo "<td width=53 align=center>";
	$image =  ($r->age < $r->max_age ? "res_" . $r->age . ".gif" : "res_dead.gif");
	echo "<img src=\"images/$image\" width=42 height=35>";
	if ($r->age < $r->max_age) echo "<span class=smalltext>$r->age</span></td>\n";
	}
echo "</tr><tr>";
foreach ($playobj->reservoirs as $k => $f) {
	echo "<td width=53 align=center><span class=actiontext>$k</span></td>";
}
echo "</tr></table></div>\n";

/*=============================================

     Display player specific action choices
     
 =============================================*/

include("action-top.php");

    item('decrease_capacity', "close down reservoir ", make_popup('close_res_number', $playobj->reservoirs), 
    		'closedownhelp');
    item('increase_capacity', "build a new reservoir", "", 'buildnewhelp');
    item('repair_reservoir', "repair reservoir", make_popup('repair_res_number', $playobj->reservoirs), 
    		'repairhelp');
    item('improve_water_quality', "improve the water quality", "", 'improvehelp');
    item('petition_politician', "request new water price " . CURRSYM, 
        "<input type=text name=price maxlength=7 size=2>", 'petitionhelp');
        
        



