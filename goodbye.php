<?php

/***********************************************
   Zurich Water Game: internet version
   Author:  Nigel Gilbert and the FIRMA project
   --------------------------------------------
   goodbye.php
   
   Logs out the user
   
   Version 1.0  6 August 2001
   Version 1.1  1 September 2001
   Version 2.0  10 November 2001

**********************************************/

include("constants.php");
include("common.php");
include("query.php");
include("objects.php");

$resume_x = get_param('resume_x');
$quit_x = get_param('quit_x');

$player = $_COOKIE['player'];

if ($resume_x) {  /* user pressed the resume button on leave.php */
    header("Location: main.php");
    exit;
}
  
db_open($database);
$zurich = db_retrieve("Zurich", "objects");
$zurich->players[$player]->loggedin = FALSE;
log_and_confirm("left the game", $player, 2);
    
if ($quit_x) { /* user pressed the quit button on leave.php */
    	setcookie("player",'', -3600);
    	unset($zurich->players[$player]->realname);
        unset($zurich->players[$player]->email);
}

// save a serialized copy of all objects
db_save($zurich, "Zurich", "objects");

header("Location: index.html");

