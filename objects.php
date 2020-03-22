<?php

/***********************************************
   Zurich Water Game: internet version
   Author:  Nigel Gilbert and the FIRMA project
   --------------------------------------------
   objects.php
   
   This file contains the class definitions for
   the objects of the game (players, resources
   etc.)
   
   Version 1.0  6 August 2001
   Version 1.1  1 September 2001
   Version 2.0  28 Occtober 2001
   Version 2.1  7 April 2002

**********************************************/

/*============================================

  An object to represent the environment
  
 ============================================*/
 
 Class Zurich {
        
        /*the parameters that describe the current state */

 	var $water_demand;
 	var $water_supply;
 	var $water_price;
 	var $water_quality;
 	var $political_popularity;
 	var $lake_quality;
 	var $env_awareness;
 	
 	var $max_water_quality;	/* records states during a turn */
 	var $max_lake_quality;
 	var $min_lake_quality;
 	
 		/* some convenient variables for global use */
	
	var $players;   			// array of the objects repesenting the players
	var $clock; 				// the time of day, (as a Unix timestamp)
	var $time;					// the time of day (as a string)
	var $action_count;			// total number of actions any player has made
	var $email_notify_time;		// time when last email was sent to players 
	var $actsperyear;			// number of actions between year ends (excludes softbot actions)
	var $action_gap;			// minimum period between actions for each player (in secs.)
	var $turn;					// count of years since the game began

	function zurich() {
		/* constructor function; populates the environment with players and 
		   resources */
		   
		/* the player constructors give the players their initial resources */
		   
		$this->players['water_utility'] = 
			new Water_utility('water_utility', 'Water Utility');
		$this->players['waste_water_utility'] = 
			new Waste_Water_utility('waste_water_utility', 'Waste Water Utility');
		$this->players['housing_assoc_1'] = 
			new Housing_association('housing_assoc_1', 'Housing Association 1');
		$this->players['housing_assoc_2'] = 
			new Housing_association('housing_assoc_2', 'Housing Association 2');
		$this->players['manufacturer_1'] = 
			new Manufacturer('manufacturer_1', 'Manufacturer 1');
		$this->players['manufacturer_2'] = 
			new Manufacturer('manufacturer_2', 'Manufacturer 2');
		$this->players['politician'] = 
			new Politician('politician', 'Politician');
		$this->players['bank'] = new Bank('bank', 'Bank');
		
		/* initial environmental state */
		
		$this->water_supply = 0; // set in measure_water_indicators()
		$this->water_demand = 0; // set in measure_water_indicators()
		$this->water_price = 5;
		$this->water_quality = 8;
		$this->political_popularity = 5;
		$this->lake_quality = 6;
		$this->env_awareness = 5;
		
		$this->set_time();
		$this->turn = 0;
		$this->action_count = 1;
		
		$this->max_water_quality = 10;	
 		$this->min_water_quality = 0;
 		$this->min_lake_quality = 9999;
 		
 		/* others */
 		$this->actsperyear = 5;
		$this->action_gap = 1 * 60;	// 1 minutes


	}
 
 	function set_time($time = 0) {
 		/* sets the time of day to supplied time (secs from the beginning of Unix time)
 		or now */
 	
 		if (!$time) $time = time();
 		$this->clock = $time;
 		$this->time = date('r', $this->clock);
 	}
 	
 	function total_demand() {
 		/* returns the total demand for water by all the houses
 		owned by housing associations */
 		
 		return $this->players['housing_assoc_1']->water_demand() +
 		       $this->players['housing_assoc_2']->water_demand();
 	}
 	
 	function total_supply() {
 		/* returns the total supply of water provided by all the
 		reservoirs owned by the water utility */
 		
 		return $this->players['water_utility']->water_supply();
 		}
 	
 	function lake_water_quality() {
		/* return the lake water quality indicator */
				
		return round(($this->total_demand() * 
			$this->players['waste_water_utility']->filter_level)/30);
	}
	
	function measure_water_indicators() {
		/* sets the global environmental indicators */
		
		$this->water_demand = $this->total_demand();
		$this->water_supply = $this->total_supply();
		$this->lake_quality = $this->lake_water_quality();
		if ($this->water_quality > $this->max_water_quality) 
				$this->max_water_quality = $this->water_quality;
		if ($this->water_quality < $this->min_water_quality) 
				$this->min_water_quality = $this->water_quality;
		if ($this->lake_quality < $this->min_lake_quality) 
				$this->min_lake_quality = $this->lake_quality;
	}
 	 	
 	function change_water_quality($delta) {
 		/* increase the water quality index by $delta, but guard against
 		the index going out of range. Return actual change */

 		$new_amount = $delta + $this->water_quality;
 		If ($new_amount > 10) $new_amount = 10;
 		if ($new_amount < 0) $new_amount = 0;
 		$delta = $new_amount - $this->water_quality;
 		$this->water_quality = $new_amount;
 		return $delta;
 	}
 	
 	function end_of_turn() {
 		/* for each player:
			1	apply strategy rules;
			3	pay maintenance on resources to bank;  note order reversed! 14/9/02 to avoid getting into debt unnecessarily 
			2	collect income;
			4	add 1 to the age of each resource.
			
			Note the tricky semantics of PHP here.  foreach works on a *copy*
			of the array in something like foreach ($this_players as $p).  Hence 
			if you use this, and try to advance the age of the resources in $p, 
			you'll actually be changing an anonymous *copy* of the resources! 
			Oh for lisp which has none of this bother! */
				
		log_act("**It is the end of the year and the bills are due:", "", 10);
		$this->measure_water_indicators();
		apply_system_rules();
		foreach (array_keys($this->players) as $k) {
			if ($k) $this->players[$k]->income();
		}
		foreach (array_keys($this->players) as $k) { 
			if ($k) $this->players[$k]->maintenance();
		}
		foreach (array_keys($this->players) as $k) {
			if ($k) $this->players[$k]->age_resources(1);
		}
		
		$this->turn++; 
 	}
 }

/*============================================

  Resource class definitions
  
 ============================================*/
 
 
Class Resource {

	 var $maintenance_pt;   // maintenance charge per turn
	 var $income_pt;        // fixed income per turn
	 var $age;              // age of this resource in years
	 var $max_age;          // max age of the resource before it stops 
	 						//   functioning
	 
	 function make_older($change) {
	 	/* age the resource by $change */
	 	
	 	$this->age += $change;
	 }
	
}

/*-------------------------------------------

  Factory class definitions
  
 -------------------------------------------*/
					
 	
Class Factory extends Resource {
 
 	/* either a normal or a water saving factory.  To change from one to
 	the other, use change_type().  Initially, a normal factory */
 
 	var $building_cost;
 	var $change_of_use_cost;
 	var $repair;
 	var $type;
 	
 	function factory() {
 	/* constructor: set initial values of parameters for a normal factory */
 		
 		$this->building_cost = 800;
 		$this->change_of_use_cost = 600;
 		$this->repair_cost = 500;
 		$this->maintenance = 75;
 		$this->max_age = 8;
 		$this->type = 'normal';
 		$this->age = mt_rand(0,$this->max_age - 1); // start with an age between 0 and max_age
 	}
 	
 	function change_type() {
 	/* change the type of this factory from Normal to one that makes Water saving 
 	technology, or vice versa.  Returns new type */
 	
 		if ($this->type == 'normal') { // change to water saving technology 
 		 	$this->building_cost = 1200;
			$this->change_of_use_cost = 600;
			$this->repair_cost = 500;
			$this->maintenance = 75;
			$this->type = 'water saving';
			} 
		else {                         // change to normal technology                  
			$this->building_cost = 800;
			$this->change_of_use_cost = 600;
			$this->repair_cost = 600;
			$this->maintenance = 75;
			$this->type = 'normal';
			}
		$this->age = 0;               // reset age to zero
		return $this->type;
	}
}

/*-------------------------------------------

  House class definitions
  
 -------------------------------------------*/

Class House extends Resource {

	var $type;

	function house() {
		/* constructor */
		
		$this->maintenance_pt = 25;
		$this->income_pt = 175;
		$this->max_age = 8;
		$this->age = mt_rand(0, $this->max_age - 1);
		$this->type = 'normal';
		}
		
	function water_demand() {
		/* return the amount of water used by this house */
		
		global $zurich;
		
		switch(floor($zurich->env_awareness / 4)) {
			case 0:		// awareness 0 - 3
				return ($this->type == 'normal' ? 8 : 5);
			case 1:     // awareness 4 - 7
				return ($this->type == 'normal' ? 6 : 4);
			case 2:     // awareness 8 - 10
				return ($this->type == 'normal' ? 4 : 2);
			default:
				echo "Env. awareness out of range:$zurich->env_awareness!";
			}
	}
	
	function fit_sanitary_system($type) {
		/* change the type of the house to $type and reset age */
		
		$this->type =  $type;
		$this->age = 0;
	}

}

/*-------------------------------------------

  Reservoir class definitions
  
 -------------------------------------------*/
 
Class Reservoir extends Resource {

	var $building_cost;
	var $repair_cost;

	function reservoir() {
		/* constructor */
		
		$this->building_cost = 1500;
		$this->repair_cost = 500;
		$this->maintenance_pt = 30;
		$this->max_age = 10;
		$this->age = mt_rand(0, $this->max_age - 1);
	}
		
}

/*============================================

  Player class definitions
  
 ============================================*/
 
Class Player {

	var $id;				// short identifier for this player
	var $name;				// the literal name of this player as a string
	var $account;			// current cash balance of this player
	var $loggedin;			// a human is playing this role
	var $email;				// email address of this role's player
	var $realname;			// name of this role's player
	var $last_time;			// the time when the last action was carried out by the player
	var $last_refresh;		// the time when the player's page was last downloaded from the server
	var $paid_off_debts;	// player has paid off borrowings during the turn
	var $rating_weight;		// array of this player's chosen weights for the 5 rating criteria
	
	function id_to_name($id) {
		/* given a player id, returns the literal name */
	
		global $zurich;
	
		return $zurich->players[$id]->name;
	}
	
	function &id_to_obj($id) {
		/* returns a reference to the object with identifier id */
		
		global $zurich;
		
		return $zurich->players[$id];
	}
	
	function rating() {
		/* Return my current rating score.  Answer should be in range 0 to 10.
		First normalise weights so that their sum is one.  Then work out each component score,
		and multiply by the corresponding normalised weight */
		
		global $zurich;
		
		$weight_sum = 0 ;
		for ($r = 0; $r < 5; $r++) {
			$weight_sum += $this->rating_weight[$r];
		}

		$rating = 0;
		for ($r = 0; $r < 5; $r++) {
			/* get this player's score for each component of the rating */
			switch ($r) {
				case 0: /* supply > demand */
					$score = ($zurich->water_supply >= $zurich->water_demand ? 10 : 0); break;
				case 1: /* water price is low */
					switch (true) {
						case ($zurich->water_price <= 1): $score = 10; break;
						case ($zurich->water_price <= 3): $score = 5; break;
						case ($zurich->water_price <= 5): $score = 1; break; 
						default: $score = 0;
					}
					break;
				case 2: /* political popularity is high */
					switch (true) {
						case ($zurich->political_popularity >= 9): $score = 10; break;
						case ($zurich->political_popularity >= 7): $score = 5; break;
						case ($zurich->political_popularity >= 5): $score = 1; break; 
						default: $score = 0;
					}
					break;
				case 3: /* lake water quality is high */
					switch (true) {
						case ($zurich->lake_quality >= 9): $score = 10; break;
						case ($zurich->lake_quality >= 7): $score = 5; break;
						case ($zurich->lake_quality >= 5): $score = 1; break; 
						default: $score = 0;
					}
					break;
				case 4: /* profit is high */
					$score = $this->account / 200; 
					if ($score > 10) $score = 10;
					break;
				default: alert("Unknown rating in Player->rating");
			}
			$rating += $this->rating_weight[$r] * $score;
			
#		  echo "component = $r rating_weight = " . $this->rating_weight[$r] . " weight_sum= $weight_sum" .
#	  			" score= $score rating= $rating answer = " . $rating/$weight_sum . "<P>";

		}
		return round($rating/$weight_sum);
	}
	
	function benefit($whom, $amount) {
		/* receive some money from $whom (an id) */
		
		$this->account += $amount;
		log_and_confirm("received " . CURRSYM . "$amount from " . 
			$this->id_to_name($whom), $this->id, 0);
		return $amount;
	}
	
	function pay($whom, $amount) {
		/* pay someone (id given in $whom) the $amount.  First check that they
		have enough money.  If not, if they are logged in, return 0, otherwise
		borrow from the bank; if they have, return the amount paid. */
				
		if ($this->account - $amount < 0) { 
		    if ($this->loggedin) return 0;
		    $this->overdraw($amount - $this->account);
		    }
		$this->account -= $amount;
		log_and_confirm("paid " . CURRSYM . "$amount to " . 
			$this->id_to_name($whom), $this->id, 0);
		$whom_obj =& $this->id_to_obj($whom); 
		return $whom_obj->benefit($this->id, $amount);
		}
		
	function cant_pay($amount) {
		/* player has attempted to pay out more than is available in their account;
		complain */
		
		global $player;

		if ($this->id == $player) {
			/* this will normally be the case, but when the bank forces
			an end of year, $playobj is the bank, and $this is the
			player paying maintenance etc. */
			echo "You have tried to pay out more than is in your 
			current account.  The bank will not allow unplanned overdrafts. 
			You will have to arrange to borrow from the bank.  
			Action cancelled.";
			}
	}
	
	function must_pay($whom, $amount) {
		/* pay someone (id given in $whom) the $amount.  If there is not that
		much in the account, first borrow sufficient from the bank. */
				
		if ($this->account - $amount < 0) {
			/* must borrow; borrow amount rounded up to nearest 100 */
			$loan = $this->overdraw($amount - $this->account);
		    /* leave a warning for the player to collect next go */ 
		    $this->overdraft_note($loan);
		}
		$this->account -= $amount;
		log_and_confirm("paid " . CURRSYM . "$amount to " . $this->id_to_name($whom), $this->id, 0);
		$whom_obj =& $this->id_to_obj($whom); 
		return $whom_obj->benefit($this->id, $amount);
	}
    
    function overdraft_note($loan) {
    	/* note the overdraft for when the user logs in.  If there is already an overdraft,
    	 	just update the existing record */
    	
    	$query = new Query("SELECT param1 FROM requests 
    						WHERE requestor='$this->id' and request='overdraft'");
    	if ($query->next_rec()) {
    		$new_loan = $loan + $query->field('param1');
    		db_write("UPDATE requests SET param1= '$new_loan' 
    					WHERE requestor='$this->id' and request='overdraft'");
    	}
    	else {
    		$this->request($this->id, 'overdraft', $loan);
    	}
    }
    
    function overdraw($amount) {
    	/* silently borrow an amount from the bank.  Return amount
    	borrowed (rounded up to nearest 100 from $amount) */
		$loan = 100 * ceil($amount/100);
		$this->account += $loan;
	    $bank_obj =& $this->id_to_obj('bank'); 
	    $bank_obj->lend($this->id, $loan);
	    log_and_confirm("borrowed " . CURRSYM . "$loan from the bank", $this->id, 2);
	    return $loan;
    }

	function overdraft($id, $requestor, $amount) {
		/* warn user that an overdraft has had to be arranged */
		
		echo "You did not have enough in your account to pay the 
		bills that were due for maintenance etc.  The Bank has therefore
		automatically provided you with overdraft facilities and lent you 
		the  " . CURRSYM . "$amount that you needed.";
		$this->delete_request($id);
    }
    
    function play() {
    	/* method to be over-ridden by each player role!
    	    Runs the player strategy while the real player is not
    	    logged in */
    	
    	log_and_confirm("Running softbot", $this->id, 1);
    	/* by default, pick a chance card */
    	$this->pick_chance_card();
    }

	/* generic actions */
	
	function advertise($state = 0) {
		/* advertise to (hopefully) improve the given environmental state */
		
		global $zurich;
		
 		if (!$state) $state = get_param('state');
 		$cost = 500;
		if (!$this->pay('bank', $cost)) {
			$this->cant_pay($cost);
			return;
			};
		$delta = dice_up($zurich->$state, -1, 3);
		$zurich->$state += $delta;
		$state_name = state_to_name($state);
		if ($delta) {
			log_and_confirm("spent " . CURRSYM . "$cost on advertising resulting in " .
		        ($delta >= 0 ? "increasing " : "decreasing ") .
		        "$state_name by " . abs($delta), 
				$this->id, 3);
			}
		else {
			log_and_confirm("spent " . CURRSYM . 
				"$cost on advertising to raise $state_name, but to no effect!", 
				$this->id, 3);
		}	
	}
		
	function borrow_from_bank() {
		/* increase my account by the amount borrowed */
	
 		$amount = get_param('amount');
 		if ($amount == 0) {
 			echo "You must say how much you want to borrow.";
 			return;
 		}
		log_and_confirm("borrowed " . CURRSYM . 
			"$amount from the bank at an interest rate of " . 
			100 * $this->interest_rate() . "%", $this->id, 3);
		$this->account += $amount;
		$bank_obj =& $this->id_to_obj('bank');  
		$bank_obj->lend($this->id, $amount);
	}
	
	function interest_rate() {
		/* return the current interest rate */
		
			global $zurich;
			
			return $zurich->players['bank']->interest_rate;
	}
	
	function pay_interest($interest) {
		/* pay interest on borrowings to the bank */
		
		$this->must_pay('bank', $interest);
		$this->warn_player("paid " . CURRSYM . "$interest in interest on the overdraft to the bank", $this->id, 2);
	}
	
	function repay_bank() {
		/* repay the bank for a loan (may be partial repayment)
		No check for overpayment */
		
		$amount = get_param('repayment');
		if (!$this->pay('bank', $amount)) {
			$this->cant_pay($amount);
			return;
			}		
		$bank_obj =& $this->id_to_obj('bank'); 
		$bank_obj->repay($this->id, $amount);
		
		log_and_confirm("repaid " . CURRSYM . "$amount of borrowings to the bank",
			$this->id, 3);
		
		/* if the debt has been paid off, note this for system rules */
		if (!$this->debtor()) $this->paid_off_debts = TRUE;
	}
	
	function debtor() {
		/* returns amount borrowed if this player owes money to the bank */
		
		$bank_obj =& $this->id_to_obj('bank');
		if (isset($bank_obj->debtors[$this->id]))
			$debt =  $bank_obj->debtors[$this->id];
		else $debt = 0;
		return ($debt > 0 ? $debt : 0);
	}
		
	
	function pick_chance_card() {
		/*  pick and carry out a chance card (actually an event, 
		since strategy cards are not implemented), with a probability of 1 in 5 */
		
		if (mt_rand(0, 100) < 20) event();
	}
	
	function warn_player($text, $who = "", $loglevel = "") {
		/* if player is logged in, display text on the screen,
		or if not logged in, leave the text in the database
		for display when the player does log in.
		If loglevel is given, also copy text to the log */

		global $player;
		
		if ($loglevel) log_act($text, $who, $loglevel);
		if ($who != 'Event') $text = "You " . $text;
		if ($this->id == $player and $this->loggedin) echo "$text<BR>";
		else $this->request($this->id, 'warning', $text);
	}
	
	function warning($id, $requestor, $text) {
		/* get here if a warning has been stored away for when the player
		logs in.  Display the warning */
		
		echo "$text<BR>";
		$this->delete_request($id);
	}
	
	function request($who, $what, $arg1, $arg2="") {
		/* deposit a request in the queue, for another player or myself
		(if $who == $player) when I log in, to pick up */
		
		global $zurich;
		
		/* to prevent duplicate requests, delete any existing request that has the same parameters as this one */
		db_write("DELETE FROM requests 
						WHERE requestor='$this->id' 
						AND requestee='$who' AND request='$what' AND param1='$arg1'");
		
		db_write("INSERT INTO requests (time, requestor, requestee, request, param1, param2)
						VALUES('$zurich->time', '$this->id', '$who', '$what', '$arg1', '$arg2')");
	}
	
	function delete_request($id) {
		/* delete a request from the queue because it has been completed */
		
		db_write("DELETE FROM requests WHERE id='$id'");
	}
}

/*-------------------------------------------

  Water utility class definitions
  
 -------------------------------------------*/
	
	
Class Water_utility extends Player {

	var $reservoirs;		// list of open reservoirs I own;
	
	function water_utility($id, $name) {
		/* constructor.  Give the water utility 5 reservoirs to start */

		$this->id = $id;
		$this->name = $name;
		for ($r = 0; $r < 5; $r++) {
			$this->reservoirs[chr(ord('a') + $r)] = new Reservoir();
			}
		$this->account = 1000;
		/* default rating weights */
		$this->rating_weight = array(3, 3, 2, 1, 3);
	}
	
	function water_supply() {
		/* returns the water supplied by my reservoirs 
		(amount of water per reservoir is 20 units) */
		
		$supply = 0;
		foreach ($this->reservoirs as $r) {
				if ($r->age < $r->max_age) $supply += 20;
		}
		return $supply;
	}
	
	function age_resources($change) {
		/* make the reservoirs $change years older */
		
		foreach ($this->reservoirs as $k=>$r) {
			$this->reservoirs[$k]->make_older($change);
			if ($r->age == $r->max_age - 2) {
				$this->warn_player("Reservoir $k is near to the end of its life", 'Event', 3);
			}
			if ($r->age == $r->max_age) {
				$this->warn_player("Reservoir $k was closed because it was at the end of its life", 
							'Event', 3);
				unset($this->reservoirs[$k]);
				}
		}
	}		

	function maintenance() {
		/* pay maintenance on reservoirs to the bank */
		
		$fee = 0;
		foreach ($this->reservoirs as $r) {
			$fee += $r->maintenance_pt;
			}
		if ($fee) {
			$this->must_pay('bank', $fee);
			$this->warn_player("paid maintenance fee of " . CURRSYM . "$fee on reservoirs", $this->id, 2);
			}
	}
	
	function income() {
		/* nothing to do */
	}
			
	function oldest() {
		/* return the index of the oldest reservoir or -1 if there are none*/
		
		$age_of_oldest = -1;
		while (list ($key, $r) = each($this->reservoirs)) {
			if ($r->age > $age_of_oldest) {
				$age_of_oldest = $r->age;
				$oldest = $key;
				}
			}
		return $oldest;
	}
		
	function decrease_capacity () {
		/* close the specified or oldest reservoir, 
		and update water supply scale*/
		
		global $zurich;
		
		$key = get_param('close_res_number'); 
		if(!$key) $key = $this->oldest();
		if ($key) {
		    log_and_confirm("reduced capacity by closing a reservoir", $this->id, 3);
			unset($this->reservoirs[$key]);
			}
		else {
	        log_act("No reservoir to close!", $this->id, 3);
		}
	}	
		
	function increase_capacity () {
		/* add an extra reservoir to the list of reservoirs */
		
		global $zurich;
		
		$new_reservoir = new Reservoir();
		$new_reservoir->age = 0;
		$cost = $new_reservoir->building_cost;
		if (!$this->pay('bank', $cost)) {
			$this->cant_pay($cost);
			return;
			}
		$new_key = 'a';
		while ($this->factories[$new_key]) {
			$new_key = chr(ord($new_key) + 1);
		}	
		$this->reservoirs[$new_key] = $new_reservoir;
		log_and_confirm("increased capacity by building reservoir \"$new_key\"", $this->id, 3);
	}
	
	function repair_reservoir() {
		/* repair the specified or oldest reservoir, and reset its age to 0 */
		
		$key = get_param('repair_res_number');
		if(!$key) $key = $this->oldest();
		if ($key) {
			$cost = $this->reservoirs[$key]->repair_cost;
			if (!$this->pay('bank', $cost)) {
				$this->cant_pay($cost);
				return;
				}
			$this->reservoirs[$key]->age = 0;
			log_and_confirm("repaired reservoir \"$key\"", $this->id, 3);
			}
	}
	
	function improve_water_quality () {
		/* improve the water quality by a random amount */
		
		global $zurich;
		
		$delta = mt_rand(1,3);
		$cost = 1000;
		if (!$this->pay('bank', $cost)) {
			$this->cant_pay($cost);
			return;
			}
		$real_delta = $zurich->change_water_quality($delta);
		log_and_confirm("improved the water quality by +$real_delta at a cost of " . 
			CURRSYM . "$cost", $this->id, 3);
		}
		
	function petition_politician ($price=0) {
		/* make a request to the politician for a change in water price */
		
		 if (!$price) $price = get_param('price');
		 $this->request('politician','request_price_change', $price);
		 log_and_confirm("asked the Politician to change the water price to " . CURRSYM . "$price", $this->id, 3);
	}
	
	function price_change_approved($id, $requestor, $amount, $ignore) {
		/* tell player the price change was approved */
		
		global $zurich;
		
		if ($amount <= 0) $amount = 1;
		if ($amount > 100) $amount = 10;
		$change = $amount - $zurich->water_price; 
		$zurich->water_price = $amount;
		echo "Your request for a water price change to " . CURRSYM . "$amount has been approved\n";
		log_and_confirm("changed the price of water to " . CURRSYM . "$amount", $this->id, 3);
		$this->delete_request($id);
	}
	
	function price_change_disapproved($id, $requestor, $amount, $ignore) {
		/* tell player the price change was not approved */
		
		echo "Your request for a water price change 
		to " . CURRSYM . "$amount has NOT been approved<P>";
		$this->delete_request($id);
	}
    
    function play() {
    	/* carry out default strategy while human player is not around: 
    	
    	if water_supply >= 1.5 * water_demand then close a reservoir
    	else if oldest reservoir is max_age or older then repair a reservoir
    	else if water quality < 5 then improve water quality
    	else if water demand > 0.8 * water supply then advertise environment
    	else if borrowing money then ask for water price increase
    	else take a chance card */
    	
    	global $zurich;
    	
    	/* get any outstanding requests and deal with them */
    	
    	$query = new Query("SELECT id, request, requestor, param1, param2 FROM requests 
					WHERE requestee='water_utility' 
					ORDER BY time DESC");
        while ($query->next_rec()) {
        	$id = $query->field('id');
        	$request = $query->field('request');
        	$requestor = $query->field('requestor');
 			$amount = $query->field('param1');
 			if ($request == 'price_change_approved') {
 			    /* price change */
 			    $zurich->water_price = $amount;
 			    log_and_confirm("changed the price of water to " . CURRSYM . "$amount.", 
 			    	$this->id, 3);
 			}
 			/* do nothing with price change disapprovals */    
 		    $this->delete_request($id);
        }

    	if ($zurich->water_supply >= 1.5 * $zurich->water_demand) {
    		$this->decrease_capacity(); 
    		return;
    	}
    	$oldest_id = $this->oldest();
    	if ($oldest_id and $this->reservoirs[$oldest_id]->age >= 
    	        $this->reservoirs[$oldest_id]->max_age) {
	    	$this->repair_reservoir(); 
	    	return;
	    }
    	if ($zurich->water_quality < 5) {
		    $this->improve_water_quality(); 
		    return;
    	}
    	if ($zurich->water_demand > 0.8 * $zurich->water_supply and
    			$zurich->env_awareness < 8) {
		    $this->advertise('env_awareness'); 
		    return;
    	}
    	if ($this->debtor() and $zurich->water_price < 10) {
    		$this->petition_politician($zurich->water_price + 1); 
    		return; 
    	} 
    	$this->pick_chance_card();
    }
}

/*-------------------------------------------

  Waste Water Utility class definitions
  
 -------------------------------------------*/
	
	
Class Waste_Water_utility extends Player {

	var $filter_level;		/* current level of the filter system 
								(1 = mechanical; 
								 2 = plus nutrient
								 3 = plus biological) */
	
	function waste_water_utility($id, $name) {
		/* constructor.  Start with mechanical only */
		
		$this->id = $id;
		$this->name = $name;
		$this->filter_level = 1;
		$this->account = 1000;
		$this->rating_weight = array(2, 2, 2, 3, 3);
	}
	
	function age_resources($change) {
		/* nothing to do */
	}

	function maintenance() {
		/* pay the cost of filtration, depending on the type in use */
		
		switch ($this->filter_level) {
			case 1: $cost = 100; break;
			case 2: $cost = 200; break;
			case 3: $cost = 300; break;
		}
		
		$this->must_pay('bank', $cost);
		$this->warn_player("paid the maintenance cost of filtration (" . CURRSYM . "$cost)", $this->id, 2);
	}
					
	function income() {
		/* income comes from the housing associations, as a flat fee per house, 
		plus a fee per unit of water used */
		
		/* nothing to do here - payments are made by HAs */
	}
	
	function flush_pipes() {
		/* flush the pipes because they are filthy */
		
		$pipe_flush_cost = 500;		
		$this->must_pay('bank', $pipe_flush_cost);
		log_and_confirm("flushed waste pipes to clean them at a cost of " . CURRSYM . "$pipe_flush_cost", 
			$this->id, 2);
	}
	
	function change_filter_level($new_quality = 0) {
		/* change the filtration quality */
		
		if(!$new_quality) $new_quality = get_param('filter_quality');
		$this->filter_level = $new_quality;
		switch ($this->filter_level) {
			case 1: $filter_type = "Mechanical"; break;
			case 2: $filter_type = "Mechanical and Nutrient"; break;
			case 3: $filter_type = "Mechanical, Nutrient and Biological"; break;
		}
		log_and_confirm("changed the filtration type to $filter_type", $this->id, 3);
	}
	
	function play() {
    	/* carry out default strategy while human player is not around: 
    	
    	Change filtration level according to level of current account
    	else take a chance card  */
    	
    	global $zurich;
    	
    	if ($this->account > 1000) $filtration = 3;
    	elseif ($this->account > 700) $filtration = 2;
    	else $filtration = 1;
    	if ($filtration != $this->filter_level) {
    		$this->change_filter_level($filtration);
    		return;
    	}
    	$this->pick_chance_card();
	}
}
	
/*-------------------------------------------

  Housing Association class definitions
  
 -------------------------------------------*/

Class Housing_association extends Player {

	var $houses;  						// list of houses owned by the association
	var $water_standing_charge = 25; 	// charge per house for clean water supply
	var $waste_standing_charge = 5; 	// charge per house for waste water removal
	var $waste_variable_charge = 1;  	// charge per unit demand for waste water removal
	
	function housing_association($id, $name) {
		/*constructor */
		
		$this->id = $id;
		$this->name = $name;

		for ($h = 0; $h < 5; $h++) {
			$this->houses[chr(ord('a') + $h)] = new House();
			}
		$this->account = 1000;
		$this->rating_weight = array(3, 3, 1, 1, 2);
	}	

	function water_demand() {
		/* returns the water required by my houses, accumulating the
		amount required by each house */
		
		$demand = 0;
		foreach ($this->houses as $h) {
			if ($h->age < $h->max_age) $demand += $h->water_demand();
			} 
		return $demand;
	}
	
	function age_resources($change) {
		/* make the houses $change years older */
		
		foreach ($this->houses as $k=>$r) {
			$this->houses[$k]->make_older($change);
			if ($r->age == $r->max_age - 2) 
				$this->warn_player("The water system in house $k is near to the end of its life", 
					'Event', 3);
			if ($r->age == $r->max_age) 
				$this->warn_player("The water system in house $k reached the end of its life", 
					'Event', 3);
		}
	}		
		
	function buy_water() {
		/* buys the water required by my houses */
		
		global $zurich;
		
		$bill = 0;
		foreach ($this->houses as $h) {
			if ($h->age < $h->max_age) $bill += $this->water_standing_charge;
			}
		$bill += $zurich->water_price * $this->water_demand();
		$this->must_pay('water_utility', $bill);
		$this->warn_player("purchased water for the housing stock, costing " . CURRSYM . "$bill", 
				$this->id, 2);		
	}
	
	function buy_waste_water() {
		/* pays the sewage charges on my houses */
		
		$bill = 0;
		foreach ($this->houses as $h) {
			if ($h->age < $h->max_age) $bill += $this->water_standing_charge;
			}		
		$bill += $this->waste_variable_charge * $this->water_demand();
		$this->must_pay('waste_water_utility', $bill);
		$this->warn_player("paid " . CURRSYM . "$bill for waste water removal from houses", 
			$this->id, 2);		
	}
	
	function maintenance() {
		/* pay maintenance on houses to the bank.  Then pay the water
		 utility and the waste water utility for the water used. */
		
		$fee = 0;
		foreach ($this->houses as $h) {
			if ($h->age < $h->max_age) $fee += $h->maintenance_pt;
			}
		$this->must_pay('bank', $fee);
		$this->warn_player("paid maintenance fee of " . CURRSYM . "$fee on houses", $this->id, 2);
		$this->buy_water();
		$this->buy_waste_water();
	}
	
	function income () {
		/* obtain rent for each house not yet older than max years from the bank. 
			Pay politician his tax */
			
		global $zurich;
		
		$rent = 0;
		foreach ($this->houses as $h) {
			if ($h->age < $h->max_age) $rent += $h->income_pt;
			}
		if ($rent) {
			$this->benefit('bank', $rent);
			$this->warn_player("received rent of " . CURRSYM . "$rent for houses", $this->id, 2);
			$tax = round($rent * $zurich->players['politician']->tax_rate);
			$this->must_pay('politician', $tax);
			$this->warn_player("paid tax of " . CURRSYM . "$tax to the politician", $this->id, 2);
			}
	}
	
	function request_sanitary_system ($type=0) {
		/* make a request to the manufacturers for a quote for a sanitary system */
		
		 if (!$type) {
		 	$type = get_param('type');
		 	}
		 if (!$type) {
		 	echo("You must specify the type of the sanitary system: normal 
		 	    or water saving");
		 	    return;
		 }
		 $this->request('manufacturer_1','request_quote', $type);
		 $this->request('manufacturer_2','request_quote', $type);
		 log_and_confirm("requested a quote for a $type sanitary system", $this->id, 3);
	}
	
	function quotation($id, $requestor, $type, $price) {
		/* display and ask player to accept a quotation for a sanitary system
		from a manufacturer */
		
 		$man_name = $this->id_to_name($requestor);
 		log_and_confirm("received a quote of " . CURRSYM . "$price from $man_name",
 				$this->id, 1); 		
 		echo "<form method=post action=main.php>
 			<input type=hidden name=id value=$id>
 			<input type=hidden name=requestor value=$requestor>
 			<input type=hidden name=price value=$price>
 			<input type=hidden name=type value=\"$type\">
 			$man_name has provided a quote of
 			" . CURRSYM . "$price for the cost of a <BR>$type sanitary system.<BR>
 			Do you want to <input name=action type=radio 
 								value=accept_sanitary_system>Accept
 			or <input name=action type=radio 
 								value=reject_sanitary_system>Reject this quote<BR>
 			or <input name=action type=radio 
 								value=haggle>Offer to buy, but only at a lower price?<BR>
 			If you want to make an offer, how much are you<BR> prepared to pay? ". CURRSYM . "
 			<input name=counter_offer type=text maxlength=7  size=7><BR>
 			If you accept the quotation, which house should be fitted <BR>with the new system? " . 
 			make_popup('quote_house_number', $this->houses) . 
 			"<BR>\n" . 
 			reply_button() . "</form>\n";
}	
 	
 	function no_quotation($id, $utility, $type, $ignore) {
 		/* note that a manufacturer has declined to quote */
 	
 		$utility_name = $this->id_to_name($utility);
		echo "$utility_name has declined to quote for the supply of a $type system";
		$this->delete_request($id);
		}
		
	function accept_sanitary_system() {
		/* accept the sale of a sanitary system for a house */
		
		$id = get_param('id');
		$requestor = get_param('requestor');
		$house_key =  get_param('quote_house_number');
		$price = get_param('price');
		$type = get_param('type');
		if (!$this->pay($requestor, $price)) {
			$this->cant_pay($price);
			return;
			}
		$this->houses[$house_key]->fit_sanitary_system($type);
		$man_name = $this->id_to_name($requestor);
		log_and_confirm("fitted house \"$house_key\" with a new $type sanitary system from $man_name", 
			$this->id, 3);
		$this->request($requestor, 'provide_system', $type, $price);
		$this->delete_request($id);
		/* delete the request for a quote to the other manufacturer if it still
			hasn't been answered (it is no longer relevant, since this manufacturer's
			quote has been accepted) */
		db_write("DELETE FROM requests 
			WHERE request='request_quote' AND requestor='$this->id' AND param1='$type'");
	}
	
	function reject_sanitary_system() {
		/* tell manufacturer that their offer of a system was not accepted */
		
		$id = get_param('id');
		$type = get_param('type');
		$price = get_param('price');
		$requestor = get_param('requestor');
		$this->request($requestor, 'reject_system', $type, $price);
		$this->delete_request($id);
		echo "The quotation was rejected.<BR>";
		}
		
	function haggle() {
		/* provide a counter offer to the manufacturer */
		$id = get_param('id');
		$type = get_param('type');
		$requestor = get_param('requestor');
		$offer = get_param('counter_offer');
		$this->request($requestor, 'haggle', $type, $offer);
		$this->delete_request($id);
		echo "An offer of " . CURRSYM . "$offer was sent to the manufacturer.<BR>";
		}

    
    function oldest() {
		/* return the index of the oldest house */
		
		$age_of_oldest = -1;
		while (list ($key, $r) = each($this->houses)) {
			if ($r->age > $age_of_oldest) {
				$age_of_oldest = $r->age;
				$oldest = $key;
				}
			}
		return $oldest;
	}

    function play() {
    	/* carry out default strategy while human player is not around: 
    	
    	if oldest house's age >= max_age - 1  then request quote for new system
    	else take a chance card */
    	
    	global $zurich;
    	
    	/* get any outstanding requests and deal with them */
    	
    	$query = new Query("SELECT id, request, requestor, param1, param2 FROM requests 
					WHERE requestee='$this->id' 
					ORDER BY time DESC");
    while ($query->next_rec()) {
        	$id = $query->field('id');
        	$request = $query->field('request');
        	$requestor = $query->field('requestor');
		$type = $query->field('param1');
		$price = $query->field('param2');
		switch($request) {
			case 'quotation':
				/* a manufacturer has sent a quote.  Check to see whether there is also a quote
				from the other manufacturer.  If so, choose the cheapest.  If the other manufacturer
				has declined to quote, choose the quotation that has been offered.  */
				$query1 = new Query("SELECT id, request, requestor, param1, param2 FROM requests 
									WHERE requestee='$this->id' AND requestor != '$requestor' AND param1 = '$type'");
				while ($query->next_rec()) {
					$id1 = $query1->field('id');
					$request1 = $query->field('request');
					$requestor1 = $query->field('requestor');
					$price1 = $query->field('param2');
					if ($request1 == 'no_quotation') {
						$cheapest = $requestor;
						$accepted_price = $price;
						}
					else {
						if ($price < $price1) {
							$cheapest = $requestor;
							$accepted_price = $price;
							}
						else {
							$cheapest = $requestor1;
							$accepted_price = $price1;
							}
						}
					if (!$this->pay($cheapest, $accepted_price)) {
						$this->cant_pay($accepted_price);
						return;
						}
					/* fit it to the oldest house */
					$house_no = $this->oldest();
					$this->houses[$house_no]->fit_sanitary_system($type);
					log_and_confirm("fitted house \"$house_no\" with a new $type sanitary system", 
										$this->id, 3);
					$this->request($cheapest, 'provide_system', $type, $accepted_price);
					$this->delete_request($id);
					$this->delete_request($id1);
					}
				break;
		 	case 'no_quotation':
 			    	/* a manufacturer has declined to quote.  Do nothing until the other manufacturer
 			    	has quoted */
 			    break;
 			}
        }

		$oldest_id = $this->oldest();
    	if ($oldest_id) {
    		$oldest_house = $this->houses[$oldest_id];
    		if ($oldest_house->age >= $oldest_house->max_age - 1) {
    	        $type = $oldest_house->type;
    	        $this->request_sanitary_system($type);
	    	    return;
    		}
	    }
    	$this->pick_chance_card();
    }
}

/*-------------------------------------------

  Manufacturer class definitions
  
 -------------------------------------------*/

Class Manufacturer extends Player {

	var $factories;        	// list of this manufacturer's factories
	var $sales;				// total sales made this turn (used to calculate tax)
	
	function manufacturer ($id, $name) {
		/* constructor: initial allocation of two normal factories and cash */
		
		$this->id = $id;
		$this->name = $name;
		$this->factories['a'] = new Factory();
		$this->factories['b'] = new Factory();
		$this->account = 1000;
		$this->sales = 0;
		$this->rating_weight = array(1, 1, 2, 1, 2);
	}
	
	function age_resources($change) {
		/* make the factories $change years older */
		
		foreach ($this->factories as $k=>$r) {
			$this->factories[$k]->make_older($change);
			if ($r->age == $r->max_age - 2) 
				$this->warn_player("Factory \"$k\" is near to the end of its life",
					'Event', 3);
			if ($r->age == $r->max_age) {
				unset($this->factories[$k]);
				$this->warn_player("Factory \"$k\" was closed because it was after the end of its life", 
					'Event', 3);
				}
			}
			/* set sales this year to zero */
			$this->sales = 0; 
	}
	
	function maintenance() {
		/* pay maintenance on factories to the bank and tax to the politician*/
		
		$fee = 0;
		foreach ($this->factories as $f) {
			$fee += $f->maintenance;
			}
		if ($fee) {
			$this->must_pay('bank', $fee);
			$this->warn_player("paid maintenance fee of " . CURRSYM . "$fee for factories", $this->id, 2);
			$tax = round($fee / 10);
			$this->must_pay('politician', $tax);
			$this->warn_player("paid tax of " . CURRSYM . "$tax on income to the politician", $this->id, 2);
			}
	}
	
	function income() {
		/* nothing to do */
	}
	
	function oldest() {
		/* return the index of the oldest factory */
		
		$age_of_oldest = -1;
		$oldest = 0;
		while (list ($key, $f) = each($this->factories)) {
			if ($f->age > $age_of_oldest) {
				$age_of_oldest = $f->age;
				$oldest = $key;
				}
			}
		return $oldest;
	}
	
	function check_can_supply($type) {
		/* retuns true if any of my factories can supply technology
		of $type */
		
		foreach ($this->factories as $f) {
			if ($f->type == $type) return TRUE;
			}
		return FALSE;
	}
			
	function repair_factory() {
		/* find the specified or oldest factory and repair it (set age to zero) */
		
		$key = get_param('repair_factory_number');
		if (!$key) $key = $this->oldest();
		if ($key) {
			$cost = $this->factories[$key]->repair_cost;
			if (!$this->pay('bank', $cost)) {
				$this->cant_pay($cost);
				return;
				}
			$this->factories[$key]->age = 0;
			log_and_confirm("repaired a factory at a cost of " . CURRSYM . "$cost", $this->id, 3);
			}
	}
	
	function change_production($key='') {
		/* change the production type of the oldest factory from normal
		to WST or vice versa */
		
		if (!$key) $key = get_param('ch_prod_factory_number');
		$cost = $this->factories[$key]->change_of_use_cost;
		if (!$this->pay('bank', $cost)) {
			$this->cant_pay($cost);
			return;
			}
		$new_type = $this->factories[$key]->change_type();
		log_and_confirm("changed factory $key to $new_type technology, costing " . 
			CURRSYM . "$cost", $this->id, 3);
	}
	
	function new_factory() {
		/* add a new normal factory to those I own */
		
		$new_factory = new Factory();
		$new_factory_type = get_param('newfactorytype');
		if ($new_factory_type != 'normal') $new_factory->change_type();
		$new_factory->age = 0;
		$cost = $new_factory->building_cost;
		if (!$this->pay('bank', $cost)) {
			$this->cant_pay($cost);
			return;
		}
		$new_key = 'a';
		while (isset($this->factories[$new_key])) {
			$new_key = chr(ord($new_key) + 1);
		}	
		$this->factories[$new_key] = $new_factory;
		log_and_confirm("built a new " . $new_factory->type . 
			" factory called \"$new_key\"", $this->id, 3);		
	}
	
	function request_quote($id, $requestor, $type, $ignore) {
 		/* note that a request has come in.  Display a message 
 		and ask for a price */
 		
 		$requestor_name = $this->id_to_name($requestor);
 		echo "<form method=post action=main.php>
 			<input type=hidden name=id value=$id>
 			<input type=hidden name=requestor value=$requestor>
 			<input type=hidden name=type value=\"$type\">
 			<input type=hidden name=action value=send_quote>
 			$requestor_name has asked for a quote for
 			a $type sanitary system.<BR>
 			Do you want to quote? 
 			<input name=quotep type=radio value=no>No
 			<input name=quotep type=radio value=yes>Yes
 			- if you do, how much do you want to charge to supply such a system?
 			" . CURRSYM . "<input type=text name=quote maxlength=7 size=7>\n" . 
 			reply_button() . "</form>\n";
 	}
 	
 	function send_quote() {
 		/* deliver a quote to the requestor */
 		
 		$id = get_param('id');
 		$type = get_param('type');
 		$requestor = get_param('requestor');
 		$purchaser_name = $this->id_to_name($requestor);
 		$quote = get_param('quote');
 		if ($quote) {
     		if (!$this->check_can_supply($type)) {
     			echo "You do not have production facilities in your factories
     			to supply $type technology!  Action cancelled.";
     			return;
     			}
     		if ($quote > 750) {
     				/* complain about profiteering! */
     			echo "You may not charge more than 	" . CURRSYM . "750 for a system.
     				Action cancelled.";
     			return;
     		}
 			log_and_confirm("sent a quote of " . CURRSYM . "$quote to $purchaser_name for a $type system", 
 				$this->id, 3);
 			$this->request($requestor, 'quotation', $type, $quote);
 			}
 		else { /* quotation refused */
 			log_and_confirm("declined to provide a quote to $purchaser_name for a $type system", 
 				$this->id, 3);
 			$this->request($requestor, 'no_quotation', $type);
 			} 		
 	 	$this->delete_request($id);
    }	
 	
 	function provide_system($id, $purchaser, $type, $price) {
 		/* receive payment for a system that has been sold */
 		
 		$purchaser_name = $this->id_to_name($purchaser);
 		log_and_confirm("sold a $type system to $purchaser_name for " . 
 			CURRSYM . "$price", $this->id, 3);
 		$this->sales += $price;
 		$this->delete_request($id);
 	}

 	function reject_system($id, $purchaser, $type, $price) {
 		/* note that purchaser has not accepted quote */
 		
 		$purchaser_name = $this->id_to_name($purchaser);
 		log_and_confirm("$purchaser_name rejected a quote of " . CURRSYM . 
 					"$price for a $type system", $this->id, 3);
 		$this->delete_request($id);
 	}
 	
 	function haggle($id, $purchaser, $type, $offer) {
 		/* Housing association has rejected the sale, but made a counter offer.
 		See whether to accept this */
 		
  		$purchaser_name = $this->id_to_name($purchaser);
 		echo "<form method=post action=main.php>
 			<input type=hidden name=id value=$id>
 			<input type=hidden name=requestor value=$purchaser>
 			<input type=hidden name=type value=\"$type\">
 			<input type=hidden name=action value=send_haggle>
 			<input type=hidden name=offer value=$offer>
 			$purchaser_name has declined to accept your quote for
 			a $type sanitary system, but has offered to pay " . CURRSYM . "$offer for it.<BR>
 			Do you want to<BR><input name=haggle_reply type=radio value=accept_offer>accept this offer or<BR>
 			<input name=haggle_reply type=radio value=reject_offer>reject this offer and propose
 			a counter bid?<BR>
 			<span class=subaction>How much do you now want to charge to supply the system?
 			" . CURRSYM . "<input type=text name=quote maxlength=7 size=7 value=$offer>
 			</span><BR>\n" . 
 			reply_button() . "</form>\n";
 	}
 	
 	function send_haggle() {
 			/* send the Housing Association a reponse to its attempt to haggle over the price */
 		$id = get_param('id');
 		$type = get_param('type');
 		$requestor = get_param('requestor');
 		$reply = get_param('haggle_reply');
 		$offer = get_param('offer');
 		$purchaser_name = $this->id_to_name($requestor);
 		$quote = get_param('quote');
 		if ($reply == 'accept_offer') $quote = $offer;
 		if ($quote) {
     		if (!$this->check_can_supply($type)) {
     			echo "You do not have production facilities in your factories
     			to supply $type technology!  Action cancelled.";
     			return;
     			}
     		if ($quote > 750) {
     				/* complain about profiteering! */
     			echo "You may not charge more than 	" . CURRSYM . "750 for a system.
     				Action cancelled.";
     			return;
     		}
 			log_and_confirm("provided a new quote of " . CURRSYM . "$quote to $purchaser_name for a $type system", 
 				$this->id, 3);
 			$this->request($requestor, 'quotation', $type, $quote);
 		}
 		else { /* quotation refused */
 			log_and_confirm("declined to provide a lower quote to $purchaser_name for a $type system", 
 				$this->id, 3);
 			$this->request($requestor, 'no_quotation', $type);
 			} 		
 	 	$this->delete_request($id);
    }	
		
 	
    function play() {
    	/* carry out default strategy while human player is not around: 
    	
    	if oldest factory is max_age or older then repair it
    	else if if there is a factory still making normal systems
    	    and there is enough in the account then change it to water saving
    	else take a chance card */
    	
    	global $zurich;
    	
     	/* get any outstanding requests and deal with them */
    	
    	$query = new Query("SELECT id, request, requestor, param1, param2 FROM requests 
					WHERE requestee='$this->id' 
					ORDER BY time DESC");
        while ($query->next_rec()) {
        	$id = $query->field('id');
        	$request = $query->field('request');
        	$requestor = $query->field('requestor');
 			$purchaser_name = $this->id_to_name($requestor);
 			$type = $query->field('param1');
 			$price = $query->field('param2');
 			switch($request) {
 			    case 'request_quote':
 			        if ($this->check_can_supply($type)) {
 			            $quote = mt_rand(100, 750);
 			            log_and_confirm("provided $purchaser_name with a quote of " . CURRSYM . 
 			            	"$quote for a $type system", $this->id, 3);
 			            $this->request($requestor, 'quotation', $type, $quote);
 			        }
 			        else {
 			            log_and_confirm("declined to quote for a $type system", 
 			                $this->id, 3);
 			            $this->request($requestor, no_quotation, $type);
 			        }
 			        break;
 			    case 'provide_system':
 			        log_and_confirm("sold a $type system to $purchaser_name for " . CURRSYM . "$price",
 			            $this->id, 3); 
 			        $this->sales += $price;
 			        break;
 			    case 'reject_system':
 			        $purchaser_name = $this->id_to_name($requestor);
 			        log_and_confirm("$purchaser_name rejected a quote of " . CURRSYM . 
 			        	"$price for a $type system", $this->id, 3); 
 			        break;
 			}
 			$this->delete_request($id);
        }
 		
 		/* repair any factories that are near to expiry */	    
   	    $oldest_id = $this->oldest();
    	if ($oldest_id and $this->factories[$oldest_id]->age >= 
    	        $this->factories[$oldest_id]->max_age - 2) {
	    	$this->repair_factory(); 
	    	return;
	    } 
	    
	    /* try to ensure that there is both a normal and a WST factory */
	    
	    if (! $this->check_can_supply('normal')) $want = 'normal';
	    if (! $this->check_can_supply('water saving')) $want = 'water saving';
	    
	    if ($want and count($this->factories) > 1) {
    		/* find the first factory with the other kind of technology */
    		$not_want = ($want == 'normal' ? 'water saving' : 'normal');
    		foreach ($this->factories as $key =>$f) {
    			if ($f->type == $not_want) {
    				$f_key = $key;
    				break;
    			}
    		} 
    		if ($f_key) {
	    		/* only change if we can afford it */
			    if ($this->account >= $f->change_of_use_cost) { 
			    	$this->change_production($f_key);
			    	return;
			    }
    		}
    	}
    	$this->pick_chance_card();
    }
}
			
/*-------------------------------------------

  Politician class definitions
  
 -------------------------------------------*/
 
 Class Politician extends Player {
 	
 	var $tax_rate;      // annual tax rate on other players' balances
 	var $in_power;		// whether in power or in opposition
 
 	function politician($id, $name) {
 		/* constructor */
 		
		$this->id = $id;
		$this->name = $name;
 		$this->account = 1000;
 		$this->tax_rate = 0.1;
 		$this->in_power = TRUE;
 		$this->rating_weight = array(3, 2, 3, 2, 1);
	}
 	
	function age_resources($change) {
		/* if it is election year, see whether we are to get re-elected */
		
		global $zurich, $govt_duration;
		
		$years_to_election = $govt_duration - $zurich->turn % $govt_duration;
		if ($years_to_election == 0) {
			if ($zurich->political_popularity > mt_rand(0, 10)) {
				log_act("was re-elected for another $govt_duration year term", $this->id, 3);
				$this->warn_player("Congratulations.  You have been re-elected for another 
				$govt_duration year term at the recent election!", 'Event', 3);
				$this->in_power = TRUE;
			}
			else {
				log_act("lost the election.", $this->id, 3);
				$this->warn_player("Unfortunately, you lost the recent election.  You will have to remain in
				opposition for $govt_duration years and during this time you will have limited
				scope for action.  You may not subsidise players, approve water price changes,
				nor hold referenda.", 'Event', 3);
				$this->in_power = FALSE;
			}
		}
		else {
			if ($years_to_election == 1) echo "There is one year";
			else echo "There are $years_to_election years";
			echo " until the next election.";
		}
	}
	
	function in_opposition() {
		/* block attempt to carry out actions while in opposition */
		
		echo "You cannot carry out this action until you have been re-elected.";
	}
		
	function maintenance () {
		/* nothing to do */
	}
	
 	function income() {
 		/* income comes from tax on turnover of HAs and Manufacturers.  Nothing to do here */
 		
  	}
 	
 	function subsidise() {
 		/* pay the recipient the subsidy */
 		
 		if (!$this->in_power) {
 				$this->in_opposition();
 				return;
 		}
 		$recipient = get_param('recipient');
 		$subsidy = get_param('subsidy');
 		$recipient_name = $this->id_to_name($recipient);
		if (!$this->pay($recipient, $subsidy)) {
			$this->cant_pay($subsidy);
			return;
			}
 		log_and_confirm("paid " . CURRSYM . "$subsidy as subsidy to $recipient_name", $this->id, 3);
 	}
 	
 	function changetax() {
 		
 		global $zurich;
 		
 		/* change the tax rate */
 	 		if (!$this->in_power) {
 				$this->in_opposition();
 				return;
 		}
 		$newrate = get_param('newtax');
 		$oldrate = round($this->tax_rate * 100);
 		if ($newrate > 25) {
 			echo "Your proposed tax rate $newrate% has been rejected by parliament because it is too high.";
 			return;
 		}
 		$this->tax_rate = $newrate / 100;
 		if ($oldrate < $newrate and $zurich->political_popularity > 0) {
			$zurich->political_popularity--;
			$effect = "down";
 		}
 		if ($oldrate > $newrate and $zurich->political_popularity < 10) {
 			$zurich->political_popularity++;
 			$effect = "up";
 		}
  		log_and_confirm("changed the tax rate from $oldrate% to $newrate%", $this->id, 3);
  		echo "and your popularity went $effect.";
 	}
 		
 	function referendum() {
 		/* The referendum consists of rolling a ten-sided dice.  
 		The answer is YES if the answer from the dice is less than or 
 		equal to the politician's current popularity; otherwise NO. */
 		
 		global $zurich;
 		
 		 if (!$this->in_power) {
 		 	$this->in_opposition();
 		 	return;
 		 }
 		if (mt_rand(1,10) <= $zurich->political_popularity) {
 			log_and_confirm("held a referendum and the electorate voted YES", $this->id, 3);
 			return TRUE;
 			}
 		else {
 			log_and_confirm("held a referendum and the electorate voted NO", $this->id, 3);
 			return FALSE;
 			} 		
 	}
 	
 	function request_price_change($id, $requestor, $amount, $ignore) {
 		/* note that a request has come in.  If the request was for a price decrease, ask whether it is to be
 		approved or not.
 		If it was for an increase, ask player also whether to call a referendum. */
 		
 		global $zurich;
 		
 		if (!$this->in_power) return;
 		
 		$requestor_name = $this->id_to_name($requestor);
 		log_and_confirm("received a request for a water price change to " . CURRSYM . "$amount from $requestor_name",
 				$this->id, 1); 
 		echo "<form method=post action=main.php>
 			<input type=hidden name=id value=$id>
 			<input type=hidden name=amount value=$amount>
 			<input type=hidden name=requestor value=$requestor>
 			$requestor_name has asked you to approve
 			a change in the price of water to " . CURRSYM . "$amount .  
 			Do you <BR><input name=action type=radio value=approve_change>Approve
 			or <BR><input name=action type=radio value=disapprove_change>Disapprove";
 			if ($amount > $zurich->water_price) {
	 			echo " or <BR><input name=action type=radio value=consult_the_voters>Do you want to 
	 			consult the voters?";
 			}
 			echo reply_button() . "</form>";
 	}
 	
 	function approve_change($held_ref = 0) {
 		/* change the water price and tell the requestor.  Change my popularity, unless I have
 		held a referendum ($held_ref is true) */
 		
 		global $zurich;
 		
 		$id = get_param('id'); 
 		$amount = get_param('amount'); 
 		$requestor = get_param('requestor'); 
 		log_and_confirm("approved water price change to " . CURRSYM . "$amount", $this->id, 3);
 		$this->request($requestor, 'price_change_approved', $amount);
 		
 		/* new price is > old, decrease popularity unless there was a referendum
 		   if new price < old, increase popularity */
 		
 		$delta = $amount - $zurich->water_price;  // delta is +ve for a price rise
 		if ($delta > 0) {
 			if (!$held_ref) {
		 		/* decrease the politician's popularity, because he took it upon his own head */
		 		$new_pop = $zurich->politician_popularity - $delta;  /* one point per unit price rise */
		 		if ($new_pop < 0) $new_pop = 0;  /* ensure not -ve */
		 		$delta = $zurich->political_popularity - $new_pop;
		 		if ($delta > 0) {
		 			log_and_confirm("The politician's popularity decreased by $delta because the water price rise was approved",
		 			    'Event', 3);
		 		}
		 		$zurich->political_popularity = $new_pop;
 			}
 		}
 		if ($delta < 0) {
		 		/* increase the politician's popularity */
	 		$new_pop = $zurich->politician_popularity - $delta;
	 		if ($new_pop > 10) $new_pop = 10;  /* ensure not too big */
	 		$delta = $new_pop - $zurich->political_popularity;
	 		if ($delta > 0) {
	 			log_and_confirm("Tthe politician's popularity increased by $delta because the water price fall was approved",
	 			    'Event', 3);
	 		}
	 		$zurich->political_popularity = $new_pop;
 		}
 		$this->delete_request($id);
 	}
 	
 	function disapprove_change() {
 		/* don't change the water price and tell the requestor */
 		
 		$id = get_param('id');
 		$amount = get_param('amount'); 
 		$requestor = get_param('requestor'); 
 		log_and_confirm("did not approve a request from the water utility to change the price of water to " . 
 			CURRSYM . "$amount", $this->id, 3);
 		$this->request($requestor, 'price_change_disapproved', $amount);
 		$this->delete_request($id);
 	}
 	
 	function consult_the_voters() {
 		/* Hold a referendum and lose popularity if you lose the vote */
 		
 		if (!$this->in_power) {
 			$this->in_opposition();
 			return;
 		}

 		if (!$this->pay('bank', 1000)) {  /* referenda cost 1000 */
 			    $this->cant_pay(1000);
 			    return;
 		}
        if ($this->referendum()) $this->approve_change(TRUE);
        else $this->disapprove_change();;
 	}			

    function play() {
    	/* carry out default strategy while human player is not around: 
    	
    	if sufficient money in account and popularity < 5 then
    	    if water demand > 0.8 * water water supply then
    	        advertise environment
    	    else advertise politician
    	else take a chance card */
    	
    	global $zurich;
 
  		if (!$this->in_power) return;
  	
    	/* get any outstanding requests and deal with them */
    	
    	$query = new Query("SELECT id, request, requestor, param1, param2 FROM requests 
					WHERE requestee='politician' 
					ORDER BY time DESC");
        while ($query->next_rec()) {
        	$id = $query->field('id');
        	$request = $query->field('request');
        	$requestor = $query->field('requestor');
 			$amount = $query->field('param1');
 			if ($request == 'request_price_change') {
 			    /* approve or disapprove requested price change */
 			    if ($zurich->political_popularity >= 5) {
 			        /* approve the change */
 			        log_and_confirm("approved the water price change to " . CURRSYM . "$amount.", 
 			            'Robot politician', 3);
 			        $this->request($requestor, 'price_change_approved', $amount);
 			        /* decrease popularity */
             		$new_pop = $zurich->politician_popularity - $amount;  /* one point per unit price rise */
             		if ($new_pop < 0) $new_pop = 0;  /* ensure not -ve */
             		if ($new_pop > 10) $new_pop = 10;
             		$delta = abs($zurich->political_popularity - $new_pop);
             		if ($delta != 0) {
             			log_act("Politicians popularity changed by $delta because the water price rise was approved",
             			    'Event', 3);
             		}
             		$zurich->political_popularity = $new_pop;
 			    }
 			    else { /* disapprove change */
 			     	log_act("did not approve price change to $amount", 'Robot politician', 2);
 		            $this->request($requestor, 'price_change_disapproved', $amount);
 			    }
 		        $this->delete_request($id);
 			}
        }

    	
    	if ($this->account >= 500 and $zurich->political_popularity < 5) {
		    if ($zurich->water_demand > 0.8 * $zurich->water_supply) {
		    	$this->advertise('env_awareness'); 
		        return;
		    }
		    $this->advertise('political_popularity');
		    return;
    	}   
    	$this->pick_chance_card();
    }
 }
 
 /*-------------------------------------------

  Bank class definitions
  
 -------------------------------------------*/
 
 Class Bank extends Player {
 
 	var $debtors;			// list of players who owe money to the bank
 	var $interest_rate;     // current value of interest rate
 	
 	function bank($id, $name) {
 		/*constructor */
 		
 		$this->id = $id;
 		$this->name = $name;
 		$this->debtors = array();
 		$this->interest_rate = 0.1;
 		$this->rating_weight = array(1, 1, 1, 1, 1);
	}
 	
	function age_resources($change) {
		/* nothing to do */	
	}
	
	function maintenance () {
		/* nothing to do */
	}
	
	function income () {
		/* collect interest from all debtors */
		
		foreach ($this->debtors as $borrower => $amount) {
			$borrower_obj =& $this->id_to_obj($borrower);
			if ($amount > 10) $borrower_obj->pay_interest(round($this->interest_rate * $amount));
			}
	}
	
 	function lend($borrower, $amount) {
 		/* record the amount borrowed */
 		
 		$borrower_name = $this->id_to_name($borrower);
 		log_and_confirm("lent " . CURRSYM . "$amount to $borrower_name", $this->id, 1);
 		if (!isset($this->debtors[$borrower])) $this->debtors[$borrower] = 0;
 		$this->debtors[$borrower] += $amount;
 	}
 	
 	function repay($borrower, $amount) {
 		/* record that the borrower has repaid (some of) the debt */
 		
 		$borrower_name = $this->id_to_name($borrower);
 		log_and_confirm("$borrower_name has repaid $amount owed to the bank", $this->id, 1);
 		$this->debtors[$borrower] -= $amount;
 	}
 	
 	function force_end_of_year() {
 		/* do the end of year operations, even though it isn't */
 		
 		global $zurich;
 		
 		$zurich->end_of_turn(1);
 	}
 	
 	function reset() {
 	    /* reset everything: has the effect of deleting the environment
 	    and all objects when the next web page is accessed! */
 	    
 	    global $zurich;
 	    
 	    /* delete all stuff from previous game */
 	    db_write("DELETE FROM log");
 	    db_write("DELETE FROM requests");
 	    db_write("DELETE FROM msgs");
 	    log_and_confirm("Game was reset!", $this->id, 3);
 	   } 
 	    	
 	function logout() {
 		/* log out the selected player */
 		
 		global $zurich;
 		
 		$who = get_param('who');
 		$zurich->players[$who]->loggedin = FALSE;
 		$name = $zurich->players[$who]->name;
 		log_and_confirm("left the game", $name, 2);
 		echo "$name was logged out.";
 	}
 	
 	function logoutall() {
 		/* logout all players (except the bank) */
 
  		global $zurich;
		
 		foreach ($zurich->players as $p) {
 			if ($p->id == 'bank') continue;
 			$p_obj = & $this->id_to_obj($p->id);
 			if ($p_obj->loggedin) {
 				$p_obj->loggedin = FALSE;
 				log_and_confirm("left the game", $p->name, 2);
 				echo "$p->name was logged out.<br>";
 			}
 		}
 	}
 	
 	function auto() {
 		/* run all the softbots for the specified number of turns */
 		
 		global $zurich, $softbot;
 		
 		$turns = get_param('auto_turns');
 		for ($t = 1;  $t <= $turns; $t++) {
 			echo "Pass $t<blockquote>\n";
 			foreach (array_keys($zurich->players) as $k) {
 				$softbot = &$zurich->players[$k];
 				echo "$softbot->name<br>\n";
 				$softbot->play();
 			}
 			echo "</blockquote>";
 			unset($softbot);
 		}
 		echo "Done.";
 	}
			
 	function turntime() {
 		/* change the minimum time between turns that players have to wait */
 		
 		global $zurich;
 				
 		$newinterval = get_param('mintimemins');
 		$zurich->action_gap = 60 * $newinterval; // $action_gap is in secs.	
 		echo "Minimum time interval between plays is now $newinterval<P>";
 	}	
		
  	function actionsperyear() {
 		/* change the number of actions that make up a game year */
 		
 		global $zurich;
 				 		
 		$zurich->actsperyear = get_param('actionsperyear');
 		echo "Number of player actions per game year is now $zurich->actsperyear<P>";
 	}	
	   
    function play() {
    	/* nothing to do */
    }	
 }
 



 	
 		
 		
 	
 
