<?php
	/*
	@license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
	
	Copyright (C) 2016 SabineWren
	
	GNU AFFERO GENERAL PUBLIC LICENSE Version 3, 19 November 2007
	https://www.gnu.org/licenses/agpl-3.0.html
	
	@license-end
	*/
	
	if( sizeof($argv) < 4){
		echo "Correct usage: php " . $argv[0] . " <db username> <db password> <path to save icons (no trailing slash)>\n";
		exit();
	}
	
	$connection = new mysqli("localhost",$argv[1],$argv[2], "cognitiondb");
	if( mysqli_connect_errno() ){
		die( "Connection failed: " . mysqli_connect_error() );
	}
	
	$PathToImages = $argv[3] . '/';
	
	$rows = $connection->query("SELECT SID, CustomIcon FROM tbl_Organizations");
	if(!$rows)die('Failed to SELECT from database');
	$orgs_with_icons = array();
	while ($row = $rows->fetch_assoc()) {
		if( !$row['CustomIcon'] )$orgs_with_icons []= $row['SID'];
	}
	unset($rows);
	
	foreach($orgs_with_icons as $SID) {
	
		if( file_exists( $PathToImages . $SID )  ){
			unlink ( $PathToImages . $SID );
		}
	}
	
	$connection->close();
?>
