<?php

/***********************************************
   Zurich Water Game: internet version
   Author:  Nigel Gilbert and the FIRMA project
   --------------------------------------------
   bankpage.php
   
   the HTML that is shown to the Gamemaster
   
   Version 1.0  6 August 2001
   Version 1.1  1 September 2001
   Version 2.1  7 April 2002
**********************************************/

include("action-top.php");

item('force_end_of_year', "move past end of year", "", 'yearendhelp');
item('reset', "reset game", "<font color=red>Caution!</font>", 'resethelp');
$popup = "<select name=newplayer><option selected value=''>Select a role...</option>\n";
foreach ($zurich->players as $p) {
        $popup .= "<option value=" . $p->id . ">" . $p->name . 
        	"</option>\n";
}
$popup .= "</select>";
echo "<input type=hidden name=frombank value=1>";
item('become', "play as a ", $popup, 'becomehelp');
$popup = "<select name=who><option selected value=''>Select...</option>\n";
foreach ($zurich->players as $p) {
    if ($p->loggedin)
        $popup .= "<option value=" . $p->id . ">" . $p->name . 
        	"</option>\n";
}
$popup .= "</select>";
item('logout', "log out a player<br>", $popup, 'logouthelp');
item('logoutall', "logout all players", "", 'logoutallhelp');
item('auto', "run in auto mode<br>", 
		"How many years? <input type=text name=auto_turns maxlength=7 size=3>",
		'autohelp');
$interval = round($zurich->action_gap/60);
item('turntime', "minimum time between turns, in minutes",
		"<input type=text name=mintimemins maxlength=7 size=4 
		value=$interval>", 'mintimehelp');
item('actionsperyear',"number of player actions per game year",
		"<input type=text name=actionsperyear maxlength=7 size=4 
		value=$zurich->actsperyear>", 'actsperyearhelp');

