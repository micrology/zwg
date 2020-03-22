<?php
include("constants.php");
include("common.php");
include("query.php");
include("objects.php");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html lang="en">
<head>
<title>The Zurich Water Game</title>
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
</style>
</head>
<body bgcolor="#FFFFFF" link="#003366" vlink="#333333" alink="#003366">
<!--Content designed for a window size of 760 by 420 (maximum size for a 800 by 600 screen)-->
<table width="750" border="0" cellspacing="0" cellpadding="0">
  <tr> 
  	<td width="30" rowspan=3><br></td>
    <td width="175" align=center> 
	  <a href="briefing.html" class=menu>briefing</a>
    </td>
    <td width="175" align=center> 
      <img src="images/firmatap.jpg" width="50" height="50">
    </td>
    <td width="175" align="center"> 
      <p class=here>start play
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
    <form method=post action="rating.php">
      <table width="100%" border="0" cellpadding="30" align="center">
        <tr> 
          <td width="540" valign="top" align="left"> 
          <!--BODY CONTENT GOES HERE-->
          <!--use class=title for headings and class=body for copy-->
<?php
#     get existing name and email address if it exists
  
db_open($database);
$zurich = db_retrieve("Zurich", "objects");
$id = get_param('id');
$email_addr = "";
$realname = "";
if ($zurich) {
	$email_addr = isset($zurich->players[$id]->email) ? $zurich->players[$id]->email : "";
	$realname = isset($zurich->players[$id]->realname) ? $zurich->players[$id]->realname : "";
	}

switch($id) {
	case 'water_utility': $role_name = 'Water Utility';
		break;
	case 'waste_water_utility': $role_name = 'Waste Water Utility';
		break;
	case 'housing_assoc_1': $role_name = 'Housing Association 1';
		break;
	case 'housing_assoc_2': $role_name = 'Housing Association 2';
		break;
	case 'manufacturer_1': $role_name = 'Manufacturer 1';
		break;
	case 'manufacturer_2': $role_name = 'Manufacturer 2';
		break;
	case 'politician': $role_name = 'Politician';
		break;
	default: echo "<font color=red>Internal error: unknown id! </font>";
		break;
}
		  echo "<p class=title>$role_name</p>";
?>
          <p class=body><b>The next time you enter the game you can play immediately by clicking on 
                    "start play" above (next to the tap).</b></p>
          <p class=body>Before you start to play the game please fill in your name and email details 
            below so that we can keep you up to date with progress on the game and assist with your 
            game playing.  You will also need to enter the game password that you were given.</p>
          <table width="100%" border="0" cellpadding="10">
              <tr> 
                <td width="16%"><span class=body><b>name</b></td>
                <td width="84%"><input class=body type="text" name="realname" 
                		<?php echo "value='$realname'"; ?> size="50">
                		<input type=hidden name=id value=<?php echo $id; ?></td>
              </tr>
              <tr> 
                <td width="16%"><span class=body><b>password</b></td>
                <td width="84%"><input class=body type="password" name="passwd" 
                		 size="10"></td>
              </tr>
              <tr> 
                <td width="16%"><span class=body><b>email</b></td>
                <td width="84%"><input class=body type="text" name="email_addr" 
                	<?php echo "value='$email_addr'"; ?> size="50"></td>
              </tr>
            </table>
          </td>
          <td valign="top" align="left"> 
          <!--SIDEBAR CONTENT GOES HERE-->
<!--          Use class=sidetitle for titles, class=body for content-->
			<input type=hidden name=id value="<?php echo $id ?>" >
			<p class=sidetitle>next
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
