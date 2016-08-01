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
	
	$prepared_select = $connection->prepare("SELECT SID, Icon FROM tbl_Organizations");
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
		$fp = fopen( ('../org_icons/' . $x['SID']), 'w' );
		fwrite($fp, $image);
		fclose($fp);
	}
	
	$prepared_select->close();
	$connection->close();
?>
