<?php

/***********************************************
   Zurich Water Game: internet version
   Author:  Nigel Gilbert and the FIRMA project
   --------------------------------------------
   common.php
   
   Functions that are referenced from many different points.
   This file must be included once and once only.
   
   Version 1.0  6 August 2001
   Version 1.1  1 September 2001
   Version 2.0  10 November 2001
   Version 2.1  7 April 2002

**********************************************/

// seed the random number generator with the current time

mt_srand(time());
srand((double)microtime()*1000000);

function html_header($title, $bgcolor = "#FFFFFF", $link = "#0000FF", $text = "#000000") {
    /* insert main HTML page header, and the javascript that drives the page tab display */

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<META HTTP-EQUIV="expires" CONTENT="now">
<META HTTP-EQUIV="pragma" CONTENT="no-cache">
<META HTTP-EQUIV="REFRESH" CONTENT="300">
<TITLE><?php echo $title ?></TITLE>
<link rel="stylesheet" href="zwg.css">
<script language="JavaScript">
<!--

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

var nowVisible;	// the object that is the currently visible layer
var nVid;		// the id of the object that is the currently visible layer
var msgVisible;	// which public or private message is currently on display

function MM_showHideLayers() { //v3.0
  var i,p,v,obj,args=MM_showHideLayers.arguments;
  if (nowVisible) {nowVisible.visibility='hidden'; /*alert('Hiding ' + nVid);*/ }
  if (msgVisible) msgVisible.visibility='hidden';
  for (i=0; i<(args.length-2); i+=3) if ((obj=MM_findObj(args[i]))!=null) { v=args[i+2];
    if (obj.style) { obj=obj.style; v=(v=='show')?'visible':(v='hide')?'hidden':v; }
    obj.visibility=v; nowVisible=obj; nVid=args[i]; /*alert('Showing ' + args[i]);*/}
}

var prevLayer;	// the layer that was visible before a help message is displayed

function displayhelp(helplayer) {
	prevLayer=nVid;
	MM_showHideLayers(helplayer,'','show');
}

function hidehelp() {  // hide a help message if there is one
	if (prevLayer) MM_showHideLayers(prevLayer,'','show');
	prevLayer = "";
}

function showmsg() { // shows (or hides) a private or public message
  var i,p,v,obj,args=showmsg.arguments;
  if (msgVisible) msgVisible.visibility='hidden'; 
  for (i=0; i<(args.length-2); i+=3) if ((obj=MM_findObj(args[i]))!=null) { v=args[i+2];
    if (obj.style) { obj=obj.style; v=(v=='show')?'visible':(v='hide')?'hidden':v; }
    obj.visibility=v; msgVisible=obj;}
}

function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}

function MM_nbGroup(event, grpName) { //v3.0
  var i,img,nbArr,args=MM_nbGroup.arguments;
  if (event == "init" && args.length > 2) {
    if ((img = MM_findObj(args[2])) != null && !img.MM_init) {
      img.MM_init = true; img.MM_up = args[3]; img.MM_dn = img.src;
      if ((nbArr = document[grpName]) == null) nbArr = document[grpName] = new Array();
      nbArr[nbArr.length] = img;
      for (i=4; i < args.length-1; i+=2) if ((img = MM_findObj(args[i])) != null) {
        if (!img.MM_up) img.MM_up = img.src;
        img.src = img.MM_dn = args[i+1];
        nbArr[nbArr.length] = img;
    } }
  } else if (event == "over") {
    document.MM_nbOver = nbArr = new Array();
    for (i=1; i < args.length-1; i+=3) if ((img = MM_findObj(args[i])) != null) {
      if (!img.MM_up) img.MM_up = img.src;
      img.src = (img.MM_dn && args[i+2]) ? args[i+2] : args[i+1];
      nbArr[nbArr.length] = img;
    }
  } else if (event == "out" ) {
    for (i=0; i < document.MM_nbOver.length; i++) {
      img = document.MM_nbOver[i]; img.src = (img.MM_dn) ? img.MM_dn : img.MM_up; }
  } else if (event == "down") {
    if ((nbArr = document[grpName]) != null)
      for (i=0; i < nbArr.length; i++) { img=nbArr[i]; img.src = img.MM_up; img.MM_dn = 0; }
    document[grpName] = nbArr = new Array();
    for (i=2; i < args.length-1; i+=2) if ((img = MM_findObj(args[i])) != null) {
      if (!img.MM_up) img.MM_up = img.src;
      img.src = img.MM_dn = args[i+1];
      nbArr[nbArr.length] = img;
  } }
}
//-->
</script>
</HEAD>
<?php
    echo    "<BODY bgcolor=\"$bgcolor\" text=\"$text\" link=\"$link\"  vlink=\"$link\" alink=\"$link\"";
    echo "\nonLoad=\"MM_preloadImages(";
    foreach (array ("bank", "diary", "public", "private", "data", "help") as $tab) {
    	for ($i = 1; $i <= 4; $i++) {
    		if ($comma_needed) echo ", ";
    		echo "'images/tab_${tab}${i}.gif'";
    		$comma_needed = TRUE;
    	}
    }
    echo ")\">\n\n";
}

function html_footer() {
/* display standard HTML footer */

    echo "</BODY>\n</HTML>";

}

function item($action, $label, $detail, $help) {
    /* construct the HTML for an item in the actions menu */
    echo "<tr><td width=27><input type=radio name=action value=$action></td>
<td width=22><a href=\"#\" onClick=\"displayhelp('$help')\" onMouseOut=\"hidehelp('$help')\">
<img src=\"images/button_question.gif\" width=18 height=18 align=middle border=0></a></td>
<td align=left><span class=actiontext>$label</span>";
    	if ($detail) echo " <span class=subaction>$detail</span>";
    	echo "</td></tr>\n";
}

function make_popup($var, $resources) {
		/* returns a string that makes a pop up menu when displayed by the
		broowser, with the keys from array $resources, to obtain a value 
		for $var.
		The menu will return the key when the user selects an item */
		
		$str = "<select name=$var>";
		foreach (array_keys($resources) as $key) {
			$str .= "<option value=$key>$key</option>\n";
		}
		return $str . "</select>\n";
}

function reply_button() {
		/* generate HTML for a submit button with text "reply" */
		
		return " <input type=image name=submit src=\"images/reply.jpg\" 
		width=45 height=23 border=0 align=middle>";
}

function alert($message, $back=0) {
/* put up a modal dialog box with the message */

    html_header("Alert");
    echo "<SCRIPT> alert('$message');";
    if ($back) echo " history.go(-1)";
    echo "</SCRIPT>";
    html_footer();
}

function dice_up($current_value, $lo, $hi) {
/* returns an increment for $current_value by a random value between $lo and $hi.
   Then ensures that the new value lies between the limits 0 to 10.
   Returns the adjusted increment */
   
   $old_value = $current_value;
   $current_value += mt_rand($lo, $hi);
   if ($current_value > 10) $current_value = 10;
   if ($current_value < 0) $current_value = 0;
   return $current_value - $old_value;
}

function dice_down($current_value, $lo, $hi) {
/* returns an decrement for $current_value by a random value between $lo and $hi.
   Then ensures that the new value lies between the limits 0 to 10.
   Returns the adjusted increment */
   
   $old_value = $current_value;
   $current_value -= mt_rand($lo, $hi);
   if ($current_value > 10) $current_value = 10;
   if ($current_value < 0)  $current_value = 0; 
   return $old_value - $current_value;
}

function log_and_confirm($text, $who, $detail) {
	/* insert text into the log
	Also, provide feedback to player by writing on screen */
	
	global $playobj, $log_level, $reasons, $softbot;

	/* append user's reasons for the action if the user is human */
	
	if ($reasons and ! $softbot and $who == $playobj->id) {
		$text .= " ($reasons)";
		$reasons = '';
	}
	if ($detail >= $log_level and isset($playobj->loggedin)) {
		if ($who == 'Event') echo "$text<br>";
		if ($who == $playobj->id) echo "You $text<br>";
		if ($who == 'bank') echo $playobj->name . "$text<BR>";
	}
		
    /* softbots are distinguished in the log by having names that start
    with a * */
	if ($softbot) $who = "*" . $who;
	log_act($text, $who, $detail);
}

function log_act($text, $name, $detail) {
	/* write text into the play log */

	global $zurich;	
	
	$text = addslashes($text);
	db_write("INSERT INTO log (name, action, detail, time) 
				VALUES ('$name', '$text', '$detail', '$zurich->time')");
}

function state_to_name($state) {
	/* converts the state id to a literal name */
	
	switch ($state) {
	 	case 'water_demand': 			return "Water demand";       
		case 'water_supply': 			return "Water supply";
		case 'water_price': 			return "Water price";
		case 'water_quality': 			return "Water quality";
		case 'political_popularity': 	return "Political popularity";
		case 'lake_quality':  			return "Quality of the water in the lake";
		case 'env_awareness': 			return "Environmental awareness";
		}
}

function get_param($name) {
/* extracts a value from $_POST (i.e. a value supplied by the user on a 
form) and returns it */
    
    if (isset($_REQUEST[$name])) return $_REQUEST[$name];
    else return "";
}

function open_database() {
	/* open the database and retrieve the saved objects */
	
	global $database, $zurich, $action;
	
	db_open($database);
  
	$zurich = db_retrieve("Zurich", "objects");
	if (!$zurich or $action == 'reset') {
		$zurich = new Zurich(); // the very first time, create a new object
		}
	
	$zurich->set_time();
}

function display_player_name($id) {
	/* prints player's name colour coded */
	
	global $zurich;
	
	/* softbot's ids are preceded by * in the log; remove this */
	if ($id{0} == '*') {
		$robot = true;
		$id = substr($id, 1);
	}
	else $robot = false;
	
	switch($id) {
		case 'water_utility': $colour = "#0000ff"; break; /* blueberry */
		case 'waste_water_utility': $colour = "#804000"; break; /* mocha */
		case 'housing_assoc_1': $colour = "#ff6666"; break; /* salmon */
		case 'housing_assoc_2': $colour = "#800040"; break; /* maroon */
		case 'manufacturer_1': $colour = "#808000"; break; /* asparagus */
		case 'manufacturer_2': $colour = "#008040"; break;  /* moss */
		case 'politician': $colour = "#ff8000"; break; /* tangerine */
		case 'bank': $colour = "#ff66cc"; break; /* carnation */
		default: $colour = "#000000"; break; /* black */
	}
	
	/* translate between id and name for player ids */
	foreach ($zurich->players as $p) {
		if ($id == $p->id) {
			$name = $p->name;
			break;
		}
	}
	if (!isset($name)) $name = $id;
	if ($robot) $name = strtolower($name);
		
	return "<font color=$colour>$name</font>";
}

	
