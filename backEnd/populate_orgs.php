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
	
	/* tofix:
	 * The public account currently has insert and update access to the db
	 * change ER diagram -- FullOrganizations to FullOrgs
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
	$prepared_replace_org ->bind_param("sss", $SID, $Name, $Icon);
	
	$prepared_replace_size = $connection->prepare("REPLACE INTO tbl_OrgSize (Organization, MemberCount) VALUES (?, ?)");
	$prepared_replace_size ->bind_param("sd", $SID, $MemberCount);
	
	$prepared_replace_commits = $connection->prepare("REPLACE INTO tbl_Commits(Organization, Commitment) VALUES (?, ?)");
	$prepared_replace_commits ->bind_param("ss", $SID, $Commitment);
	
	$prepared_insert_full = $connection->prepare("INSERT INTO tbl_FullOrgs(Organization) VALUES (?)");
	$prepared_delete_full = $connection->prepare("DELETE from tbl_FullOrgs WHERE Organization = ?");
	$prepared_insert_full ->bind_param("s", $SID);
	$prepared_delete_full ->bind_param("s", $SID);
	
	$prepared_replace_primary = $connection->prepare("REPLACE INTO tbl_PrimaryFocus(PrimaryFocus, Organization) VALUES (?, ?)");
	$prepared_replace_secondary = $connection->prepare("REPLACE INTO tbl_SecondaryFocus(SecondaryFocus, Organization) VALUES (?, ?)");
	$prepared_replace_performs = $connection->prepare("REPLACE INTO tbl_Performs(PrimaryFocus, SecondaryFocus, Organization) VALUES (?, ?, ?)");
	$prepared_replace_primary->bind_param("ss", $PrimaryFocus, $SID);
	$prepared_replace_secondary->bind_param("ss", $SecondaryFocus, $SID);
	$prepared_replace_performs->bind_param("sss", $PrimaryFocus, $SecondaryFocus, $SID);
	
	$prepared_replace_archetype = $connection->prepare("REPLACE INTO tbl_OrgArchetypes(Organization, Archetype) VALUES (?, ?)");
	$prepared_replace_archetype->bind_param("ss", $SID, $Archetype);
	
	$prepared_insert_roleplay = $connection->prepare("INSERT INTO tbl_RolePlayOrgs(Organization) VALUES (?)");
	$prepared_insert_roleplay->bind_param("s", $SID);
	$prepared_delete_roleplay = $connection->prepare("DELETE from tbl_RolePlayOrgs WHERE Organization = ?");
	$prepared_delete_roleplay->bind_param("s", $SID);
	
	$prepared_replace_language = $connection->prepare("REPLACE INTO tbl_OrgFluencies(Organization, Language) VALUES (?, ?)");
	$prepared_replace_language->bind_param("ss", $SID, $Language);

	for($x = 1; ; $x++){
		//3) Query SC-API (all orgs)
		$lines = file_get_contents(
			'http://sc-api.com/?api_source=live&system=organizations&action=all_organizations&source=rsi&start_page='
			. $x . '&end_page=' . $x . 
			'&items_per_page=1&sort_method=&sort_direction=ascending&expedite=0&format=raw'
		);    
		$dataArray = json_decode($lines, true);//json to php associated array
		if($dataArray == false)exit("failed to decode\n");
		unset($lines);
		
		if($dataArray["data"] == null)break;//if we have read all orgs
		
		foreach ($dataArray["data"] as $org){
			//4) Sub-query Org (more data)
			$subquery = file_get_contents(
			//we use cached because the sc-api does not provide language information on live queries
				'http://sc-api.com/?api_source=cache&system=organizations&action=single_organization&target_id='
				. $org['sid'] . '&expedite=0&format=raw'
			);
			$orgArray = json_decode($subquery, true);
			unset($subquery);
			if($orgArray['data'] == null)echo "WARNING: Org null!\n";
			
			//5) Bind data to statement
			$SID         = strtoupper( $orgArray['data']['sid'] );
			$Name        = html_entity_decode(  $orgArray['data']['title']  );
			$Icon        = $orgArray['data']['logo'];
			$MemberCount = intval( $orgArray['data']['member_count'] );
			$recruiting  = $orgArray['data']['recruiting'];
			$Archetype   = $orgArray['data']['archetype'];
			$Commitment  = $orgArray['data']['commitment'];
			$Roleplay    = $orgArray['data']['roleplay'];
			$Language    = html_entity_decode(  $orgArray['data']['lang']  );
			$PrimaryFocus   = $orgArray['data']['primary_focus'];
			$SecondaryFocus = $orgArray['data']['secondary_focus'];
			//banner
			//headline
			//history
			//manifesto
			//charter

			//test code
			//echo "SID: " . $SID . "\n";
			//echo "Name: " . $Name . "\n";
			//echo "$Icon \n";
			//echo "Members: " . $MemberCount . "\n";
			//echo "Commitment: " . $Commitment . "\n";
			//echo "Primary: " . $PrimaryFocus . "\n";
			//echo "Language: " . $Language . "\n";
			//echo "\n";

			//6) Execute Database Transactions
			$prepared_replace_org->execute();
			$prepared_replace_size->execute();
			$prepared_replace_commits->execute();
			if( $recruiting === "No" )$prepared_insert_full->execute();
			else $prepared_delete_full->execute();
			$prepared_replace_primary->execute();
			$prepared_replace_secondary->execute();
			$prepared_replace_performs->execute();
			$prepared_replace_archetype->execute();
			if( $Roleplay === "Yes" )$prepared_insert_roleplay->execute();
			else $prepared_delete_roleplay->execute();
			$prepared_replace_language->execute();
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
	$prepared_insert_full->close();
	$prepared_delete_full->close();
	$prepared_replace_primary->close();
	$prepared_replace_secondary->close();
	$prepared_replace_performs->close();
	$prepared_replace_archetype->close();
	$prepared_insert_roleplay->close();
	$prepared_delete_roleplay->close();
	$prepared_replace_language->close();
	
	$connection->close();
?>
