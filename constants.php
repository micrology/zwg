<?php

/***********************************************
   Zurich Water Game: internet version
   Author:  Nigel Gilbert and the FIRMA project
   --------------------------------------------
   constants.php
   
   This file contains the values of global
   constants.  It is included where ever these
   constants are referenced.
   
   Version 1.0  6 August 2001
   Version 1.1  1 September 2001
   Version 2.1  7 April 2002

**********************************************/

$log_level = 2;			    // controls about of detail displayed in the 
							//  Diary of Events
							// 2 is a normal level, 0 gives masses of detail, 

$database = "zwg";  		// name of the database

$govt_duration = 5;         // years between elections
$send_emails = TRUE;		// send an email to players every day 
							//  (email_notify_interval) listing the
							//  actions carried out

$email_notify_interval = 24 * 60 * 60;  // normally 24 * 60 * 60
$admin = "n.gilbert@surrey.ac.uk"; //email address of administrator


define("CURRSYM", "&#8364;"); // unicode symbol for the currency (&#8364; = euro)
