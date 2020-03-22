<?php

/***********************************************
   Zurich Water Game: internet version
   Author:  Nigel Gilbert and the FIRMA project
   --------------------------------------------
   action-top.php
   
   the HTML that forms the top of the Actions panel
   
   Version 1.0  6 August 2001
   Version 1.1  1 September 2001
   Version 2.0  10 November 2001
   Version 2.1  7 April 2002

**********************************************/

?>

<!-- display panel for player to choose an action from -->

<div id="Actions" style="position:absolute; left:5px; top:100px; 
    width:270px; height:217px; z-index:3"> 
<form method=post action=main.php>
  <table border=0 cellspacing=0 cellpadding=1>
    <tr> 
      <td colspan=4>
      	<img src="images/action_to_take.gif" width=250 height=18> 
      </td>
     </tr>
<?php
