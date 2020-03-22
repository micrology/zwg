<?php

/***********************************************
   Zurich Water Game: internet version
   Author:  Nigel Gilbert and the FIRMA project
   --------------------------------------------
   display-top.php
   
   Displays a panel for the main pages showing the headings and 
   setting up the tables. Also, the help and data on other player tabs
   
   Version 1.0  6 August 2001
   Version 1.1  1 September 2001
   Version 2.0  10 November 2001
   Version 2.1  7 April 2002

**********************************************/

/* get player heading */

switch ($player) {
 
 	case 'water_utility':  
 	    $title="images/title_player_water_utility.gif";
 	    break;
 	case 'waste_water_utility':  
 	    $title="images/title_player_waste_utility.gif";
 	    break;
 	case 'housing_assoc_1': 
 	    $title="images/title_player_housing_assoc1.gif";
 	    break;
 	case 'housing_assoc_2': 
 	 	$title="images/title_player_housing_assoc2.gif";
        break;
 	case 'manufacturer_1':      
 	    $title="images/title_player_manufacturer1.gif";
 	    break;
 	case 'manufacturer_2':      
 	    $title="images/title_player_manufacturer2.gif";
 	    break;
 	case 'politician':      
 	    $title="images/title_player_politician.gif";
 	    break;
  	case 'bank':      
 	    $title="images/title_player_bank.gif";
 	    break;
 	default:
 	    alert("Bad player name: $player");
 	    exit;
	}
?>
<!--display player name-->

<div id="Title" style="position:absolute; left:5px; top:5px;
 width:255px; height:25px; z-index:1">
<img src="<?php echo $title; ?>" width="175" height="20" align="top">
<span class=actiontext>&nbsp;year <?php echo $zurich->turn ?></span>
</div>

<!--display tabs-->

<div id="Tabs" style="position:absolute; left:300px; top:5px; width:600px; 
		height:400px; z-index:1">
<?php
	$tab_names = array ("bank", "diary", "public", "private", "data", "help");
	$image_no = 0;
	foreach ($tab_names as $tab) {
		$image_no++;
		echo "<a href=\"#\" 
				onClick=\"MM_nbGroup('down','group1','$tab','images/tab_${tab}2.gif',1);
					MM_showHideLayers('${tab}layer','','show')\" 
				onMouseOver=\"MM_nbGroup('over','$tab','images/tab_${tab}.gif','',1)\" 
				onMouseOut=\"MM_nbGroup('out')\"><img name=$tab src=\"images/tab_${tab}.gif\" 
					border=0 width=80 height=23></a>";
	}
?>
	<br><img src="./images/tab-border.gif" width="491" height="380">

</div>

<!--display help tab-->
	
<div id="helplayer" style="position:absolute; left:320px; top:40px; width:410px; 
		height:330px; background-color:white; z-index:2; visibility:hidden">

	<div id="faqlist" style="position:absolute; left:0px; top:20px; width:410px; height: 170px; overflow: auto">
	<table width="90%" border="0" cellspacing="2" cellpadding="0"><tr><td>
<?php
	/* get the FAQ texts */
	
	include("faqs.php");
	
	/* display a table containing a header for each FAQ so that user can click on it to get the
	whole thing */
	
	$i = 0;
	foreach ($faqs as $faq) {
		printf("<a class=faq href=\"#\" onClick=\"showmsg('%s','','show')\">%s.&nbsp;%s</a><BR>\n",
					$faq['id'],
					++$i,
					$faq['question']);
	}
	echo "</td></tr></table></div>\n\n";
	
	/* write out each FAQ as a hidden layer */
	foreach ($faqs as $faq) {
		printf("<div id=\"%s\" style=\"position:absolute; left:0px; top:210px; width:410px; height:120; 
			visibility:hidden; border: 1px solid #003399; overflow:auto\">
      		<table width=\"90%%\" border=0 cellspacing=2 cellpadding=0>
      		<tr><td><span class=subaction>%s</span></td></tr></table></div>",
      		$faq['id'],
      		$faq['answer']);
	}
	echo "</div>\n";
?>

<!--display system messages on bank tab-->

<div id="banklayer" style="position:absolute; left:320px; top:40px; 
	width:410px; height:330px; visibility:visible; z-index:2">
<div id="sysmsgs" style="position:absolute; left:10px; top:60px; 
	width:400px; height:270px; overflow:auto">
<span class=actiontext>
<?php

