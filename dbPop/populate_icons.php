<?php
	/*
	@license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
	
	Copyright (C) 2016 LucFauvel and SabineWren
	
	GNU AFFERO GENERAL PUBLIC LICENSE Version 3, 19 November 2007
	https://www.gnu.org/licenses/agpl-3.0.html
	
	@license-end
	*/
	 
	ini_set('default_charset', 'UTF-8');

	//1) Connect to DB
	//password convenient because some security settings by default require a password
	$connection = new mysqli("192.168.0.105","publicselect","public", "cognitiondb");
	if( mysqli_connect_errno() ){
		die( "Connection failed: " . mysqli_connect_error() );
	}
	
	$prepared_select = $connection->prepare("SELECT SID, Icon FROM tbl_Organizations LIMIT 3670, 40000");//remove offset after next update
	$prepared_select->execute();
	
	//parse data and create json using metadata
	$meta = $prepared_select->result_metadata();

	while ($field = $meta->fetch_field()) {
		$parameters[] = &$row[$field->name];
	}

	call_user_func_array(array($prepared_select, 'bind_result'), $parameters);
	while ($prepared_select->fetch()) {
		foreach($row as $key => $val) {
			$x[$key] = $val;
		}
		//$SID = $x['SID'];
		//$URL = $x['Icon'];
		//echo $SID . "\n";
		//echo $URL . "\n\n";
		
		$image = file_get_contents($x['Icon']);
		//TODO add proper error control and error logging for when the URL is dead
		//may need a way to re-query and re-insert URLs for orgs that have changed their URL since last scrape
		//the complication is INSERT and REPLACE require a password, but this script does not because it only uses SELECT
		//example dead link:	http://robertsspaceindustries.com/media/t713kgg9mniiar/logo/PHG-Logo.jpg
		$fp = fopen( ('/media/usb_mysql/org_icons/' . $x['SID']), 'w' );
		fwrite($fp, $image);
		fclose($fp);
	}
	
	$prepared_select->close();
	$connection->close();
?>
