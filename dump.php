<?php

include("constants.php");
include("common.php");
include("query.php");
include("objects.php");
  
db_open($database);
$zurich = db_retrieve("Zurich", "objects");
$player = $_COOKIE['player'];
?>
<html>
<head>
<title>Dump of ZWG3</title>
<link rel="stylesheet" href="zwg.css">
</head>
<body>
<?php
function print_vars($obj) {
    $arr = get_object_vars($obj);
    if ($arr) {
	    while (list($prop, $val) = each($arr)) {
	    	$type = gettype($val);
	    	if ($type == "array") {
	    		echo "$prop = $val<BR>\n<blockquote>\n";
	    		if ($obj->$prop) 
	    			foreach ($obj->$prop as $k => $o) {
	    				echo "[$k] <BR>";
		    			print_vars($o);
		    			echo "<br>\n";
	    		}
	    		echo "\n</blockquote>\n";
	    	}
	    	else { /* $type != array */
	        	echo "$prop = $val";
	        	if ($val > 1000000000) {
	        		$time = date("g:i a D, d M", $val);
	        		echo " ($time)";
	        	}
	        	echo "<BR>\n";
	    	}
	    }
    }
    else {echo "$obj";}
}

function print_log() {
	$query = new Query("SELECT id, name, action, 
					to_char(time, 'HH24:MI on DD Mon') as timestring
					FROM log
					ORDER BY id DESC");
	 echo "<table width=\"90%\" border=0 cellspacing=2 cellpadding=0>";
	$query->last_rec();
	while($query->prev_rec()) {
		$log_time = $query->field('timestring');
		$log_id = $query->field('id');
		$log_name = $query->field('name');
		$log_action = $query->field('action');
		echo "<tr><td colspan=3 valign=top bgcolor=white>
		    <span class=smalltext>$log_time</span></td>
			<td width=76 valign=top bgcolor=white>
			<span class=smalltext>";
	    echo "&nbsp;$log_name&nbsp;";
		echo "</span></td><td valign=top bgcolor=white>
			<span class=smalltext>$log_action</span></td>\n</tr>\n";
    }
	echo "</table>\n";
}


 
echo "<H3>Dump of the ZWG database: $database at " . date("g:ia, l j F Y") . "</H3>";

echo "Player from cookie: [$player]<P>";
	    
print_vars ($zurich);

echo "<h2>The complete action log</h2>";
print_log();

?>
<center><h2>End</h2></center>
</body>
</html>
