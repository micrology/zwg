<?php

/***********************************************
   Zurich Water Game: internet version
   Author:  Nigel Gilbert and the FIRMA project
   --------------------------------------------
   display-help.php
   
   Generate the HTML for the help prompts that are
   shown when the user clicks the mouse over the
   help button
   
     
   Version 1.0  6 August 2001
   Version 1.1  1 September 2001
   Version 2.0  10 November 2001
   Version 2.1  7 April 2002
**********************************************/

function helpmsg($helpid, $msg) {
    /* generate the HTML to display a help message, shown when the
    user clicks the mouse over the help icon */
    
    echo "<div id=\"$helpid\" style=\"visibility:hidden; position:absolute; 
            left:400px; top:120px; width:250px; height:160px; background-color:white; z-index:5\">
            	<p class=\"helptext\">$msg</p>
           </div>\n";
}

switch ($player) {
 
 	case 'water_utility': 
 	    helpmsg('closedownhelp', "When a reservoir is closed down, 
 	    the volume of water supplied is reduced by 20.  If you ever want 
 	    to increase the supply again, you will need to build a new reservoir.");
 	    
 	    helpmsg('buildnewhelp',"Building a new reservoir costs " . CURRSYM . "1500, 
 	    and the volume of water that can be supplied then increases by 20");
 	    
 	    helpmsg('repairhelp',"The selected reservoir is repaired.  After 
 	    the repairs have been made, the reservoir will last for
 	    another 10 years.  Repairing a reservoir costs " . CURRSYM . "500.");
 	    
 	    helpmsg('improvehelp',"The quality of the water supply can be improved
 	    by using new technology, which costs " . CURRSYM . "1000 to purchase and install.");
 	    
 	    helpmsg('petitionhelp',"The politician must be asked to approve a
 	    proposed increase in the price of water.  Only when approval has been given
 	    can the price be raised. ");
 	     
 	    break;
 	    
 	case 'waste_water_utility':
 		helpmsg('filterhelp', "There is a choice of the type of filtration system to use.  
 		They differ in their running costs and the amount of protection they provide.  
 		Level I is mechanical filtration which gives a low level of protection at a cost 
 		of " . CURRSYM . "100 per turn.  Level II combines nutient and mechanical methods, 
 		gives medium protection and costs " . CURRSYM . "200.  Level II includes biological, 
 		nutrient and mechanical filtration, gives a high level of protection and costs 
 		" . CURRSYM . "300 per turn. Changing fitration type is free.");
 		
 		helpmsg('flushhelp', "The waste water pipes can be flushed to clear them of 
 		waste at a cost of " . CURRSYM . "500. This is desirable if the water demand is low 
 		(below 50).");
 		
 		break;
 		
 	case 'housing_assoc_1': 
 	case 'housing_assoc_2': 
 	    helpmsg('wstquotehelp',"Either normal or water saving technology sanitary 
 	    systems can be used in houses.  To buy a system, you must ask the 
 	    manufacturers to say how much they would charge for a system.  A new system
 	    installed in your house will last for 8 years.");
 	    
        break;
        
 	case 'manufacturer_1':      
 	case 'manufacturer_2':
 	    helpmsg('newfactoryhelp', "You can build an additional factory to produce 
 	    sanitary systems at a cost of " . CURRSYM . "800.  The factory will continue in 
 	    production for a maximum of 8 years before it will need repair.");
 	    
 	    helpmsg('repairfactoryhelp', "The oldest factory is repaired.  After it has 
 	    been repaired it can continue in production for another 8 years.  Repairs cost
 	    " . CURRSYM . "500 for normal or " . CURRSYM . "600 for water saving technology 
 	    factories.");
 	    
 	    helpmsg('changeprodhelp', "A factory can be changed from producing normal sanitary 
 	    systems to water saving ones, or vice versa.  Each change costs " . CURRSYM . "600."); 
 	         
 	    break;
 	    
 	case 'politician': 
 	    helpmsg('subsidisehelp', "You can give a subsidy to another player to help
 	    them if you wish.  This is a single payment that goes directly from you
 	    to their account.");
 	    
 	    helpmsg('referendumhelp', "You can hold a referendum at any time to find out
 	    the voter's opinion.  Following the voters' decision avoids the possible
 	    loss of popularity if you make decisions yourself."); 
 	        
 	    break;
 	    
  	case 'bank':
  	    helpmsg('yearendhelp', "Causes the end of the financial year to occur, and
  	    annual bills to be issued to the players.  This is in addition to the
  	    regular end of year that occurs automatically.");
  	    
  	    helpmsg('resethelp', "Logs out all players, resets all bank balances and
  	    clears all logs.");
  	    
  	    helpmsg('becomehelp', "Switch to the selected player's game page.");
  	    
  	    helpmsg('logouthelp', "Logs out the selected player");
  	    
   	    helpmsg('logoutallhelp', "Logs out all the players except the Bank");
   	    
    	helpmsg('autohelp', "Runs the default strategy for all players in turn, 
    			for specified number of turns");
    			
    	helpmsg('mintimehelp', "Sets the time players must wait between successive
    			turns");
    	helpmsg('actsperyearhelp', "Number of player actions between each year end");
	          
 	    break;
 	    
 	default:
 	    alert("Shouldnt happen (helpmsgs)");
 	    break;
	}
    
   /* help messages for the standard actions available for all */

helpmsg('adverthelp', "An advertisement costs " . CURRSYM . "500. Advertising may increase the 
popularity of the selected institution (but there is a chance that it will backfire 
and decrease popularity)");

helpmsg('borrowhelp', "You can borrow as much as you like from the bank, but you 
will have to pay interest on your overdraft at the end of each year.");

helpmsg('repayhelp', "You may pay off your loan from the bank with as much as you 
can afford at any time.");

	/* help messages for the indicator scales */
	
helpmsg('rating_scale_help', "This scale measures how well you are doing in 
maintaining the situation in comparison to the objectives you set at the beginning 
of the game.  It rises as the indicators that you set as important or very important
get closer to their optima.  The settings you made are:<br>" .  rating_text($playobj->rating_weight));

helpmsg('water_demand_scale_help', "This scale measures the total amount of water demanded by both
the housing associations. Only if water supply is as high or higher can
this demand be met by the water utility. Water demand is affected by the
type of water sanitary system installed by the housing associations in
their houses and the level of environmental awareness. Water demand levels
also affect lake water quality.");

helpmsg('water_supply_scale_help', "This scale measures the amount of water that the water utility
can supply to the housing associations. It is related to the number of
working reservoirs.");

helpmsg('water_price_scale_help', "This scale measures the cost per unit of water demanded for the
housing associations. Water price can be changed by the water utility with
the agreement of the politician and if necessary a public referendum. Water
price affects political popularity.");

helpmsg('water_quality_scale_help', "This scale measures the quality of the drinking water coming out
of the households' taps. Water quality is related to the ratio of water
supply to water demand. If the water supply is too high in relation to
demand, then the water will stand too long in the pipes and will sour, thus
reducing the water quality. When the supply and demand are well matched,
the water quality rises.");

helpmsg('pol_pop_scale_help', "This scale measures the popularity of the politician. The
level of popularity determines the probability that the politician will be
re-elected on election day. Popularity is affected by, among other things,
the water supply to water demand ratio, the water price, water quality and
lake water quality. If supply is less than demand, the water price rises, or
the quality of the water supplied or in the lake falls, then popularity will fall. The
reverse is true. Popularity can be raised by advertising.");

helpmsg('lake_quality_scale_help', "This scale measures how well the waste water
utility is cleaning the waste water coming from the housing associations.
Lake water quality is affected by the level of water demand and the type of
filtration unit used by the waste water utility. If water demand is low,
then the concentration of waste is high and a higher quality filtration
system is needed if lake water quality is not to fall.");

helpmsg('env_awareness_scale_help', "This scale measures the level of environmental
awareness among the people living in the housing association houses. If
awareness is high, then water demand will lower and vice versa. Awareness
can be raised by advertising.");

function rating_text($weights) {
	
	$comp_names = array("Water supply", "Water price", "Political popularity", "Lake water quality", "Profitability");
	$rating_names = array(0, "Not important", "Important", "Very important");
	$text = "";
	for ($i = 0; $i < 5; $i++) { 
		$text .= "<b>" . $comp_names[$i] . "</b> is <b>" . $rating_names[$weights[$i]] . "</b><br>";
	}
	return $text;
}
		
		
