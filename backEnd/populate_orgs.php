<?php
	/*
	@license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
	
	Copyright (C) 2016 LucFauvel and SabineWren
	
	GNU AFFERO GENERAL PUBLIC LICENSE Version 3, 19 November 2007
	https://www.gnu.org/licenses/agpl-3.0.html
	
	@license-end
	
	* @Description:
	* 1) Connect to DB
	* 2) Prepare statement
	* OUTER LOOP:
	*		3) Query SC-API
	*		INNER LOOP:
	*			4) Bind data to statement
	*			5) Execute replaceion
	* 6) Close connection
	*/
	
	/* Known problems:
	 * The sc-api does not store special characters correctly; a possibly fix is to create our own scraper
	 * The public account currently has insert and update access to the db
	 */

	//1) Connect to DB
	//password convenient because some security settings by default require a password
	$connection = new mysqli("192.168.0.105","publicselect","public", "cognitiondb");
	if( mysqli_connect_errno() ){
		die( "Connection failed: " . mysqli_connect_error() );
	}
	
	//2) Prepare statement
	$prepared_replace_org = $connection->prepare("REPLACE INTO tbl_Organizations (SID, Name, Icon) VALUES (?, ?, ?)");
	$prepared_replace_org->bind_param("sss", $SID, $Name, $Icon);
	
	$prepared_replace_size = $connection->prepare("REPLACE INTO tbl_OrgSize (Organization, MemberCount) VALUES (?, ?)");
	$prepared_replace_size->bind_param("sd", $SID, $MemberCount);

	for($x = 1; $x <= 2; $x++){
		//3) Query SC-API
		$lines = file_get_contents(
			'http://sc-api.com/?api_source=live&system=organizations&action=all_organizations&source=rsi&start_page='
			. $x . '&end_page=' . $x . 
			'&items_per_page=1&sort_method=&sort_direction=ascending&expedite=0&format=pretty_json'
		);    
		$dataArray = json_decode($lines, true);//convert json object to php associative array
		if($dataArray == false)exit("failed to decode\n");

		foreach ($dataArray["data"] as $org){
			//4) Bind data to statement
			$SID = strtoupper( $org['sid'] );
			$MemberCount = intval( $org['member_count'] );
			$Name = $org['title'];
			$Icon = $org['logo'];

			/* test code*/
			echo "SID: " . $SID . "\n";
			echo "Members: " . $MemberCount . "\n";
			echo "Name: " . $Name . "\n";
			echo "$Icon \n";
			echo "\n";

			//5) Execute replaceion
			$prepared_replace_org->execute();
			$prepared_replace_size->execute();
		}
		
		//unset($lines);
	}
	
	//6) Close Connection
	$prepared_replace_org->close();
	$prepared_replace_size->close();
	$connection->close();
?>
