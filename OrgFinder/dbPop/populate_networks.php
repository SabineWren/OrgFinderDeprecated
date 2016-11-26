<?php
	/*
	@license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
	
	Copyright (C) 2016 LucFauvel and SabineWren
	
	GNU AFFERO GENERAL PUBLIC LICENSE Version 3, 19 November 2007
	https://www.gnu.org/licenses/agpl-3.0.html
	
	@license-end
	*/
	//Connect to DB
	
	mb_internal_encoding("UTF-8");
	
	if( sizeof($argv) < 3){
		echo "Correct usage: php " . $argv[0] . " <db username> <db password>\n";
		exit();
	}
	
	$connection = new mysqli("localhost",$argv[1],$argv[2], "cognitiondb");
	if( mysqli_connect_errno() ){
		die( "Connection failed: " . mysqli_connect_error() );
	}
	if( !$connection->set_charset("utf8") )echo "Error changing connection character set\n";
	
	$connection->autocommit(FALSE);//accelerate inserts
	
	//Cognition Corp *************************************************
	/*echo "Cog\n";
	$prepared_insert_cog = $connection->prepare("INSERT INTO tbl_Cog (SID) VALUES (?) ON DUPLICATE KEY UPDATE SID = ?");
	$prepared_insert_cog ->bind_param("ss", $SID, $SID);
	
	$orgsFile = file_get_contents('./cog');
	$orgsLines = explode("\n", $orgsFile);
	unset($orgsFile);
	
	//Cog
	
	for($i = 0; $i < sizeof($orgsLines); ++$i){
		$SID = $orgsLines[$i];
		if(!$prepared_insert_cog->execute())echo "Failed to insert Org == $SID\n";
	}
	unset($orgsLines);
	$connection->commit();
	$prepared_insert_cog ->close();
	$connection->query('ALTER TABLE tbl_Cog ENGINE=INNODB');
	
	*/
	//OPPF *************************************************
	/*echo "OPPF\n";
	$prepared_insert_oppf = $connection->prepare("INSERT INTO tbl_OPPF (SID) VALUES (?) ON DUPLICATE KEY UPDATE SID = ?");
	$prepared_insert_oppf ->bind_param("ss", $SID, $SID);
	
	$orgsFile = file_get_contents('./oppf');
	$orgsLines = explode("\n", $orgsFile);
	unset($orgsFile);
	
	//Insert
	for($i = 0; $i < sizeof($orgsLines); ++$i){
		$SID = $orgsLines[$i];
		if(!$prepared_insert_oppf->execute())echo "Failed to insert Org == $SID\n";
	}
	unset($orgsLines);
	$connection->commit();
	$prepared_insert_oppf->close();
	$connection->query('ALTER TABLE tbl_OPPF ENGINE=INNODB');
	
	*/
	//STAR *************************************************
	/*echo "STAR\n";
	$prepared_insert_star = $connection->prepare("INSERT INTO tbl_STAR (SID) VALUES (?) ON DUPLICATE KEY UPDATE SID = ?");
	$prepared_insert_star ->bind_param("ss", $SID, $SID);
	
	$orgsFile = file_get_contents('./star');
	$orgsLines = explode("\n", $orgsFile);
	unset($orgsFile);
	
	//Insert
	for($i = 0; $i < sizeof($orgsLines); ++$i){
		$SID = $orgsLines[$i];
		if(!$prepared_insert_star->execute())echo "Failed to insert Org == $SID\n";
	}
	unset($orgsLines);
	$connection->commit();
	$prepared_insert_star->close();
	$connection->query('ALTER TABLE tbl_STAR ENGINE=INNODB');
	*/
	//Reddit ********************************************
	echo "Reddit\n";
	$prepared_insert_reddit = $connection->prepare("INSERT INTO tbl_Reddit (SID) VALUES (?) ON DUPLICATE KEY UPDATE SID = ?");
	$prepared_insert_reddit ->bind_param("ss", $SID, $SID);
	
	$orgsFile = file_get_contents('./reddit');
	$orgsLines = explode("\n", $orgsFile);
	unset($orgsFile);
	
	//Insert
	for($i = 0; $i < sizeof($orgsLines); ++$i){
		$SID = $orgsLines[$i];
		if(!$prepared_insert_reddit->execute())echo "Failed to insert Org == $SID\n";
	}
	unset($orgsLines);
	$connection->commit();
	$prepared_insert_reddit->close();
	$connection->query('ALTER TABLE tbl_Reddit ENGINE=INNODB');
	
	//Close Connection
	$connection->autocommit(TRUE);
	$connection->close();
?>
