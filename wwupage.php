<?php

/***********************************************
   Zurich Water Game: internet version
   Author:  Nigel Gilbert and the FIRMA project
   --------------------------------------------
   wwupage.php
   
   the HTML that is shown to the Waste Water
   Utility
   
   Version 1.0  6 August 2001
   Version 1.1  1 September 2001
   Version 2.0  10 November 2001

**********************************************/
?>
<div id="table" style="position:absolute; left:5px; top:30px; width:300px; 
        height:80px; z-index:2"> 
<p><img src="./images/toilet.gif" width="53" height="75" align=middle>
<?php

switch ($playobj->filter_level) {
	case 1: echo "Mechanical"; break;
	case 2: echo "Nutrient and mechanical"; break;
	case 3: echo "Biological, nutrient and mechancial"; break;
}
?>
 filtration
</p>
</div>
<?php

include("action-top.php");

item('change_filter_level', "select filtration type (quality)", "<br>
<select name=filter_quality>
<option value= '1'>Mechanical (low)</option>
<option value= '2'>Nutrient + Mech (med.)</option>
<option value= '3'>Bio + Nutr + Mech (high)</option>
</select>", 'filterhelp');
item('flush_pipes', "clean the pipes of waste", "", 'flushhelp');

