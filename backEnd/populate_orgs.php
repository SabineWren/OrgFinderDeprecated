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
	*			6) Execute replaceion
	* 7) Sort Tuples
	* 8) Close connection
	*/
	
	/* Known problems:
	 * The public account currently has insert and update access to the db
	 */
	 
	ini_set('default_charset', 'UTF-8');

	//1) Connect to DB
	//password convenient because some security settings by default require a password
	$connection = new mysqli("192.168.0.105","publicselect","public", "cognitiondb");
	if( mysqli_connect_errno() ){
		die( "Connection failed: " . mysqli_connect_error() );
	}
	
	//2) Prepare statements
	$prepared_replace_org = $connection->prepare("REPLACE INTO tbl_Organizations (SID, Name, Icon) VALUES (?, ?, ?)");
	$prepared_replace_org->bind_param("sss", $SID, $Name, $Icon);
	
	$prepared_replace_size = $connection->prepare("REPLACE INTO tbl_OrgSize (Organization, MemberCount) VALUES (?, ?)");
	$prepared_replace_size->bind_param("sd", $SID, $MemberCount);
	
	$prepared_replace_commits = $connection->prepare("INSERT INTO tbl_Commits(Organization, Commitment) VALUES (?, ?)");
	$prepared_replace_commits->bind_param("ss", $SID, $Commitment);

	for($x = 1; $x <= 1; $x++){
		//3) Query SC-API (all orgs)
		$lines = file_get_contents(
			'http://sc-api.com/?api_source=live&system=organizations&action=all_organizations&source=rsi&start_page='
			. $x . '&end_page=' . $x . 
			'&items_per_page=1&sort_method=&sort_direction=ascending&expedite=0&format=raw'
		);    
		$dataArray = json_decode($lines, true);//convert json object to php associative array
		if($dataArray == false)exit("failed to decode\n");
		unset($lines);
		
		foreach ($dataArray["data"] as $org){
			//4) Sub-query Org (more data)
			$subquery = file_get_contents(
				'http://sc-api.com/?api_source=live&system=organizations&action=single_organization&target_id='
				. $org['sid'] . '&expedite=0&format=raw'
			);
			$orgArray = json_decode($subquery, true);
			unset($subquery);
			
			//5) Bind data to statement
			$SID         = strtoupper( $orgArray['data']['sid'] );
			$Name        = html_entity_decode(  $orgArray['data']['title']  );
			$Icon        = $orgArray['data']['logo'];
			$MemberCount = intval( $orgArray['data']['member_count'] );
//			$recruiting
			//archetype
			$Commitment = $orgArray['data']['commitment'];
			//roleplay
			//lang
			//primary_focus
			//secondary_focus
			//banner
			//headline
			//history
			//manifesto
			//charter
			

			//test code
			echo "SID: " . $SID . "\n";
			echo "Name: " . $Name . "\n";
			//echo "$Icon \n";
			echo "Members: " . $MemberCount . "\n";
			echo "Commitment: " . $Commitment . "\n";
			echo "\n";

			//6) Execute replaceion
			$prepared_replace_org->execute();
			$prepared_replace_size->execute();
			$prepared_replace_commits->execute();
		}
	}
	//7) Sort Tuples
	/*
	ALTER TABLE tbl_Countries ENGINE=INNODB;
	ALTER TABLE tbl_Organizations ENGINE=INNODB;
	ALTER TABLE tbl_Commitments ENGINE=INNODB;
	ALTER TABLE tbl_Commits ENGINE=INNODB;
	ALTER TABLE tbl_RolePlayOrgs ENGINE=INNODB;
	ALTER TABLE tbl_Archetypes ENGINE=INNODB;
	ALTER TABLE tbl_OrgArchetypes ENGINE=INNODB;
	ALTER TABLE tbl_FullOrgs ENGINE=INNODB;
	ALTER TABLE tbl_ExclusiveOrgs ENGINE=INNODB;
	ALTER TABLE tbl_Fluencies ENGINE=INNODB;
	ALTER TABLE tbl_OrgFluencies ENGINE=INNODB;
	*/
	
	//8) Close Connection
	$prepared_replace_org->close();
	$prepared_replace_size->close();
	$prepared_replace_commits->close();
	$connection->close();
?>
