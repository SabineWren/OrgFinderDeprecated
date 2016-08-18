<?php

mb_internal_encoding("UTF-8");

$orgsFile = file_get_contents('./star');
	$orgsLines = explode("\n", $orgsFile);
	
	
	foreach($orgsLines as $line){
		echo "$line\n";
	}
unset($orgsFile);


?>
