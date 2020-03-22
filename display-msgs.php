<?php

/***********************************************
   Zurich Water Game: internet version
   Author:  Nigel Gilbert and the FIRMA project
   --------------------------------------------
   display-msgs.php
   
   Displays the layers for public and private discussions
   
   Version 1.0  6 August 2001
   Version 1.1  1 September 2001
   Version 2.0  10 November 2001
   Version 2.1  7 April 2002
**********************************************/

    $n_msgs = 20;  // max.number of messages to display in the scrolling message header box  

	/* Public discussion */
?>
<!--display messages for public forum-->
<div id="publiclayer" style="position:absolute; left:320px; top:40px; width:410px; height:330px; 
		z-index:2; visibility:hidden;  background-color:white">
  	<p class=actiontext>Public discussion forum</p> 
<?php
    /* get the last few public messages */
    $query = new query("SELECT id, sender, recipient, timesent, to_char(timesent, 'HH24:MI on DD Mon') as senttime, 
                            timeread, msg 
                            FROM msgs 
                            WHERE (recipient = 'All') 
                            ORDER BY timesent DESC LIMIT $n_msgs");
	echo "<div id=\"pubmsglist\" style=\"position:absolute; left:0px; top:20px; width:410px; height: 90px; overflow: auto\">";
   $new_public_msg = display_msgs($query); 

    /* display the message composition area */
?>
<div id="pubmsgwrt" style="position:absolute; left:0px; top:210px; width:410px; height: 110">
<?php
	echo "<form method=post action=main.php><span class=actiontext>Write your message in the box:<br></span>\n";
	echo "<textarea name=msg cols=40 rows=4></textarea>";
	echo "<input type=hidden name=recipient value='All'>";
	echo "<p align=right><input type=image align=middle src=\"images/send.jpg\" width=45 height=23 name=submit value=\"Send\"></p>
		</form>";
?>
</div>
</div>

<!--display messages for private forum-->

<div id="privatelayer" style="position:absolute; left:320px; top:40px; width:410px; height:330px;
		z-index:2; visibility:hidden;  background-color:white">
  	<p class=actiontext>Messages to you</p> 
<?php
    $query = new query("SELECT id, sender, recipient, timesent, to_char(timesent, 'HH24:MI on DD Mon') as senttime, 
                            timeread, msg 
                            FROM msgs 
                            WHERE recipient='$player' AND sender != '$player' 
                            ORDER BY timesent DESC LIMIT $n_msgs");
	echo "<div id=\"privmsglist\" style=\"position:absolute; left:0px; top:20px; width:410px; height: 90px; overflow: auto\">";
    $new_private_msg = display_msgs($query);

    /* display the message composition area */
?>
<div id="privmsgwrt" style="position:absolute; left:0px; top:210px; width:410px; height: 110">
<?php
	echo "<form method=post action=main.php name=privform 
		onSubmit=\"return document.privform.recipient.value != 'xx'\">
		<span class=actiontext>Write your message in the box:<br></span>\n";
	echo "<textarea name=msg cols=40 rows=4></textarea><br>";
	echo "<span class=actiontext>Send the message to
			<select name=recipient><option value='xx'>choose player...</option>\n";
	foreach ($zurich->players as $p) {
		if ($p->id != $player)
		    printf("<option value=%s>%s</option>\n", $p->id, $p->name);
		}
	echo "</select></span>
		<input type=image align=middle src=\"images/send.jpg\" width=45 height=23 name=submit value=\"Send\">
		</form>";
?>
</div>
</div>

<?php
#
#	if ($new_public_msg) {
#		echo "There is news in the public discussion area<BR>";
#	}
#	if ($new_private_msg) {
#		echo "There is a new message waiting for you in the private negotiation area<BR>";
#	}
    
function display_msgs($query) {
	/* display a table containing a header for each message so that user can click on it to get the
	whole thing. Return TRUE if any message is 'new' (unread) */
	
	global $playobj;
?>	
	<table width="90%" border="0" cellspacing="2" cellpadding="0"><tr><td>
<?php
	$msgcnt = 0;
	$new_msg = 0;
	while ($query->next_rec()) {
		$msgcnt++;
		$msg = $query->field("msg");
		/* extract the first 30 or so characters of the message (removing newlines and multiple spaces) */
		$msg_start = preg_replace("/[\s]+/", " ", $msg);
		if (strlen($msg_start) > 34) {
			$msg_start = substr($msg_start, 0, 34) . "...";
			} 
		/* set flag if any message is not yet read */
		$timesent = strtotime(substr($query->field("timesent"), 0, strpos($query->field("timesent"), ".")));
		if (!$new_msg) $new_msg = ($timesent >= $playobj->last_refresh);
		printf("<a class=hdrtext href=\"#\" onClick=\"showmsg('msg%s','','show')\"> %s at %s</a>
					<span class=smalltext> %s&nbsp;&nbsp;%s</span><BR>\n",
				$query->field("id"),
				$playobj->id_to_name($query->field("sender")),
				$query->field("senttime"), 
				($timesent < $playobj->last_refresh ? "" : "[NEW]"),
				$msg_start);
		}
	echo "</td></tr></table></div>\n\n";
	
	/* write out each message as a hidden layer */
	$query->last_rec();
	while ($query->prev_rec()) {
		$msg = $query->field("msg");
		$id = $query->field("id");
		echo "<div id=\"msg$id\" style=\"position:absolute; left:0px; top:110px; width:410px; height:80; 
			visibility:hidden; border: 1px solid #003399; overflow:auto\">
      	<table width=\"90%\" border=0 cellspacing=2 cellpadding=0>
      	<tr><td><span class=subaction>$msg</span></td></tr></table></div>";
	}
	return $new_msg;
}


?>
