<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML lang="en">
<HEAD>
<META HTTP-EQUIV="expires" CONTENT="now">
<META HTTP-EQUIV="pragma" CONTENT="no-cache">
<TITLE>Zurich Water Game: program source</TITLE>
</HEAD>
<BODY bgcolor="#FFFFFF">
<?php
    echo "<font color=red><H1>Zurich Water Game: program source</H1></font>";
    echo "<H3>" . date("g:ia, l j F Y") . "</H3>";
    $files =  array("action-top.php",
                    "bankpage.php",
                    "common.php",
                    "constants.php",
                    "createdb.sql",
                    "display-bank.php",
                    "display-generic.php",
                    "display-help.php",
                    "display-log.php",
                    "display-msgs.php",
                    "display-scales.php",
                    "display-top.php",
                    "dump.php",
                    "email.php",
                    "faqs.php",
                    "goodbye.php",
                    "hapage.php",
                    "leave.php",
                    "main.php",
                    "objects.php",
                    "polpage.php",
                    "prodpage.php",
                    "query.php",
                    "rating.php",
                    "roles0.php",
                    "rules.php",
                    "source.php",
                    "wspage.php",
                    "wwupage.php",
                    "zwg.css");
    sort($files);
    
    echo "<A NAME=\"TOP\"><HR><H2>Contents</H2></A><UL>\n";
    foreach ($files as $file) {
        echo "<LI><A HREF = \"#$file\">$file</A>\n";
        }
    echo "</UL><P>\n";
    
    foreach ($files as $file) {
        echo "<HR><A NAME=\"$file\"><H2>$file</H2></A>\n";
        highlight_file($file);
        echo "<HR><A HREF=\"#TOP\">Return to Contents...</A>\n";
        }
                    
?>
<HR><CENTER>***ENDS***</CENTER>
</BODY>
</HTML>
