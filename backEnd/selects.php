<?php
	/*
	@license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
	
	Copyright (C) 2016 LucFauvel and SabineWren
	
	GNU AFFERO GENERAL PUBLIC LICENSE Version 3, 19 November 2007
	https://www.gnu.org/licenses/agpl-3.0.html
	
	@license-end
CAVEATS:
Activity (front end) == Focus (database)
Members  (front end) == Size  (database)
	*/
	
	mb_internal_encoding("UTF-8");
	
	$connection = new mysqli("192.168.0.105","publicselect","public", "cognitiondb2");
	if( mysqli_connect_errno() ){
		die( "Connection failed: " . mysqli_connect_error() );
	}
	
	if( !$connection->set_charset("utf8") )echo "Error changing connection character set\n";
	
	//get parameters from query string
	$pageNum = (int)$_GET['pagenum'];
	if($pageNum < 0){
		exit("invalid page offset"); 
	}
	$pageSize = 12;
	$offset = $pageNum * $pageSize;
	
	//init loop
	$sql = "SELECT * FROM View_OrganizationsEverything";
	$parameters = array();
	$types = '';
	$conjunction = ' WHERE (';
	
	function addParamsToQuery($Attribute, $Values, &$types, &$sql, &$conjunction, &$parameters){
		foreach($Values as $Value){
			if($Value == '')continue;//We might have 0 Params to add
			$sql .= $conjunction . $Attribute . ' = ?';
			array_push($parameters, $Value);
			$types .= 's';
			$conjunction = ' OR ';
		}
		if($conjunction === ' OR ')$conjunction = ' AND (';
	}
	
	$Values = explode( ',', $_GET['Commitment'] );
	addParamsToQuery('Commitment', $Values, $types, $sql, $conjunction, $parameters);
	if(strlen($Values[0]) > 0)$sql .= ')';
	unset($Values);
	
	$Values = explode( ',', $_GET['Recruiting'] );
	addParamsToQuery('Recruiting', $Values, $types, $sql, $conjunction, $parameters);
	if(strlen($Values[0]) > 0)$sql .= ')';
	unset($Values);
	
	$Values = explode( ',', $_GET['Roleplay'] );
	addParamsToQuery('Roleplay', $Values, $types, $sql, $conjunction, $parameters);
	if(strlen($Values[0]) > 0)$sql .= ')';
	unset($Values);
	
	$lang = $_GET['Lang'];
	if($lang !== "Any"){
		$sql .= "$conjunction SID in (SELECT Organization FROM tbl_FilterFluencies WHERE Language = ?))";
		$conjunction = ' AND (';
		$types .= 's';
		array_push($parameters, $lang);
	}
	unset($lang);
	
	if(isset($_GET['Min'])){
		$min = (int)$_GET['Min'];
		$sql .= "$conjunction Size >= ?)";
		$conjunction = ' AND(';
		$types .= 'd';
		array_push($parameters, $min);
		unset($min);
	}
	
	if(isset($_GET['Max'])){
		$max = (int)$_GET['Max'];
		$sql .= "$conjunction Size <= ?)";
		$conjunction = ' AND(';
		$types .= 'd';
		array_push($parameters, $max);
		unset($max);
	}
	
	//if org in Cognition Corp
	if((int)$_GET['Cog']){
		$sql .= "$conjunction SID IN (SELECT SID FROM tbl_RepresentsCog))";
		$conjunction = ' AND(';
	}
	
	//WHERE SID LIKE Value and subselect using Name
	$Values = explode( ',', $_GET['NameOrSID'] );
	if( strlen($Values[0]) > 0 ){
		$Value = '%' . rawurldecode( $Values[0]) . '%';//mysql->real_escape_string and html_entity_decode do not decode %20 (space)
		$temp = $Value . "\n" . $Values[0];
		$sql .= $conjunction . "SID LIKE UPPER(?) OR SID IN (
			SELECT SID FROM tbl_Organizations WHERE Name LIKE UPPER(?)
		))";
		array_push($parameters, $Value);
		array_push($parameters, $Value);
		$types .= 'ss';
		$conjunction = ' AND (';
	}
	unset($Values);
	
	//subselect to filter using Activity
	$Activities = explode( ',', $_GET['Activity'] );
	if(strlen($Activities[0]) > 0){		
		$sql .= $conjunction;
			//add filter and join for primary focus (activity1)
			$sql .= 'SID IN (SELECT Organization FROM tbl_PrimaryFocus';
			$conjunction = ' WHERE ';
			addParamsToQuery('PrimaryFocus', $Activities, $types, $sql, $conjunction, $parameters);
		
			//add filter and join for secondary focus (activity2)
			$sql .= ') OR SID IN (SELECT Organization from tbl_SecondaryFocus';
			$conjunction = ' WHERE ';
			addParamsToQuery('SecondaryFocus', $Activities, $types, $sql, $conjunction, $parameters);
		$sql .= ')) ';
	}
	unset($Activities);
	
	//subselect to filter using Archetype
	$Archetypes = explode( ',', $_GET['Archetype'] );
	if(strlen($Archetypes[0]) > 0){
		$sql .= $conjunction;
		
		//add filter and join for primary focus (activity1)
		$sql .= 'SID IN (
			SELECT Organization FROM tbl_FilterArchetypes';
			$conjunction = ' WHERE ';
			addParamsToQuery('Archetype', $Archetypes, $types, $sql, $conjunction, $parameters);
		$sql .= '))';
	}
	unset($Archetypes);
	
	//apply sorting
	if( isset($_GET['nameDir']) ){
		$nameDir = $_GET['nameDir'];
		//for some reason 'Name' has to be in quotes for DESC to work, but ASC works without!?!?
		if($nameDir == 'down')    $sql .= " ORDER BY Name DESC";
		else if($nameDir == 'up') $sql .= " ORDER BY Name ASC";
		unset($nameDir);
	}
	
	if( isset($_GET['sizeDir']) ){
		$sizeDir = $_GET['sizeDir'];
		if($sizeDir == 'down')    $sql .= " ORDER BY Size DESC";
		else if($sizeDir == 'up') $sql .= " ORDER BY Size ASC";
		unset($sizeDir);
	}
	
	//we use a bound param so guarantee at least one param in our statement (otherwise the function call breaks)
	$sql .= " LIMIT $pageSize OFFSET ?";
	array_push($parameters, $offset);
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
	/*$fp = fopen('debug', 'a');
	fwrite($fp, $connection->error . "\n\n" . $sql . "\n\n" . implode(' ', $bindParams));
	fclose($fp);*/
	//var_dump($sql, $types, $bindParams);
	//var_dump($connection->error);
	
	$prepared_select->execute();
	
	//parse data and create json using metadata
	$meta = $prepared_select->result_metadata();
	//var_dump($meta);
	unset($parameters);
	while ($field = $meta->fetch_field()) {
		$parameters[] = &$row[$field->name];
	}
	
	call_user_func_array(array($prepared_select, 'bind_result'), $parameters);
	while ($prepared_select->fetch()) {
		foreach($row as $key => $val) {
			//var_dump($key);
			//var_dump($val);
			//if($key == "Name")$x[$key] = htmlentities($val);
			$x[$key] = $val;
			//$x[$key] = $val;
			//var_dump($x[$key]);
			//echo "\n";
		}
		$results[] = $x;
	}
	
	$prepared_select->close();
	$connection->close();
	if(isset($results))echo json_encode($results);//, JSON_HEX_APOS|JSON_HEX_QUOT
	else echo "null";
?>
