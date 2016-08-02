<?php
	/*
	@license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
	
	Copyright (C) 2016 LucFauvel and SabineWren
	
	GNU AFFERO GENERAL PUBLIC LICENSE Version 3, 19 November 2007
	https://www.gnu.org/licenses/agpl-3.0.html
	
	@license-end

CAVEATS:
Activity (front end) == Focus (database)

SELECTION Query Types:

	Default:
	
	SELECT * FROM View_OrganizationsEverything WHERE ATTRIBUTE1 = ?1 OR Attribute1 = ?2 OR ... OR AttributeN = ?M LIMIT 10 OFFSET ?
	"s^{M}d", $value1, $value2, ..., $valueM, $pagenum
	//M 's's and 1 d
	
	If filter by Activity, then as above with additions:
	
	SELECT * FROM View_OrganizationsEverything WHERE ATTRIBUTE1 = ?1 OR Attribute1 = ?2 OR ... OR AttributeN = ?M
	<WHERE or AND> SID IN (
		SELECT SID FROM View_OrgsFilterPrimary
		WHERE Activity = ?a OR ... OR Activity = ?z
	)
	OR SID IN (
		SELECT SID from View_OrgsFilterSecondary
		WHERE Activity = ?a OR ... OR Activity = ?z
	)
	LIMIT 10 OFFSET ?;
	"s^{M+2*z}d", $value1, $value2, ..., $valueM, $valuea ... $valuez, $valuea ... $valuez, $pagenum
	//M+2*z 's's and 1 d
	*/
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
	$pagenum = $pagenum * 10;//number of results to skip
	
	$UseActivityFilter = false;
	$Activities = explode( ',', $_GET['Activity'] );
	if(strlen($Activities[0]) > 0)$UseActivityFilter = true;
	
	$Attributes = ['Archetype', 'Commitment', 'Recruiting', 'Roleplay'];
	
	//init loop
	$sql = "SELECT * FROM View_OrganizationsEverything";
	$parameters = array();
	$types = '';
	$conjunction = ' WHERE (';
	
	function addParamsToQuery($Attribute, $Values, &$types, &$sql, &$conjunction, &$parameters){
		foreach($Values as $Value){
			if($Value == '')continue;//We might have 0 Params to add
			$sql .= $conjunction . $Attribute . ' = ?';
			array_push($parameters, $Value);//need array of references
			$types .= 's';
			$conjunction = ' OR ';
		}
	}
	
	//dynamically add query restrictions	
	foreach($Attributes as $Attribute){
		$Values = explode( ',', $_GET[$Attribute] );
		addParamsToQuery($Attribute, $Values, $types, $sql, $conjunction, $parameters);
		$conjunction = ') AND (';
	}
	
	
	if($UseActivityFilter){
		//there could be other query restrictions
		if( sizeof($parameters) === 0)$sql .= ' WHERE ';
		else $sql .= ') AND ';
		
		$sql .= '( ';
			//add filter and join for primary focus (activity1)
			$sql .= 'SID IN (SELECT SID FROM View_OrgsFilterPrimary';
			$conjunction = ' WHERE ';
			addParamsToQuery('Focus', $Activities, $types, $sql, $conjunction, $parameters);
		
			//add filter and join for secondary focus (activity2)
			$sql .= ' ) OR SID IN (SELECT SID from View_OrgsFilterSecondary';
			$conjunction = ' WHERE ';
			addParamsToQuery('Focus', $Activities, $types, $sql, $conjunction, $parameters);
			$sql .= ') ';
		$sql .= ')';
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
