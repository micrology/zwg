<?php

$msg = "In the Zurich Water Game, the actions carried out since Friday, 6:07 pm have been:
	 					
	 					Thursday, 1:33 am  changed factory a to water saving technology, costing &#8364;600";

 				// replace euro symbols 
 				$msg = preg_replace("/&#8364;\s*(\d+)/", "$1 euro", $msg);

$res = mail("n.gilbert@soc.surrey.ac.uk", "Test from www", $msg, 	 					"From: \"Zurich Water Game\"<n.gilbert@soc.surrey.ac.uk>\n" .
    					"Reply-To: \"Zurich Water Game\"<n.gilbert@soc.surrey.ac.uk>");

echo "Msg is: [$msg]<P>";
if ($res) {
	echo "<H1> Mail was sent </H>";
	}
else { echo "<H1>Mail failed </H1>"; }

