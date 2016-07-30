<?php
	/*
	@license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
	
	Copyright (C) 2016 LucFauvel and SabineWren
	
	GNU AFFERO GENERAL PUBLIC LICENSE Version 3, 19 November 2007
	https://www.gnu.org/licenses/agpl-3.0.html
	
	@license-end
	*/
	
	$pagenum = $_GET['pagenum'];
	if($pagenum < 0){
		exit("invalid page offset"); 
	}
	
	//account publicselect has no permissions except 'read'
	//password convenient because some security settings by default require a password
	$connection = new mysqli("192.168.0.105","publicselect","public", "cognitiondb");
	if( mysqli_connect_errno() ){
		die( "Connection failed: " . mysqli_connect_error() );
	}
	
	//echo("database host info: %s\n" . $connection->host_info);
	
	$prepared_select = $connection->prepare("SELECT * FROM tbl_Organizations LIMIT 10 OFFSET ?");
	$prepared_select->bind_param("d", $pagenum);

	$pagenum = $pagenum * 10;
	$prepared_select->execute();
	
	/*
	//output plain text
	$sid = "";
	$name = "";
	$icon = "";
	$prepared_select->bind_result($sid, $name, $icon);
	while ($prepared_select->fetch()) {
		echo $sid  . "\n";
		echo $name . "\n";
		echo $icon . "\n";
	}*/
	
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
	echo json_encode($results);
	
	$prepared_select->close();
	$connection->close();
?>
