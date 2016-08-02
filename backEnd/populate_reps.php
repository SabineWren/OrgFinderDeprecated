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

	$prepared_replace_rep = $connection->prepare("REPLACE INTO tbl_Persons (Name) VALUES (?)");
	$prepared_replace_rep ->bind_param("s", $Name);

	$file = file('./reps.txt');

	foreach ($file as $rep_num => $rep) {
		$Name = $rep;
		$prepared_replace_rep->execute();
	}

	$prepared_replace_rep->close();

	$connection->close();
?>
