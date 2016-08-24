<?php
	/*
	@license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
	
	Copyright (C) 2016 SabineWren
	
	GNU AFFERO GENERAL PUBLIC LICENSE Version 3, 19 November 2007
	https://www.gnu.org/licenses/agpl-3.0.html
	
	@license-end
CAVEATS:
Activity (front end) == Focus (database)
Members  (front end) == Size  (database)
	*/
	
	mb_internal_encoding("UTF-8");
	
	$connection = new mysqli("localhost","publicselect","public", "cognitiondb");
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
	
	function addParamsToQuery($Attribute, $Values, &$types, &$sql, &$conjunction, &$parameters){
	if($Values[0] == '')return;//no params to add
		$conjunction .= '(';
		foreach($Values as $Value){
			$sql .= $conjunction . $Attribute . ' = ?';
			array_push($parameters, $Value);
			$types .= 's';
			$conjunction = ' OR ';
		}
		$sql .= ')';
		$conjunction = ' AND ';
	}
	
	//init
	$parameters = array();
	$types = '';
	$conjunction = ' WHERE ';
	
	$sql = "
SELECT orgs.SID as SID, orgs.Name as Name, orgs.Size as Size, orgs.Main as Main, orgs.GrowthRate as GrowthRate, orgs.CustomIcon as CustomIcon,
Performs.PrimaryFocus as PrimaryFocus, Performs.SecondaryFocus as SecondaryFocus, Commitment, Language, Archetype,
CASE
	WHEN RolePlayBool IS NOT NULL then 'Yes'
	ELSE 'No'
	END AS Roleplay,
CASE
	WHEN RecruitingBool IS NOT NULL then 'No'
	WHEN ExclBool IS NOT NULL then 'Excl.'
	ELSE 'Yes'
	END AS Recruiting
FROM (
	SELECT SID, Name, Size, Main, GrowthRate, CustomIcon, Roleplay.Organization as RolePlayBool, FullOrgs.Organization as RecruitingBool, ExclOrgs.Organization as ExclBool
	FROM tbl_Organizations orgs
	LEFT JOIN tbl_RolePlayOrgs   Roleplay  ON orgs.SID = Roleplay.Organization
	LEFT JOIN tbl_FullOrgs       FullOrgs  ON orgs.SID = FullOrgs.Organization
	LEFT JOIN tbl_ExclusiveOrgs  ExclOrgs  ON orgs.SID = ExclOrgs.Organization
";
	$Values = explode( ',', $_GET['Recruiting'] );
	if( isset($Values[0]) && $Values[0] != "" && !isset($Values[1]) ){
		if( $Values[0] == 'Yes' )     $sql .= "$conjunction(FullOrgs.Organization IS NULL)";
		else if( $Values[0] == 'No' ) $sql .= "$conjunction(FullOrgs.Organization IS NOT NULL)";
		$conjunction = ' AND ';
	}
	unset($Values);
	
	$Values = explode( ',', $_GET['Roleplay'] );
	if( isset($Values[0]) && $Values[0] != "" && !isset($Values[1]) ){
		if( $Values[0] == 'Yes' )     $sql .= "$conjunction(Roleplay.Organization IS NOT NULL)";
		else if( $Values[0] == 'No' ) $sql .= "$conjunction(Roleplay.Organization IS NULL)";
		$conjunction = ' AND ';
	}
	unset($Values);
	
	$lang = $_GET['Lang'];
	if($lang !== "Any"){
		$sql .= "$conjunction SID in (SELECT Organization FROM tbl_FilterFluencies WHERE Language = ?)";
		$conjunction = ' AND ';
		$types .= 's';
		array_push($parameters, $lang);
	}
	unset($lang);
	
	if(isset($_GET['Min'])){
		$min = (int)$_GET['Min'];
		$sql .= "$conjunction Size >= ?";
		$conjunction = ' AND ';
		$types .= 'd';
		array_push($parameters, $min);
		unset($min);
	}
	
	if(isset($_GET['Max'])){
		$max = (int)$_GET['Max'];
		$sql .= "$conjunction Size <= ?";
		$conjunction = ' AND ';
		$types .= 'd';
		array_push($parameters, $max);
		unset($max);
	}
	
	//Unions
	if((int)$_GET['Cog']){
		$sql .= "$conjunction SID IN (SELECT SID FROM tbl_RepresentsCog)";
		$conjunction = ' AND ';
	}
	if((int)$_GET['OPPF']){
		$sql .= "$conjunction SID IN (SELECT SID FROM tbl_OPPF)";
		$conjunction = ' AND ';
	}
	if((int)$_GET['STAR']){
		$sql .= "$conjunction SID IN (SELECT SID FROM tbl_STAR)";
		$conjunction = ' AND ';
	}
	
	//WHERE SID LIKE Value and subselect using Name
	$Values = explode( ',', $_GET['NameOrSID'] );
	if( strlen($Values[0]) > 0 ){
		$Value = '%' . rawurldecode( $Values[0]) . '%';//mysql->real_escape_string and html_entity_decode do not decode %20 (space)
		$temp = $Value . "\n" . $Values[0];
		$sql .= $conjunction . "(SID LIKE UPPER(?) OR SID IN (
			SELECT SID FROM tbl_Organizations WHERE Name LIKE UPPER(?)
		))";
		$Value = '%' . $Value . '%';
		array_push($parameters, $Value);
		array_push($parameters, $Value);
		$types .= 'ss';
		$conjunction = ' AND ';
	}
	unset($Values);
	
	//subselect to filter using Activity
	$Activities = explode( ',', $_GET['Activity'] );
	if(strlen($Activities[0]) > 0){		
		$sql .= $conjunction;
			//add filter and join for primary focus (activity1)
			$sql .= '(SID IN (SELECT Organization FROM tbl_PrimaryFocus';
			$conjunction = ' WHERE ';
			addParamsToQuery('PrimaryFocus', $Activities, $types, $sql, $conjunction, $parameters);
		
			//add filter and join for secondary focus (activity2)
			$sql .= ') OR SID IN (SELECT Organization from tbl_SecondaryFocus';
			$conjunction = ' WHERE ';
			addParamsToQuery('SecondaryFocus', $Activities, $types, $sql, $conjunction, $parameters);
		$sql .= ')) ';
	}
	unset($Activities);
	
	//subselect to filter using Commitment
	$Values = explode( ',', $_GET['Commitment'] );
	if(strlen($Values[0]) > 0){
		$sql .= $conjunction;
		$sql .= 'SID IN (
			SELECT Organization FROM tbl_Commits';
			$conjunction = ' WHERE ';
			addParamsToQuery('Commitment', $Values, $types, $sql, $conjunction, $parameters);
			$sql .= ') ';
	}
	unset($Values);
	
	//subselect to filter using Archetype
	$Values = explode( ',', $_GET['Archetype'] );
	if(strlen($Values[0]) > 0){
		$sql .= $conjunction;
		$sql .= 'SID IN (
			SELECT Organization FROM tbl_FilterArchetypes';
			$conjunction = ' WHERE ';
			addParamsToQuery('Archetype', $Values, $types, $sql, $conjunction, $parameters);
			$sql .= ') ';
	}
	unset($Values);
	
	//apply sorting
	if( isset($_GET['Growth']) ){
		$growthDir = $_GET['Growth'];
		if($growthDir == 'down')    $sql .= " ORDER BY GrowthRate DESC";
		else if($growthDir == 'up') $sql .= " ORDER BY GrowthRate ASC";
		unset($growthDir);
	}
	else if( isset($_GET['nameDir']) ){
		$nameDir = $_GET['nameDir'];
		if($nameDir == 'down')    $sql .= " ORDER BY Name DESC";
		else if($nameDir == 'up') $sql .= " ORDER BY Name ASC";
		unset($nameDir);
	}
	else if( isset($_GET['sizeDir']) ){
		$sizeDir = $_GET['sizeDir'];
		if($sizeDir == 'down')    $sql .= " ORDER BY Size DESC";
		else if($sizeDir == 'up') $sql .= " ORDER BY Size ASC";
		unset($sizeDir);
	}
	else if( isset($_GET['mainDir']) ){
		$mainDir = $_GET['mainDir'];
		if($mainDir == 'down')    $sql .= " ORDER BY Main DESC";
		else if($mainDir == 'up') $sql .= " ORDER BY Main ASC";
		unset($mainDir);
	}

	//we use a bound param so guarantee at least one param in our statement (otherwise the function call breaks)
	$sql .= " LIMIT $pageSize OFFSET ?";
	array_push($parameters, $offset);
	$types .= 'd';

$sql .=  ") as orgs
LEFT JOIN tbl_Performs       Performs  ON orgs.SID = Performs.Organization
     JOIN tbl_Commits        Commits   ON orgs.SID = Commits.Organization
LEFT JOIN tbl_OrgFluencies   Language  ON orgs.SID = Language.Organization
LEFT JOIN tbl_OrgArchetypes  Archetype ON orgs.SID = Archetype.Organization
	";
	
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
	unset($parameters);
	
	$meta = $prepared_select->result_metadata();
	
	while ($field = $meta->fetch_field()) {
		$parameters[] = &$rowKeyValue[$field->name];
	}
	
	call_user_func_array(array($prepared_select, 'bind_result'), $parameters);
	
	//fetch results into $parameters, which references the values of $resultsKeyValue
	while ($prepared_select->fetch()) {
		//copy the resulting row one attribute at a time
		//we use a loop because the contents are references
		foreach($rowKeyValue as $key => $val) {
			$x[$key] = $val;
		}
		$results[] = $x;//save the row
	}
	
	$prepared_select->close();
	$connection->close();
	if(isset($results))echo json_encode($results);//, JSON_HEX_APOS|JSON_HEX_QUOT
	else echo "null";
?>

