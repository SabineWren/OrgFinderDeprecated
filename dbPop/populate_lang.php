<?php
	/*
	@license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
	
	Copyright (C) 2016 LucFauvel and SabineWren
	
	GNU AFFERO GENERAL PUBLIC LICENSE Version 3, 19 November 2007
	https://www.gnu.org/licenses/agpl-3.0.html
	
	@license-end
	
	* @Description:
	* 1) Connect to DB
	* 2) Prepare statements
	* OUTER LOOP:
	*		3) Query SC-API (all orgs)
	*		INNER LOOP:
	*			4) Sub-query Org (more data)
	*			5) Bind data to statement
	*			6) Execute Database Transactions
	* 7) Sort Tuples
	* 8) Close connection
	*/
	mb_internal_encoding("UTF-8");
	
	if( sizeof($argv) < 3){
		echo "Correct usage: php $argv[0] <db username> <db password>\n";
		exit();
	}

	//1) Connect to DB
	$connection = new mysqli("192.168.0.105",$argv[1],$argv[2], "cognitiondb");
	if( mysqli_connect_errno() ){
		die( "Connection failed: " . mysqli_connect_error() );
	}
	if( !$connection->set_charset("utf8") )echo "Error changing connection character set\n";
	
	//2) Prepare statement
	$prepared_insert_language = $connection->prepare("INSERT INTO tbl_OrgFluencies(Organization, Language) VALUES (?, ?) ON DUPLICATE KEY UPDATE Language = ?");
	$prepared_insert_filter = $connection->prepare("INSERT INTO tbl_FilterFluencies(Language, Organization) VALUES (?, ?) ON DUPLICATE KEY UPDATE Language = ?");
	$prepared_insert_language->bind_param("sss", $SID, $Language, $Language);
	$prepared_insert_filter->bind_param("sss", $Language, $SID, $Language);
	
	$rows = $connection->query("SELECT SID FROM tbl_Organizations");
	$connection->commit();//might not be needed
	
	function doesLangExist(&$SID, &$connection){
		$rows = $connection->query(
			"SELECT a.SID as SID
			FROM tbl_Organizations a
			LEFT JOIN tbl_OrgFluencies b ON a.SID = b.Organization
			WHERE b.Language is NULL"
		);
		$connection->commit();//might not be needed
	
	for($org = $rows->fetch_assoc(); $org != null; $org = $rows->fetch_assoc()){
			//4) Sub-query Org (more data)
		
		$failCounter = 0;
		for(;;){//loop in case request fails due to poor connection
			$subquery = file_get_contents(//can only get language info from cache
				'http://sc-api.com/?api_source=cache&system=organizations&action=single_organization&target_id='
				. $org['SID'] . '&expedite=0&format=raw'
			);
			if($subquery)break;
			$failCounter++;
			sleep(1);
			if($failCounter > 2){
				echo "FAILED to query API cache for org with SID == " . $org['SID'] . "\n";
				continue(2);
			}
		}
		$orgArray = json_decode($subquery, true);
		unset($subquery);
		if($orgArray['data'] == null)echo "WARNING: Org null (in API cache result!)\n";
	
		//5) Bind data to statement
		$SID         = strtoupper( $orgArray['data']['sid'] );
		$Language    = $orgArray['data']['lang'];
		unset($orgArray);
		//echo "Language: " . $Language . "\n";

		//6) Execute Database Query
		if(!$prepared_insert_language->execute())echo "Error inserting language $SID $Language\n";
		if(!$prepared_insert_filter->execute())echo "Error inserting filter $SID $Language\n";
	}
	
	//7) Sort Tuples
	$connection->query('ALTER TABLE tbl_OrgFluencies ENGINE=INNODB');//recluster tables
	$connection->query('ALTER TABLE tbl_FilterFluencies ENGINE=INNODB');
	
	//8) Close Connection
	$prepared_insert_language->close();
	$prepared_insert_filter->close();
	
	$connection->close();
?>
