<?php

/***********************************************
   Zurich Water Game: internet version
   Author:  Nigel Gilbert and the FIRMA project
   --------------------------------------------
   main.php
   
   This file despatches to the appropriate
   code to display the next web page to the player,
   retrieving and updating as necessary.
   
   Version 1.0  6 August 2001
   Version 1.1  1 September 2001
   Version 2.0  16 November 2001
   Version 2.1  7 April 2002

**********************************************/

include("constants.php");
include("common.php");
include("query.php");
include("objects.php");
include("rules.php");

/*============================================

  Retrieve the environment state (and as a 
  side effect, the players and resources)
  
 ============================================*/

open_database();

$newplayer = get_param('newplayer');
$action = get_param('action');
$msg = get_param('msg');
$requestor = get_param('requestor');
$reasons = get_param('reasons');
$recipient = get_param('recipient');
$id = get_param('id');
$type = get_param('type');
$price = get_param('price');
$counter_offer = get_param('counter_offer');
$quotep = get_param('quotep');
$quote = get_param('quote');
$offer = get_param('offer');
$haggle_reply = get_param('haggle_reply');
$reject_offer = get_param('reject_offer');
$amount = get_param('amount');
$offer = get_param('offer');
$who = get_param('who');
// water utility
$close_res_number = get_param('close_res_number');
$repair_res_number = get_param('repair_res_number');
// manufacturers
$newfactorytype = get_param('newfactorytype');
$repair_factory_number = get_param('repair_factory_number');
$ch_prod_factory_number = get_param('ch_prod_factory_number');
// waste water
$filter_quality = get_param('filter_quality');
// bank
$frombank = get_param('frombank');
$auto_turns = get_param('auto_turns');
$mintimemins = get_param('mintimemins');
$actionsperyear = get_param('actionsperyear');


/*============================================

  Complain if the player has got here without 
  setting the cookie.
  
 ============================================*/

if ($newplayer) {  // here only if changing role from bank to other player or vice versa
	$player = $newplayer;
	setcookie("player", $player, mktime(0,0,0,1,1,2030), '/', '', 0);
	$zurich->players[$player]->loggedin = date("g:i a D, d M");
	$action = "";
}
$player = $_COOKIE['player'];
// sanity check (in case, e.g. bank has reset the game behind the scenes)
if (!$player or !$zurich->players[$player]->loggedin) header("Location: briefing.html");
$playobj = &$zurich->players[$player];  // the object representing the player

/*============================================

   Store a message that the player has written to the Public or
   Private fora
   
============================================*/

$tab_to_display = "bank";	// tab to display when page is shown (default)

if ($msg) {
    db_write("INSERT INTO msgs (sender, recipient, timesent, timeread, msg) 
            VALUES('$player', '$recipient', now(), NULL, '$msg')");
    if ($recipient == "All") {
        log_act("sent a message to the Public Forum", $playobj->name, 2);
    	$tab_to_display = "public";
        }
    else {
    	$r_name = $playobj->id_to_name($recipient);
        log_act("sent a message to $r_name", $playobj->name, 2);
    	$tab_to_display = "private";
        }
}

/*============================================

  It is easy to select a sub-action (e.g. amount
  to borrow from the bank) without clicking the
  corresponding radio button. Set the action
  if this was done by mistake.
  
 ============================================*/
 /* the user's selection (made on the last page the user saw before 
    s/he arrived here) is recorded in $action by the web server. */
 
//foreach ($HTTP_POST_VARS as $k => $v) { echo "$k = $v<BR>";}
if (!$action) {
    if (get_param('who')) $action = 'logout';
    if (get_param('amount')) $action = 'borrow_from_bank';
    if (get_param('repayment')) $action = 'repay_bank';
    if (get_param('price')) $action = 'petition_politician';
    if (get_param('type')) $action = 'request_sanitary_system';
    if (get_param('factory_number')) $action = 'change_production';
    if (get_param('recipient') and get_param('subsidy')) $action = 'subsidise';
}

/*============================================

  Display the game page header
 
 ============================================*/

html_header("Zurich Water Game: $playobj->name");

include("display-top.php");

if ($msg) echo "Message sent.\n";
	
/*============================================

  If the user is carrying out an action,
  run the softbots (agents representing players
  that are not logged in at the moment).  While 
  the softbot is playing, its object is in $softbot
 
 ============================================*/

if ($action) {
    foreach ($zurich->players as $s) {
	    $softbot = &$zurich->players[$s->id];
	    if ($softbot && !$softbot->loggedin) {
	    	log_and_confirm("Robot $softbot->name playing", 'Event', 0);
	        $softbot->play();
	    }
    }
    unset($softbot);
}

/*============================================

  Check whether the player has waited a sufficient interval
  since the last action.  If not, warn and reject
  action.  (Actions that are a response to another player's
  request ($requestor, a variable that is sent when the
  user POSTs a form from a dialogue) is not blank) or
  involve borrowing from/repaying the bank are permitted anytime)
  
 ============================================*/

if ($action and !$requestor and 
	$action != 'borrow_from_bank' and $action != 'repay_bank') {
	if ($playobj->id != 'bank') {
		if ($playobj->last_time and 
			(time() - $playobj->last_time < $zurich->action_gap)) {
				alert("You are not permitted to carry out another action yet!");
				echo "Not enough time has elapsed since your last action.
				You can next carry out an action on " . 
				date("l, jS F \\a\\t g:ia", $playobj->last_time + $zurich->action_gap) .
				".<BR>Request cancelled.<BR>";
				$action="";
			}
	}
}

/*============================================

	Check whether the player has provided a reason
	for the selected action.  If not, object.
	If so, store it 
	
 ============================================*/

if ($action and !$requestor and !($player == 'bank')) {
		if (!$reasons) {
			alert("You have not provided a reason for selecting this action.");
			echo "Please enter a reason in the box at the bottom left
			and reselect the action.<BR>";
			$action = "";
		}
}

/*============================================

  Carry out the requested action
  
 ============================================*/
 
 if ($action) {
	/* branch to the required action.  Note that this requires that the value of 
		$action is the name of a method of Player. */  
	$playobj->$action();
    $zurich->action_count++;
    $playobj->last_time = time();
 	}

/*============================================

  Retrieve any queued requests from other
  players and carry out the requested actions
  
 ============================================*/
 
 /* obtain and process each request in turn */
 
$query = new Query("SELECT id, request, requestor, param1, param2 FROM requests 
					WHERE requestee='$player' 
					ORDER BY id DESC");
while ($query->next_rec()) {
	$method = $query->field('request');
 	$playobj->$method($query->field('id'), $query->field('requestor'), 
 						$query->field('param1'), $query->field('param2'));
 	echo "<br>";
 	}
 	
/*============================================

  Update the world
  
 ============================================*/

/* If the next end of year is now due, carry out the required operations (e.g. collect
   maintenance) */

    if ($zurich->action_count % $zurich->actsperyear == 0) {
    	$zurich->end_of_turn();
    	$zurich->action_count = 1;
    }

/*============================================

   Send an email to all logged in players listing actions 
   that have been carried out in the last 24 hours
   
============================================*/
 
 $now = time();
 
 if ($send_emails) {
 	if (!$zurich->email_notify_time) $zurich->email_notify_time = $now;
 	if ($now - $zurich->email_notify_time > $email_notify_interval) {
 		$query = new query("SELECT name, action, time FROM log 
 							WHERE detail >= '$log_level'"); 							
 		while ($query->next_rec()) {
 			$timestamp = strtotime($query->field('time'));
 			if ($timestamp > $zurich->email_notify_time) {
 				$msg .= date("l, g:i a", $timestamp) . "  " . 
 						$query->field('name') . "  " . 
 						stripslashes($query->field('action')) . "\n";
 				// replace euro symbols 
 				$msg = preg_replace("/&#8364;\s*(\d+)/", "$1 euro", $msg);
 			}
 		}

 		if ($msg) {
	 		foreach ($zurich->players as $p) {
	 			if (isset($p->logged_in) && !$p->logged_in && $p->email) {
	 				$last_date = date("l, g:i a", $zurich->email_notify_time);
	 				$mail_sent = mail($p->email, "Zurich water Game: today's news",
	 					"In the Zurich Water Game, the actions carried out 
	 					since $last_date have been:\n\n$msg", 
	 					"From: \"Zurich Water Game\"<$admin>\n" .
    					"Reply-To: \"Zurich Water Game\"<$admin>");
	 				if (!$mail_sent) echo "Problem sending mail to $p->email<P>";
	 			}
	 		}
	 		$zurich->email_notify_time = $now;
 		}
 	}
 }
 		
/*============================================

  Display bank balance 

============================================*/

include("display-bank.php"); 	
 	
/*============================================

  Display any current messages from other players
  
============================================*/
 
include("display-msgs.php"); 
   	  
/*============================================

  Display the player-specific parts of the page
  
============================================*/

switch ($player) {
 
 	case 'water_utility':  		include("wspage.php"); break;
 	case 'waste_water_utility':	include("wwupage.php"); break;
 	case 'housing_assoc_1': 	include("hapage.php"); break;
 	case 'housing_assoc_2': 	include("hapage.php"); break;
 	case 'manufacturer_1':		include("prodpage.php"); break;
 	case 'manufacturer_2':		include("prodpage.php"); break;
 	case 'politician':			include("polpage.php"); break;
 	case 'bank':				include("bankpage.php"); break;
 	}
 	
/*============================================

 Display the actions available to all players

============================================*/

include("display-generic.php");

/*============================================

 Display the help messages for these actions 

============================================*/

include("display-help.php");
 	 	
/*============================================

 Display a log of recent actions 

============================================*/

 include("display-log.php");

/*============================================

  Display the indicator scales
  
============================================*/
 
/* update the water supply and demand indicators */
 
$zurich->measure_water_indicators();

include("display-scales.php");

html_footer();

/*============================================

  Save the player and environment states
  
============================================*/
$playobj->last_refresh = $now;
 
// save a serialized copy of all objects
db_save($zurich, "Zurich", "objects");
?>
