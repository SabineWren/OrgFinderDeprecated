<?php
	/*
	@license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
	
	Copyright (C) 2016 LucFauvel and SabineWren
	
	GNU AFFERO GENERAL PUBLIC LICENSE Version 3, 19 November 2007
	https://www.gnu.org/licenses/agpl-3.0.html
	
	@license-end
	*/
	
	//password convenient because some security settings by default require a password
	$connection = new mysqli("localhost","publicselect","public", "cognitiondb");
	if( mysqli_connect_errno() ){
		die( "Connection failed: " . mysqli_connect_error() );
	}
	
	$prepared_select = $connection->prepare("SELECT Activity, Icon FROM tbl_Activities");
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
		$results[] = $x;
	}

	//return data via JSON
	$icons = null;
	foreach($results as $keyValue_ActivityIcon){
		$key = $keyValue_ActivityIcon['Activity'];
		$value = $keyValue_ActivityIcon['Icon'];
		$icons[$key] = $value;
	}
	
	echo json_encode($icons);
	
	$prepared_select->close();
	$connection->close();
?>
