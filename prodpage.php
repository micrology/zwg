<?php

/***********************************************
   Zurich Water Game: internet version
   Author:  Nigel Gilbert and the FIRMA project
   --------------------------------------------
   prodpage.php
   
   the HTML that is shown to the manufacturers
   
   Version 1.0  6 August 2001
   Version 1.1  1 September 2001

**********************************************/

/*=============================================

     Display table of my stock of factories
     
 =============================================*/

?>
<div id="table" style="position:absolute; left:5px; top:30px; width:300px; 
        height:50px; z-index:2"> 
    <img src="images/title_age_state_factories.gif" width="140" height="12">
  <table border="0" cellspacing="0" cellpadding="0">
    <tr> 

<?php

foreach ($playobj->factories as $f) {
	echo "<td width=53 align=center>";
	$image =  ($f->type == 'normal' ? "factory.gif" : "factory_ws.gif");
	if ($f->age >= $f->max_age) $image = "factory_dead.gif";
	echo "<img src=\"images/$image\" width=42 height=35>";
	if ($f->age < $f->max_age) echo "<span class=smalltext>$f->age</span></td>\n";
}
echo "</tr><tr>";

foreach ($playobj->factories as $k => $f) {
	echo "<td width=53 align=center><span class=actiontext>$k</span></td>";
}

echo "</tr></table></div>\n";

/*=============================================

     Display player specific action choices
     
 =============================================*/

include("action-top.php");

    item('new_factory', "build a new " .
    		make_popup('newfactorytype', array('normal' => 'normal', 'water saving' => 'water-saving')) .
    		" factory", "", 'newfactoryhelp');
    item('repair_factory', "repair a factory", 
    		"<br>which factory? " . make_popup('repair_factory_number', $playobj->factories), 
    		'repairfactoryhelp');
    item('change_production', "change the factory type", 
        "<br>which factory? " . make_popup('ch_prod_factory_number', $playobj->factories), 
        'changeprodhelp');
