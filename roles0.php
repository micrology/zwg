<?php

/***********************************************
   Zurich Water Game: internet version
   Author:  Nigel Gilbert and the FIRMA project
   --------------------------------------------
   roles0.php
   
  If the user presses "start play" from the front page and has not previously
  been playing (i.e. there is no cookie), force user to go through the 
  briefing pages, otherwise jump straight to the playing page.
   
   Version 1.0  6 August 2001
   Version 1.1  1 September 2001
   Version 2.0  10 November 2001

**********************************************/

if ($player) {
	header("Location: main.php");
}
else {
	header("Location: briefing.html");
}
