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
	 
	ini_set('default_charset', 'UTF-8');
	
	if( sizeof($argv) < 3){
		echo "Correct usage: php $argv[0] <db username> <db password>\n";
		exit();
	}

	//1) Connect to DB
	$connection = new mysqli("192.168.0.105",$argv[1],$argv[2], "cognitiondb");
	if( mysqli_connect_errno() ){
		die( "Connection failed: " . mysqli_connect_error() );
	}
	
	//2) Prepare statement
	$prepared_insert_language = $connection->prepare("INSERT INTO tbl_OrgFluencies(Organization, Language) VALUES (?, ?) ON DUPLICATE KEY UPDATE Language = ?");
	$prepared_insert_language->bind_param("sss", $SID, $Language, $Language);
	
	$numberInserted = 0;
	for($x = 1; ; $x++){
		//3) Query SC-API (all orgs)
		$lines = file_get_contents(
			"http://sc-api.com/?api_source=live&system=organizations&action=all_organizations&source=rsi&start_page=$x&end_page=$x&items_per_page=1&sort_method=&sort_direction=ascending&expedite=0&format=raw"
		);    
		$dataArray = json_decode($lines, true);//json to php associated array
		if($dataArray == false)exit("failed to decode\n");
		unset($lines);
		
		if($dataArray["data"] == null)break;//if we have read all orgs
		
		foreach ($dataArray["data"] as $org){
			//4) Sub-query Org (more data)
			
			$failCounter = 0;
			for(;;){//loop in case request fails due to poor connection
				$subquery = file_get_contents(
					'http://sc-api.com/?api_source=cache&system=organizations&action=single_organization&target_id='
					. $org['sid'] . '&expedite=0&format=raw'
				);
				if($subquery)break;
				$failCounter++;
				sleep(1);
				if($failCounter > 2){
					echo "FAILED to query API cache for org with SID == " . $org['sid'] . "\n";
					continue(2);
				}
			}
			$orgArray = json_decode($subquery, true);
			unset($subquery);
			if($orgArray['data'] == null)echo "WARNING: Org null (in API cache result!)\n";
			
			//5) Bind data to statement
			$SID         = strtoupper( $orgArray['data']['sid'] );
			$Language    = html_entity_decode(  $orgArray['data']['lang']  );
			unset($orgArray);
			//echo "Language: " . $Language . "\n";

			//6) Execute Database Query		
			if(!$prepared_insert_language->execute())echo "Error inserting language $SID $Language\n";
			++$numberInserted;
		}
		echo "Inserted $numberInserted Orgs\n";
	}
	//7) Sort Tuples
	//ALTER TABLE tbl_OrgFluencies ENGINE=INNODB;
	
	//8) Close Connection
	$prepared_insert_language->close();
	
	$connection->close();
?>
