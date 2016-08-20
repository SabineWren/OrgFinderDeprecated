<?php
	/*
	@license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
	
	Copyright (C) 2016 SabineWren
	
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
	
	$PathToImages = "/media/usb_mysql/org_icons/";
	$rows = $connection->query("SELECT Organization, Icon FROM tbl_IconURLs");
	if(!$rows)die('Failed to SELECT from database');
	
	while ($row = $rows->fetch_assoc()) {
		$SID     = $row['Organization'];
		$IconURL = $row['Icon'];
		
		//download image
		if(  !file_exists( $PathToImages . $SID )  ){
			$image = file_get_contents($IconURL);
			if($image === FALSE){
				//Possibly a dead URL
				echo "Failed to download image for SID = $SID\n";
				continue;
			}
				//example of dead link:
				//http://robertsspaceindustries.com/media/t713kgg9mniiar/logo/PHG-Logo.jpg

			$fp = fopen( ( $PathToImages . $SID ), 'w' );
			fwrite($fp, $image);
			fclose($fp);
		}
	}
	
	$prepared_update->close();
	$connection->close();
?>
