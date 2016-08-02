<?php
	/*
	@license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
	
	Copyright (C) 2016 LucFauvel and SabineWren
	
	GNU AFFERO GENERAL PUBLIC LICENSE Version 3, 19 November 2007
	https://www.gnu.org/licenses/agpl-3.0.html
	
	@license-end
	*/
	//1) Connect to DB
	$connection = new mysqli("localhost","root","","cognitiondb");
	if( mysqli_connect_errno() ){
		die( "Connection failed: " . mysqli_connect_error() );
	}

	$prepared_replace_repcog = $connection->prepare("REPLACE INTO tbl_Representscog (SID, Representative) VALUES (?, ?)");
	$prepared_replace_repcog ->bind_param("ss", $SIDs, $reps);

	$linesReps = file('./reps.txt');
	$linesSID = file('./orgs.txt');

	foreach (array_combine($linesReps, $linesSID) as $rep => $SID) {
		$reps = $rep;
		$SIDs = $SID;
		$prepared_replace_repcog->execute();
	}

	$prepared_replace_repcog->close();

	$connection->close();
?>
