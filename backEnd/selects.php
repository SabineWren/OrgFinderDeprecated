<?php
	/*
	@license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
	
	Copyright (C) 2016 LucFauvel and SabineWren
	
	GNU AFFERO GENERAL PUBLIC LICENSE Version 3, 19 November 2007
	https://www.gnu.org/licenses/agpl-3.0.html
	
	@license-end
	*/
	
	//THIS DOES NOT WORK WITH ACTIVITIES AND IT'S THE WRONG TABLE FOR FILTERING THEM ANYWAY
	$connection = new mysqli("192.168.0.105","publicselect","public", "cognitiondb");
	if( mysqli_connect_errno() ){
		die( "Connection failed: " . mysqli_connect_error() );
	}
	//echo("database host info: %s\n" . $connection->host_info);
	
	//get parameters from query string
	$pagenum = $_GET['pagenum'];
	if($pagenum < 0){
		exit("invalid page offset"); 
	}
	
	$Attributes = ['Archetype', 'Commitment', 'Recruiting', 'Roleplay'];
	
	//init loop
	$sql = "SELECT * FROM View_OrganizationsEverything";
	$parameters = array();
	$types = '';
	$or = ' WHERE ';
	
	//dynamically add select restrictions
	foreach($Attributes as $Attribute){
		$Values = explode( ',', $_GET[$Attribute] );
		foreach($Values as $Value){
			if($Value == '')continue;
			$sql .= $or . $Attribute . ' = ?';
			array_push($parameters, $Value);//need array of references
			$types .= 's';
			$or = ' OR ';
		}
	}
	
	//add offset
	$sql .= ' LIMIT 10 OFFSET ?';
	array_push($parameters, $pagenum);
	$types .= 'd';
	
	//require references to array elements to bind
	$bindParams = array();
	foreach($parameters as $key => $param){
		$bindParams[$key] = &$parameters[$key];
	}
	
	//amalgamate the types and values for callback function argument
	//we do not need references to the first element
	array_unshift($bindParams, $types);
	$prepared_select = $connection->prepare($sql);
	call_user_func_array( array($prepared_select, "bind_param"), $bindParams );
	//var_dump($sql, $types, $bindParams, $connection->error);
	//var_dump($prepared_select);
	
	//THIS WAS CODE WITHOUT FILTERING
	//$prepared_select->close();
	//$prepared_select = $connection->prepare("SELECT * FROM View_OrganizationsEverything LIMIT 10 OFFSET ?");
	//$prepared_select->bind_param("d", $pagenum);
	
	$pagenum = $pagenum * 10;//number of results to skip
	$prepared_select->execute();
	
	//parse data and create json using metadata
	$meta = $prepared_select->result_metadata();

	unset($parameters);
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

	echo json_encode($results);
	
	$prepared_select->close();
	$connection->close();

?>
