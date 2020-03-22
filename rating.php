<?php
/***********************************************
   Zurich Water Game: internet version
   Author:  Nigel Gilbert and the FIRMA project
   --------------------------------------------
   rating.php
   
   User arrives here from email.php
   This page check the user's password and does login
   stuff, including setting a cookie.  
   Also asks user to rate the criteria on
   importance (used for calculating the user's score)
   
   Input variables: 
   	$id -- player
   	$realname
   	$email_addr
   	$passwd
   	$checked -- if 1, second time through this page
   Output variables
    $rank0 .. rank4 -- the weights
    
   Version 2.1  7 April 2002

**********************************************/

include("constants.php");
include("common.php");
include("query.php");
include("objects.php");

	open_database();

	$passwd = get_param('passwd');
	$checked = get_param('checked');
	$newplayer = get_param('newplayer');
	$id = get_param('id');
	$email_addr = get_param('email_addr');
	$realname = get_param('realname');
	
    /* check password is OK */
    if (!$checked and $passwd != 'firma') {
        alert('Incorrect password!', 1);
    }
    /* check that someone else is not already playing this role */
    if (!$checked and isset($zurich->players[$newplayer]->loggedin) and $id != 'bank') {
    	echo "<html><head><SCRIPT language=Javascript>
    	function check() {
    		if (confirm('Warning: someone is already playing the ' + '";
    		echo $zurich->players[$id]->name; 
    		echo "' + ' role! ' +
    		'Do you want to continue?'))
    			location.replace(\"rating.php?id=$id&checked=1\");
    		else history.go(-1);
    		}
    	</SCRIPT></head><body onLoad=\"check()\"></body></html>";
    	exit;
	}
	/* remember this player's role in a cookie, with expiry time set to 2030AD */
	setcookie("player", $id, mktime(0,0,0,1,1,2030), '/', '', 0);
    log_and_confirm("joined the game", $id, 2);
    $zurich->players[$id]->loggedin = date("g:i a D, d M");
    if ($email_addr) $zurich->players[$id]->email = $email_addr;
    if ($realname) $zurich->players[$id]->realname = $realname;
    db_save($zurich, "Zurich", "objects");
	$player_name = $zurich->players[$id]->name;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html lang="en">
<head>
<title>The Zurich Water Game: <?php echo $player_name; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta name="author" content="Nigel Gilbert and Sarah Maltby, FIRMA project (C)">
<style type="text/css">
a.menu:link, a:menu:active, a.menu:visited {
		font-family: verdana,arial,helvetica,sans-serif;
		font-size: 14px;
		font-weight: bold;
		color: #333333;
		text-decoration: none;
		}
a.menu:hover {
		font-family: verdana,arial,helvetica,sans-serif;
		font-size: 14px;
		font-weight: bold;
		color: #336699;
		text-decoration: none;
		}
.here {
		font-family: verdana,arial,helvetica,sans-serif;
		font-size: 14px;
		font-weight: bold;
		color: #336699;
		text-decoration: none;
		}
.title {
		font-family: verdana,arial,helvetica,sans-serif;
		font-size: x-small;
		font-weight: bold;
		color: #000066;
		}
.sidetitle {
		font-family: verdana,arial,helvetica,sans-serif;
		font-size: x-small;
		font-weight: bold;
		color: black;
		text-align: right;
		}		
.body {	
		font-family: verdana,arial,helvetica,sans-serif;
		font-size: xx-small;
		font-weight: normal;
		color: black;
		}
.sub-head {	
		font-family: verdana,arial,helvetica,sans-serif;
		font-size: x-small;
		line-height:150%;
		font-weight: normal;
		color: #000066;
		}	
</style>
</head>
<body bgcolor="#FFFFFF" link="#003366" vlink="#333333" alink="#003366">
<!--Content designed for a window size of 760 by 420 (maximum size for a 800 by 600 screen)-->
<table width="750" border="0" cellspacing="0" cellpadding="0">
  <tr> 
  	<td width="30" rowspan=3><br></td>
    <td width="175" align=center> 
	  <p class=here>briefing
    </td>
    <td width="175" align=center> 
      <img src="images/firmatap.jpg" width="50" height="50">
    </td>
    <td width="175" align="center"> 
      <a href="roles0.php" class=menu>start play</a>
    </td>
    <td width="175" align="center"> 
      <img src="images/condensation.jpg" width="50" height="50">
    </td>
  </tr>
  <tr> 
    <td width="175" align="center"> 
      <img src="images/rain.jpg" width="50" height="50">
    </td>
    <td width="175" align="center"> 
      <a href="contact.html" class=menu>contact us</a>
    </td>
    <td width="175" align="center"> 
      <img src="images/streamwater.jpg" width="50" height="50">
    </td>
    <td width="175" align="center"> 
      <a href="acknowledgements.html" class=menu>acknowledgements</a>
    </td>
  </tr>
  <tr>
    <td colspan="4">
      <br>
      <img src="./images/top_line.gif" height=1 width=700>
    </td>
  </tr>
  <tr> 
    <td colspan="5"> 
    <form method=post action="main.php">
      <table width="100%" border="0" cellpadding="30" align="center">
        <tr> 
          <td width="540" valign="top" align="left"> 
          <!--BODY CONTENT GOES HERE-->
          <!--use class=title for headings and class=body for copy-->
          <table width="100%" border=0>
	          <tr>
		          <td>
			          <p class=title>YOUR OBJECTIVES</p>
		          </td>
		       </tr>
		       <tr>
		          <td colspan=2>
		          <p class=body>Please indicate the objectives that you will seeking to achieve:</p>
		          </td>
	          </tr>
	          <tr>
	          	<td><p class=sub-head>Water supply</p></td></tr>
	          <tr>
		          <td width="50%">
			          <p class=body>Aim: to ensure that the supply of water is greater than the demand for water
			      </td>
			      <td>
<?php
function makeselector($component) {
		/* construct a pop up with the default or previously chosen importance already selected */
	
	global $zurich, $id;
	
	echo "<select name=rank$component>\n";
	for ($i = 3; $i > 0; $i--) {
		echo "<option value=$i";
		if ($i == $zurich->players[$id]->rating_weight[$component]) echo " SELECTED";
		echo ">";
		switch ($i) {
			case 3: echo "Very important"; break;
			case 2: echo "Important"; break;
			case 1: echo "Not important"; break;
		}
		echo "</option>\n";
	}
	echo "</select>\n";
}	
					makeselector(0);
?>	
			      </td>
			   </tr>
	          <tr>
	          	<td><p class=sub-head>Water price</p></td></tr>
	          <tr>
		          <td width="50%">
			          <p class=body>Aim: to ensure that the price of water charged to consumers is low
			      </td>
			      <td>
<?php
					makeselector(1);
?>			      
				  </td>
			   </tr>
	          <tr>
	          	<td><p class=sub-head>Political popularity</p></td></tr>
	          <tr>
		          <td width="50%">
			          <p class=body>Aim: to ensure that the politician is popular and gets re-elected
			      </td>
			      <td>
<?php
					makeselector(2);
?>			      
			      </td>
			   </tr>
	          <tr>
	          	<td><p class=sub-head>Lake water</p></td></tr>
	          <tr>
		          <td width="50%">
			          <p class=body>Aim: to ensure the quality of water in the lake is high
			      </td>
			      <td>
<?php
					makeselector(3);
?>			      
			      </td>
	          <tr>
	          	<td><p class=sub-head>Profit</p></td></tr>
	          <tr>
		          <td width="50%">
			          <p class=body>Aim: to ensure that your trading surplus is high
			      </td>
			      <td>
<?php
					makeselector(4);
?>			      
			      </td>
			   </tr>
				</table>          
			</td>
          <td valign="top" align="left"> 
          <!--SIDEBAR CONTENT GOES HERE-->
<!--          Use class=sidetitle for titles, class=body for content-->
			<p class=sidetitle>start play
			<input type=image name=submit src="./images/arrow.gif" alt="Arrow" width="30" height="26" align=absmiddle></p>
          </td>
        </tr>
      </table>
      </form>
    </td>
  </tr>
</table>
</body>
</html>
