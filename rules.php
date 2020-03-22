<?php

/***********************************************
   Zurich Water Game: internet version
   Author:  Nigel Gilbert and the FIRMA project
   --------------------------------------------
   rules.php
   
   Applies all the system rules and generates events
   
   Version 1.0  6 August 2001
   Version 1.1  1 September 2001
   Version 2.0  10 November 2001

**********************************************/


function apply_system_rules() {

	global $zurich;

/* These rules are applied only at the end of each turn, one after the other */

/* Rule 1: if the water supply > water demand and supply < 1.5 demand, 
			increase water quality by (1..3)  */
			
	if ($zurich->water_supply > $zurich->water_demand
		and $zurich->water_supply < 1.5 * $zurich->water_demand) {
		$change = dice_up($zurich->water_quality, 1, 3);
		$zurich->water_quality += $change;
		if ($change) log_and_confirm(
		"Because water supply exceeds demand, the water quality has increased by $change.", 
			'Rule', 3);	
		}
			
/* Rule 2: if the water supply is > 1.5 demand, reduce water quality */

	if ($zurich->water_supply >= 1.5 * $zurich->water_demand) {
		$change = dice_down($zurich->water_quality, 1, 3);
		$zurich->water_quality -= $change;
		if ($change) log_and_confirm(
		"Because water supply greatly exceeds demand, the water quality has decreased by $change.", 
			'Rule', 3);	
		}

/* Rule 3:  (a) if the waste water utility used to have debts, now paid off, increase 
					the politician's popularity by (1..3)
			(b) if the water utility used to have debts, now paid off, increase 
					the politician's popularity by (1..3) */
			
	if ($zurich->players['waste_water_utility']->paid_off_debts) {
		$change = dice_up($zurich->political_popularity, 1, 3);
		$zurich->political_popularity += $change;
		if ($change) log_and_confirm(
		"Because the waste water utility no longer has debts, the politician's popularity has increased by $change.", 
			'Rule', 3);
		$zurich->players['waste_water_utility']->paid_off_debts = 0;
		}
	if ($zurich->players['water_utility']->paid_off_debts) {
		$change = dice_up($zurich->political_popularity, 1, 3);
		$zurich->political_popularity += $change;
		if ($change) log_and_confirm(
		"Because the water utility no longer has debts, the politician's popularity has increased by $change.", 
			'Rule', 3);
		$zurich->players['water_utility']->paid_off_debts = 0;
		}
		
			
/* Rule 4:  if the water quality was above 7 at any time during the last turn,
 				increase the politician's popularity by (1..3)*/
 				
 	if ($zurich->max_water_quality > 7) {
		$change = dice_up($zurich->political_popularity, 1, 3);
		$zurich->political_popularity += $change;
		if ($change) log_and_confirm(
		"Because the water quality has risen above 7, the politician's popularity has increased by $change.", 
			'Rule', 3);
		$zurich->max_water_quality = 0;
 	}
 		
/* Rule 5:  if the water quality is now below 6, decrease political popularity */

 	if ($zurich->water_quality < 6) {
		$change = dice_down($zurich->political_popularity, 1, 3);
		$zurich->political_popularity -= $change;
		if ($change) log_and_confirm(
		"Because the water quality is below 6, the politician's popularity has decreased by $change.", 
			'Rule', 3);
 	}

/* Rule 6:  if the lake water quality is below 6, decrease political popularity */

 	if ($zurich->lake_quality) {
		$change = dice_down($zurich->political_popularity, 1, 3);
		$zurich->political_popularity -= $change;
		if ($change) log_and_confirm(
		"Because the lake's water quality is below 6, the politician's popularity has decreased by $change.", 
			'Rule', 3);
 	}

/* Rule 7:  if the water supply is less than demand, decrease political popularity */

 	if ($zurich->water_supply < $zurich->water_demand) {
		$change = dice_down($zurich->political_popularity, 1, 3);
		$zurich->political_popularity -= $change;
		if ($change) log_and_confirm(
		"Because the water supply is less than the water demand, the politician's popularity has decreased by $change.", 
			'Rule', 3);
 	}

/* Rule 8:  if the lake water quality was ever below 5 in this turn, increase
		environmental awareness */
		
	 if ($zurich->min_lake_quality < 5) {
		$change = dice_up($zurich->env_awareness, 1, 3);
		$zurich->env_awareness += $change;
		if ($change) log_and_confirm(
		"Because the lake water quality fell to below 5, the level of environmental awareness has increased by $change.", 
			'Rule', 3);
		$zurich->min_water_quality = 9999;
 	}

/* Rule 9:  if the politican, the water utility or the waste water utility owes money, 
			decrease the poitician's popularity */
			
	if ($zurich->players['water_utility']->debtor()) {
		$change = dice_down($zurich->political_popularity, 1, 3);
		$zurich->political_popularity -= $change;
		if ($change) log_and_confirm(
		"Because the water utility owes money, the politician's popularity has decreased by $change.", 
			'Rule', 3);
 	}
	if ($zurich->players['waste_water_utility']->debtor()) {
		$change = dice_down($zurich->political_popularity, 1, 3);
		$zurich->political_popularity -= $change;
		if ($change) log_and_confirm(
		"Because the waste water utility owes money, the politician's popularity has decreased by $change.", 
			'Rule', 3);
 	}
	if ($zurich->players['politician']->debtor()) {
		$change = dice_down($zurich->political_popularity, 1, 3);
		$zurich->political_popularity -= $change;
		if ($change) log_and_confirm(
		"Because the politician owes money, the politician's popularity has decreased by $change.", 
			'Rule', 3);
 	}
	
/* Rule 10:  if the lake water quality was ever above 7 in this turn, increase
		political popularity */
		
	 if ($zurich->max_lake_quality > 7) {
		$change = dice_up($zurich->env_awareness, 1, 3);
		$zurich->political_popularity += $change;
		if ($change) log_and_confirm(
		"Because the lake water quality rose above 7, the politician's popularity has increased by $change.", 
			'Rule', 3);
		$zurich->max_water_quality = 0;
 	}


/* Rule 11: implemented by politician */

/* Rule 12: if demand is below 50, the waste water utility flushes the pipes */

	if ($zurich->water_demand < 50) {
		log_and_confirm(
			"Because water demand is low, the waste pipes are beoming clogged and must be flushed.", 
				'Rule', 3);
		$zurich->players['waste_water_utility']->flush_pipes();
	}
	
/* Rule 13: every six years, the politician must be voted back in */

		/* DON'T KNOW HOW TO IMPLEMENT THIS ! */
		
/* Rule 14: if politician calls a referendum, there is no subsequent loss in 
popularity when there is a price rise.  Implemented by politician */

/* Rule 15: For every point of price reduction, the politician's popularity rises by one point. 
		Implemented by politican */	

/* Rule 16:  Max. quoted price for water systems is 750 
		Implemented by manufacturer  */
		
}

function event() {

	global $zurich, $player, $softbot;

	/* chooses an event 'card' at random and applies it.  Unimplemented options generate
	a 'no-op' event (leaves the game state unchanged) */
	
	$card = mt_rand(1, 14);	// second value must equal the number of different event cards below
	log_act("Picked chance card $card.", ($softbot ? $softbot->id : $player), 0);

	switch ($card) {
	
	case 1: /* Card 1: repairs necessary to a reservoir */
            /* choose a reservoir at random */
            $wu = $zurich->players['water_utility'];
            if (count($wu->reservoirs) == 0) return; // no reservoirs exist!
            $re_id = array_rand($wu->reservoirs);
			$cost = $wu->reservoirs[$re_id]->repair_cost;
			$wu->must_pay('bank',$cost);
			$wu->reservoirs[$re_id]->age = 0;
            $re_letter = chr($re_id + ord("a") + 1);
            log_and_confirm(
            "Because of an incident, it has been necessary to repair reservoir $re_letter at a cost of " 
            	. CURRSYM . "$cost.", "Event", 3);
            return;
	case 2: /* Card 2: repair a house */
            /* choose a housing association at random */
            $ha = rand(1,2);
            $ha_id = ($ha == 1 ? 'housing_assoc_1' : 'housing_assoc_2');
            /* choose a house at random */
            $house_id = array_rand($zurich->players[$ha_id]->houses);
            /*set its age past max age to signal that it needs repair */
            $zurich->players[$ha_id]->houses[$house_id]->age = 
                $zurich->players[$ha_id]->houses[$house_id]->max_age + 1;
            log_and_confirm(
            	"One of " . $zurich->players[$ha_id]->name . "'s houses has become in urgent need of repair.", 
            	'Event', 3);
            return;
	case 3: /* Card 3: environmental crisis */
			$change = dice_up($zurich->env_awareness, 1, 3);
			if ($change) {
				$zurich->env_awareness +=  $change;
				log_and_confirm(
					"An environmental crisis has caused an increase in environmental awareness of $change.",
					 "Event", 3);
			}
			return;
	case 4: /* Card 4: materialism! */
			$change = dice_down($zurich->env_awareness, 1, 3);
			if ($change) {
				$zurich->env_awareness -=  $change;
				if ($change) log_and_confirm(
				"A cultural shift to materialism has caused a decrease in environmental awareness of $change.",
					 "Event", 3);
			}
			return;
	case 5: /* Card 5: politican's popularity increases */
			$change = dice_up($zurich->political_popularity, 1, 3);
			if ($change) {
				$zurich->political_popularity += $change;
				if ($change) log_and_confirm("The politician's popularity has increased by $change.", 
					'Event', 3);
			}
			return;
	case 6: /* Card 6: politican's popularity decreases */
			$change = dice_down($zurich->political_popularity, 1, 3);
			if ($change) {
				$zurich->political_popularity -= $change;
				if ($change) log_and_confirm("The politician's popularity has decreased by $change.", 
					'Event', 3);
			}
			return;
	case 7: /* Card 7: not implemented */		
			break;
	case 8: /* Card 8: not implemented */	
			break;	
	case 9: /* Card 9:  Accident! water quality decreases */
			$change = dice_down($zurich->water_quality, 1, 3);
			if ($change) {
				$zurich->water_quality -= $change;
				if ($change) log_and_confirm(
					"There has been an accident and the water quality has decreased by $change.", 
					'Event', 3);
			}
			return;
	case 10: /* Card 10: Tax rate changes (to between 5 and 15 percent */
#  Now done by politician
	        return;
	case 11: /* Card 11: The bank calls in all debts */
	
				/* not clear what the implications of this are */
			break;
	case 12: /* Card 12: The bank gives you a cheque */
			if ($zurich->players[$player]->id == 'bank') return;  /* Bank doesn't receive anything */
			$zurich->players[$player]->benefit('bank', 1000);
			$recipient = $zurich->players[$player]->name;
			log_and_confirm("The bank has awarded " . CURRSYM . 
				"1000 to $recipient as a reward for inspirational work.",
				'Event', 3);
			return;
	case 13: /* card 13: Period of dryness, increased demand in range 1 to 30 */
            $old_value = $zurich->water_demand;
			if ($old_value < 100) {
				$new_value = $old_value + mt_rand(1,30);
				if ($new_value > 100) $new_value = 100;
				$change = $new_value - $old_value;
			}
			if ($change) {
				$zurich->water_demand = $new_value;
				log_and_confirm("There has been a drought and the demand for water has increased by $change.",
					'Event', 3);
			}
			return;
	case 14: /* Card 14: Interest rate changes (to within the range 5% .. 25%) */
            $rate = round(mt_rand(25, 125)/5);
	        $zurich->players['bank']->interest_rate = $rate/100;
	        log_and_confirm("The interest rate on bank loans has changed to $rate%.", 
	        			'Event', 3);
	        return;
	default: 
			break;
	}
	
	/* here only if one of the implemented Event cards has not been randomly chosen */
	
// this is too boring; just ignore ineffective event cards
//	log_and_confirm("There has been a record amount of rain this month.", 'Event', 3);
}
