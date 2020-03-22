<?php

/***********************************************
   Zurich Water Game: internet version
   Author:  Nigel Gilbert and the FIRMA project
   --------------------------------------------
   faqs.php

   Displays the FAQs for the help tab

   Version 1.0  6 August 2001
   Version 1.1  1 September 2001
   Version 2.0  10 November 2001

**********************************************/


/*  text for Frequently Asked Questions that are displayed under the
	Help tab.  Each entry consists of a unique id, the question and the answer */
	

$faqs = array(

array(
	'id'		=> 'faq1',
	'question'	=> "Acknowledgements",
	'answer'	=> "This page gives information about the game developers 
	and research supporters."
	),

array(
	'id'		=> 'faq2',
	'question'	=> "Actions",
	'answer'	=> "The list of actions that players can take will vary but it 
	is always located on the left-hand side of the player pages with question mark 
	buttons beside each action that can be clicked for further clarification displayed 
	under the \"bank statement\" tab.  In order for the text to remain on screen, the 
	cursor must remain over the question mark button.  'Advertise' for political 
	popularity or environmental awareness and \"borrow from the bank\" are the two 
	actions that are offered to all players.  In order to select an action, click in 
	one of the clear buttons on the left-hand side of the preferred action, write a 
	reason for this action in the \"reason for action\" text box and then press the 'go' 
	button to complete."
	),

array(
	'id'		=> 'faq3',
	'question'	=> "Automated players",
	'answer'	=> "Some players may be automated agents that can take actions.  
	They cannot respond to or make public and private messages nor respond to price requests."
	),

array(
	'id'		=> 'faq4',
	'question'	=> "Bank statement",
	'answer'	=> "This tab on all player pages gives the current bank balance for 
	specific players, displays the definition for the scale bars at the bottom of the 
	page, receives messages from the bank about automatic overdrafts and current rates 
	of interest on accruing debt, displays the specific definitions of each player 
	action once one of the question mark buttons are clicked, gives quotes for a 
	sanitary system, and provides a place to hold a referendum or make requests for 
	subsidies."
	),

array(
	'id'		=> 'faq5',
	'question'	=> "Borrowing",
	'answer'	=> "Borrowing money from the bank is an action offered to all players.  
	Payments on loans must be made each year and the interest rates can change so be 
	aware of any charges and changes displayed under the 'bank statement' tab.  Overdrafts 
	will be automatically taken by the bank if there is not enough money in a player's 
	bank account at the time of payment.  Increasing debts may prevent players from taking 
	any actions so be careful!  Players can take an action and borrow money in the same turn."
	),

array(
	'id'		=> 'faq6',
	'question'	=> "Briefing",
	'answer'	=> "This describes the history and current context of urban water 
	supply management in the metropolitan area of Z¸rich."
	),

array(
	'id'		=> 'faq7',
	'question'	=> "Cleaning pipes",
	'answer'	=> "The waste water utility is responsible for cleaning the pipes 
	of waste.  It costs Ä500 each cleaning and it is recommended that the utility does 
	this when water demand is low (below 50)."
	),

array(
	'id'		=> 'faq8',
	'question'	=> "Contacts",
	'answer'	=> "In order to make general enquiries or report problems about the 
	game identify the appropriate people by clicking onto the 'contact us' section at 
	the top of the main introductory pages."
	),

array(
	'id'		=> 'faq9',
	'question'	=> "Diary of events",
	'answer'	=> "This tab on all player pages will identify the days and times 
	of specific actions taken by players.  It also includes rules that govern some 
	actions and automated events such as accidents, elections, and factory and reservoir 
	closures.  The most recent action/event/activated rule is listed at the top."
	),

array(
	'id'		=> 'faq10',
	'question'	=> "Elections",
	'answer'	=> "Elections occur every four years.  The politician must maintain 
	a high level of popularity in order to get re-elected each term.  Communicating with 
	other players, advertising political popularity and giving subsidies can sometimes 
	help maintain a high popularity.  If not re-elected, a politician cannot subsidize 
	other players, change the tax rate or hold referendums."
	),

array(
	'id'		=> 'faq11',
	'question'	=> "FIRMA",
	'answer'	=> "Freshwater Integrated Resource Management with Agents.  The FIRMA 
	project is supported by European Union's Framework 5 Programme for Research and Development 
	and by the European Commission as part of its Key Action on Sustainable Management and 
	Quality of Water programme (contract EVK1-CT1999-00016), 2000-2003.  The Z¸rich Water Game 
	has been developed as a part of this project."
	),

array(
	'id'		=> 'faq12',
	'question'	=> "Facilitator",
	'answer'	=> "The facilitator is the key person responsible for the smooth 
	running of the game while players are playing.  He or she will identify themselves 
	to the players before the game and be the contact person for any problems or questions 
	that arise during play."  
	),

array(
	'id'		=> 'faq13',
	'question'	=> "Factories",
	'answer'	=> "In order for manufacturers to produce normal or water saving 
	sanitary water systems, they have to build the appropriate factory.  Once built, a 
	factory can only produce one system for sale per turn.  Factories can grow old and 
	must be maintained at a cost to the manufacturers or they become unusable.  They can 
	also be replaced or converted to another type of system.  A manufacturer can either 
	build, repair or change a factory type during one turn.  The state and age of factories 
	is identified on the top left-hand corner of the manufacturer player pages."  
	),

array(
	'id'		=> 'faq14',
	'question'	=> "Filtration",
	'answer'	=> "The waste water utility is responsible for maintaining the 
	waste water cleaning system.  Three types of filtration are available:  low protection 
	mechanical filtration (Ä100 per turn); medium protection nutrient and mechanical 
	filtration (Ä200 per turn); and high protection biological, nutrient and mechanical 
	filtration (Ä300 per turn)."  
	),

array(
	'id'		=> 'faq15',
	'question'	=> "Houses",
	'answer'	=> "The state and age of houses of the housing associations is 
	listed in the upper left-hand corner of the housing association player pages.  The 
	lifetime of a water sanitary system for each house is eight years."
	),

array(
	'id'		=> 'faq16',
	'question'	=> "Housing Associations",
	'answer'	=> "There are two housing association roles.  They both need to provide 
	water and pay for the amount of water consumed by their tenants.  They can choose to 
	purchase either normal or water saving water sanitary systems from one of the manufacturers 
	in order to replace aging systems that will break down after a number of years.  Bargaining 
	with manufacturers over system prices and negotiating with the politician for subsidies 
	can help to bring costs down.  Maximizing profits and being the most successful housing 
	association are the two main objectives of this role."
	),

array(
	'id'		=> 'faq17',
	'question'	=> "Leaving",
	'answer'	=> "If you wish to leave the game, click on the 'leave' button on 
	the bottom right-hand corner of the player pages.  On the next page, choose 'leave' 
	if you would like to return to the game later, 'quit' if you do not intend to return, 
	or 'resume' if you would like to continue playing."
	),

array(
	'id'		=> 'faq18',
	'question'	=> "Manufacturers",
	'answer'	=> "There are two manufacturer roles.  They compete with each other 
	to sell normal or water saving sanitary water systems to housing associations.  In 
	order to produce either system, they have to build the appropriate factory.  Their 
	ultimate goal is to maximize profits."
	),

array(
	'id'		=> 'faq19',
	'question'	=> "Negotiations/Discussions (Private)",
	'answer'	=> "In order to negotiate or communicate privately with another 
	player, click on the 'private negotiations' tab at the top of the main player page.  
	Select the player with whom you would like to negotiate from the scrolling feature.  
	Write a message in the text box and press 'send' to send the message.  You receive 
	private messages above the text box.  The most recent message is listed at the top."
	),

array(
	'id'		=> 'faq20',
	'question'	=> "Negotiations/Discussions (Public)",
	'answer'	=> "In order to negotiate or communicate with all players, click on 
	the 'public negotiation' tab at the top of the main player page.  Write a message in 
	the text box and press 'send' to send a message.  The message will be displayed under 
	the 'public discussion forum' list (above the text box) and will identify the day, 
	time and player who sent it.  The most recent message is listed at the top."
	),

array(
	'id'		=> 'faq21',
	'question'	=> "Objectives",
	'answer'	=> "Each player can choose to rank their own objectives with 'not 
	important,' 'important,' and 'very important.'  The list of objectives includes 
	ensuring:  'water supply,' water supply is greater than demand; 'water price,' water 
	price is low; 'political popularity,' the politician gets re-elected; 'lake water,' 
	high lake water quality; and 'profit,' high trade surplus.  Ranking these objectives 
	occurs after you register as one of the players."
	),

array(
	'id'		=> 'faq22',
	'question'	=> "Other players",
	'answer'	=> "This tab is on all of the player pages and identifies which players 
	are online, the day and time that they logged on, their last actions and current bank 
	balances."
	),

array(
	'id'		=> 'faq23',
	'question'	=> "Political popularity",
	'answer'	=> "Political popularity is the measurement of the popularity of the 
	politician.  Popularity can be maintained by advertising political popularity at a 
	cost (offered to all players), maintaining a balance between secure water supply 
	(50% > demand) and an efficient supply (supply is just over demand), maintaining high 
	water quality, and having the politician negotiate with and/or subsidize other stakeholders 
	to encourage them to support his or her interests.  Popularity is lost when supply is 
	too high or too low in relation to demand, if water quality decreases or if water prices 
	rise without a successful referendum of the people."
	),

array(
	'id'		=> 'faq24',
	'question'	=> "Politician",
	'answer'	=> "The politician's main objective is to maintain his or her popularity 
	in order to get re-elected every four years when general elections are held.  If the 
	politician is not re-elected, granting subsidies, holding referendums and changing tax 
	rates cannot be performed.  See 'political popularity' for hints about maintaining or 
	losing political popularity."
	),

array(
	'id'		=> 'faq25',
	'question'	=> "Referendums",
	'answer'	=> "The politician can hold referendums in order to evaluate current 
	public opinion.  The results, either 'Yes' or 'No' votes can help the politician make 
	decisions, i.e. agree to a water price rise for the water utility, that will maintain 
	his or her popularity.  There is no cost attached to this action."
	),

array(
	'id'		=> 'faq26',
	'question'	=> "Requests (new water price)",
	'answer'	=> "The Water Utility can request a new water price from the politician 
	who can choose to accept or reject it.  The correspondence is recorded under the 'Bank 
	Statement' and 'Diary of Events' tabs."
	),

array(
	'id'		=> 'faq27',
	'question'	=> "Requests (water equipment prices)",
	'answer'	=> "Requests for either normal or water saving water equipment prices 
	can be made by the Housing Associations to the Manufacturers by using the 'get quotes 
	for a sanitary system' action.  The quotes will appear under the 'bank statement' tab.  
	The Housing Associations can choose to accept, reject or counter-offer."
	),

array(
	'id'		=> 'faq28',
	'question'	=> "Reservoirs",
	'answer'	=> "The Water Utility is responsible for the maintenance of the reservoirs 	
	and can either close, build or repair them.  The age of each reservoir is listed in 
	the top left-hand corner of the 'water utility' player page."
	),

array(
	'id'		=> 'faq29',
	'question'	=> "Roles",
	'answer'	=> "There is a selection of seven roles from which players can choose:  
	two water technology manufacturers, 2 housing associations, 1 water utility, 1 politician 
	and 1 waste water utility.  Descriptions of each role are provided after clicking onto 
	one of the role icons on the 'Z¸rich Water Game Roles' page.  To assume one of these roles 
	simply move through their descriptions using the 'next' buttons and register with your 
	name, a password provided by the facilitator and email address."
	),

array(
	'id'		=> 'faq30',
	'question'	=> "Rules",
	'answer'	=> "There are a set of rules that constrain and support the actions 
	taken by players as well as events that can occur, i.e. a politician's popularity 
	has increased by 2 because water quality has risen above 7 or because lake water 
	quality fell below 5, environmental awareness has increased by 3.  When they are 
	activated they are listed under the 'diary of events' tab.  The complete list of rules 
	for ZWG3 can be found at:  http://www.soc.surrey.ac.uk/~scs1ng/zwg3/source.php."  
	),

array(
	'id'		=> 'faq31',
	'question'	=> "Scale bars",
	'answer'	=> "The scale bars at the bottom of each player page indicate the 
	current measurements for eight items:  individual player rankings; water demand; 
	water supply; water price; water quality; political popularity; lake water quality; 
	and environmental awareness.  Moving a cursor over each scale bar will give a more 
	specific definition of each measurement including what it is influenced by and what 
	it influences.  These definitions will appear under the 'bank statement' tab."
	),

array(
	'id'		=> 'faq32',
	'question'	=> "Turns",
	'answer'	=> "The length of a turn determines how often a player can take an 
	action.  It is arbitrarily set by the facilitator and can change each game.  Make 
	sure that the facilitator has informed players of the length of a turn before each 
	game."  
	),

array(
	'id'		=> 'faq33',
	'question'	=> "Waste water utility",
	'answer'	=> "The waste water utility is responsible for maintaining the waste 
	water cleaning system for the metropolitan area of Z¸rich.  It is publicly owned but 
	financially self-supporting.  It is concerned that water saving may be a problem since 
	income is dependent on water consumption and fixed costs will remain and lower water 
	consumption also places an added pressure on transporting waste efficiently from households."  
	),

array(
	'id'		=> 'faq34',
	'question'	=> "Water quality (improving)",
	'answer'	=> "Water quality can be improved when the Water Utility purchases 
	and installs new technology at the cost of Ä1000."  
	),

array(
	'id'		=> 'faq35',
	'question'	=> "Water supply system",
	'answer'	=> "Currently, the system has approximately 100% over-capacity with 
	respect to average demand."  
	),

array(
	'id'		=> 'faq36',
	'question'	=> "Water utility",
	'answer'	=> "The water utility is responsible for maintaining the water supply 
	system and water quality of the metropolitan area of Z¸rich.  It is publicly owned 
	but financially self-supporting.  It has two major concerns:  1) a decrease in demand 
	would mean price rises and a risk of angering the public and 2) water quality could 
	be threatened by a decreased demand that would decrease the flow within the system."  
	),

array(
	'id'		=> 'faq37',
	'question'	=> "Years",
	'answer'	=> "One year passes once the players have collectively taken ten actions."
	)

);

