<?php
	/*
	@license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
	
	Copyright (C) 2016 LucFauvel and SabineWren
	
	GNU AFFERO GENERAL PUBLIC LICENSE Version 3, 19 November 2007
	https://www.gnu.org/licenses/agpl-3.0.html
	
	@license-end
	*/
	
	//Connect to DB
	if( sizeof($argv) < 3){
		echo "Correct usage: php " . $argv[0] . " <db username> <db password>\n";
		exit();
	}
	
	$connection = new mysqli("192.168.0.105",$argv[1],$argv[2], "cognitiondb");
	if( mysqli_connect_errno() ){
		die( "Connection failed: " . mysqli_connect_error() );
	}
	
	$prepared_update = $connection->prepare("UPDATE tbl_Organizations SET Icon = ? WHERE SID = ?");
	$prepared_update->bind_param("ss", $Icon, $SID);
	
	//get images
	$rows = $connection->query("SELECT SID, Icon FROM tbl_Organizations");
	if(!$rows)die('Failed to SELECT from database');
	
	//parse data as an associative array
	while ($row = $rows->fetch_assoc()) {
		$SID = $row['SID'];
		$Icon = $row['Icon'];
		
		//download image
		$image = file_get_contents($Icon);
		if($image === FALSE){
			//Our URL may be out of date, so let's get a new one
			$apiQuery = file_get_contents("http://sc-api.com/?api_source=live&system=organizations&action=single_organization&target_id=$SID&expedite=0&format=raw");
			if(!$apiQuery){
				echo "Failed to query API for SID = $SID\n";
				unset($apiQuery);
				continue;
			}
			$orgArray = json_decode($apiQuery, true);
			unset($apiQuery);
			if($orgArray['data'] == null){
				echo "API returned null for SID = $SID (org may no longer exist)\n";
				unset($orgArray);
				continue;
			}
			$Icon = $orgArray['Icon'];
			unset($orgArray);
			//update database URL
			if( $Icon == null || !$prepared_update->execute() ){
				echo "Failed to UPDATE database for SID = $SID\n";
				continue;
			}
			//try again
			$image = file_get_contents($Icon);
			if($image === FALSE){
				echo "Unable to retrieve Icon for SID = $SID\n";
				continue;
			}
			//example of dead link:
			//http://robertsspaceindustries.com/media/t713kgg9mniiar/logo/PHG-Logo.jpg
		}
		
		$fp = fopen( "/media/usb_mysql/org_icons/$SID", 'w' );
		//$fp = fopen( "./org_icons/$SID", 'w' );
		fwrite($fp, $image);
		fclose($fp);
	}
	
	$prepared_update->close();
	$connection->close();
?>
