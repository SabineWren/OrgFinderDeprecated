<?php
	/*
	@license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
	
	Copyright (C) 2016 SabineWren
	
	GNU AFFERO GENERAL PUBLIC LICENSE Version 3, 19 November 2007
	https://www.gnu.org/licenses/agpl-3.0.html
	
	@license-end
	*/
	
	if( sizeof($argv) < 3){
		echo "Correct usage: php " . $argv[0] . " <db username> <db password>\n";
		exit();
	}
	
	$connection = new mysqli("localhost",$argv[1],$argv[2], "cognitiondb");
	if( mysqli_connect_errno() ){
		die( "Connection failed: " . mysqli_connect_error() );
	}
	
	$rows = $connection->query("SELECT SID, CustomIcon FROM tbl_Organizations");
	if(!$rows)die('Failed to SELECT from database');
	$orgs_with_icons = array();
	while ($row = $rows->fetch_assoc()) {
		if( $row['CustomIcon'] )$orgs_with_icons []= $row['SID'];
	}
	unset($rows);
	
	$get_url = $connection->prepare("SELECT Icon FROM tbl_IconURLs WHERE Organization = ?");
	$get_url ->bind_param("s", $SID);
	
	foreach($orgs_with_icons as $SID) {
	
		if( !$get_url->execute() ){
			echo "failed to query for SID = $SID\n";
			echo $connection->error . "\n";
			continue;
		};
		
		$get_url->bind_result($IconURL);
		$get_url->fetch();
		
		//download image
		if(  !file_exists( __dir__ . '/../../org_icons_fullsize/' . $SID )  ){
			$image = file_get_contents($IconURL);
			if($image === FALSE){
				//Possibly a dead URL
				echo "Failed to download image for SID = $SID\n";
				continue;
			}
				//example of dead link:
				//http://robertsspaceindustries.com/media/t713kgg9mniiar/logo/PHG-Logo.jpg

			$fp = fopen( ( __dir__ . '/../../org_icons_new/' . $SID ), 'w' );
			fwrite($fp, $image);
			echo "saved icon for SID = $SID\n";
			fclose($fp);
		}
	}
	
	$connection->close();
?>
