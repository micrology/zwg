<?php

/***********************************************
   Zurich Water Game: internet version
   Author:  Nigel Gilbert and the FIRMA project
   --------------------------------------------
   polpage.php
   
   the HTML that is shown to the Politician
   
   Version 1.0  6 August 2001
   Version 1.1  1 September 2001
   Version 2.1  7 April 2002

**********************************************/
?>
<div id="table" style="position:absolute; left:5px; top:30px; width:300px; 
        height:50px; z-index:2"> 
<p><img src="./images/ballotbox.gif" width="42" height="36">
</p>
</div>
<?php

include("action-top.php");

item('subsidise', "subsidise a player (select which)", "
<select name=recipient>
<option value=water_utility>Water Utility</option>
<option value=waste_water_utility>Waste Water Utility</option>
<option value=housing_assoc_1>Housing Association 1</option>
<option value=housing_assoc_2>Housing Association 2</option>
<option value=manufacturer_1>Manufacturer 1</option>
<option value=manufacturer_2>Manufacturer 2</option>
<option value=politician>Politician</option>
</select>
Value of subsidy:
<input type=text name=subsidy maxlength=7 size=7>", 'subsidisehelp');

item('changetax', "change the tax rate from " . 100*$playobj->tax_rate . 
	"% to ", "<input type=text name=newtax maxlength=7 size=3>%",
	'changetaxhelp');

item('referendum', "hold a referendum", "", 'referendumhelp');

