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
	*			6) Execute Database Transaction
	* 7) Close Statements
	* 8) Recluster Tables
	* 9) Close connection
	*/
	mb_internal_encoding("UTF-8");
	
	function getOrgSize(&$SID, &$connection){
//THIS IS A POSSIBLE SECURITY VULNERABILITY (SQL injection)
//but the input is from the sc-api, not from a regular user
		$rows = $connection->query("SELECT Size FROM tbl_Organizations WHERE SID = '$SID'");
		$row = $rows->fetch_assoc();
		$connection->commit();// may be unnecessary
		if($row == null){
			echo "NOT FOUND Org SID = $SID\n";
			return 0;
		}
		return $row['Size'];
	}
	
	function attemptInsert(&$SID, $Value, &$statement, &$connection){
		if( !$statement->execute() ){
			echo "Error Inserting for SID: $SID debug: $Value\n";
			echo $connection->error . "\n";
		}
	}
	
	function queryAPI(&$queryString){
		$dataArray = null;
		
		for($failCounter = 0; $failCounter < 4; ++$failCounter){
			$lines = file_get_contents($queryString);
			if(!$lines){
				sleep(1);
				continue;//try again
			}
			
			$dataArray = json_decode($lines, true);//json to php associated array
			if($dataArray == false){
				echo "failed to decode\n";
				return -1;
			}
			unset($lines);
			
			if($dataArray["data"] == null){
				echo "Query returned null\n";
				continue;//try again; we might be done
			}
			break;
		}
		
		if($failCounter >= 4)return -1;
		return $dataArray;
	}
	
	//1) Connect to DB
	if( sizeof($argv) < 3){
		echo "Correct usage: php " . $argv[0] . " <db username> <db password>\n";
		exit();
	}
	
	$connection = new mysqli("192.168.0.105",$argv[1],$argv[2], "cognitiondb");
	if( mysqli_connect_errno() ){
		die( "Connection failed: " . mysqli_connect_error() );
	}
	if( !$connection->set_charset("utf8") )echo "Error changing connection character set\n";
	
	$connection->autocommit(FALSE);//accelerate inserts BUGGY
	
	//2) Prepare statements
	$prepared_insert_org  = $connection->prepare("INSERT INTO tbl_Organizations (SID, Name, Size, Main, CustomIcon, URL) VALUES (?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE Name = ?, Size = ?, Main = ?, CustomIcon = ?, URL = ?");
	$prepared_update_org  = $connection->prepare("UPDATE tbl_Organizations SET Size = ?, Main = ? WHERE SID = ?");
	$prepared_insert_org ->bind_param("ssssdssssds", $SID, $Name, $Size, $Main, $CustomIcon, $URL, $Name, $Size, $Main, $CustomIcon, $URL);
	$prepared_update_org ->bind_param("dds", $Size, $Main, $SID);
	
	$prepared_insert_date  = $connection->prepare("INSERT INTO tbl_OrgMemberHistory (Organization, ScrapeDate, Size, Main) VALUES (?, CURDATE(), ?, ?) ON DUPLICATE KEY UPDATE ScrapeDate = CURDATE(), Size = ?, Main = ?");
	$prepared_insert_date ->bind_param("sdddd", $SID, $Size, $Main, $Size, $Main);
	
	$prepared_insert_icon = $connection->prepare("INSERT INTO tbl_IconURLs(Organization, Icon) VALUES (?, ?)");
	$prepared_insert_icon->bind_param("ss", $SID, $IconURL);
	
	$prepared_insert_commits = $connection->prepare("INSERT INTO tbl_Commits(Organization, Commitment) VALUES (?, ?) ON DUPLICATE KEY UPDATE Commitment = ?");
	$prepared_insert_commits->bind_param("sss", $SID, $Commitment, $Commitment);
	
	$prepared_insert_full = $connection->prepare("INSERT INTO tbl_FullOrgs(Organization) VALUES (?) ON DUPLICATE KEY UPDATE Organization = ?");
	$prepared_delete_full = $connection->prepare("DELETE from tbl_FullOrgs WHERE Organization = ?");
	$prepared_insert_full->bind_param("ss", $SID, $SID);
	$prepared_delete_full->bind_param("s", $SID);
	
	$prepared_insert_primary   = $connection->prepare("INSERT INTO tbl_PrimaryFocus  (PrimaryFocus,   Organization) VALUES (?, ?) ON DUPLICATE KEY UPDATE PrimaryFocus = ?");
	$prepared_insert_secondary = $connection->prepare("INSERT INTO tbl_SecondaryFocus(SecondaryFocus, Organization) VALUES (?, ?) ON DUPLICATE KEY UPDATE SecondaryFocus = ?");
	$prepared_insert_performs  = $connection->prepare("INSERT INTO tbl_Performs(PrimaryFocus, SecondaryFocus, Organization) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE PrimaryFocus = ?, SecondaryFocus = ?");
	$prepared_insert_primary  ->bind_param("sss",  $PrimaryFocus, $SID, $PrimaryFocus);
	$prepared_insert_secondary->bind_param("sss",  $SecondaryFocus, $SID, $SecondaryFocus);
	$prepared_insert_performs ->bind_param("sssss", $PrimaryFocus, $SecondaryFocus, $SID, $PrimaryFocus, $SecondaryFocus);
	
	$prepared_insert_archetype  = $connection->prepare("INSERT INTO tbl_OrgArchetypes(Organization, Archetype) VALUES (?, ?) ON DUPLICATE KEY UPDATE Archetype = ?");
	$prepared_insert_filterarch = $connection->prepare("INSERT INTO tbl_FilterArchetypes(Archetype, Organization) VALUES (?, ?) ON DUPLICATE KEY UPDATE Archetype = ?");
	$prepared_insert_archetype  ->bind_param("sss", $SID, $Archetype, $Archetype);
	$prepared_insert_filterarch ->bind_param("sss", $Archetype, $SID, $Archetype);
	
	$prepared_insert_roleplay = $connection->prepare("INSERT INTO tbl_RolePlayOrgs(Organization) VALUES (?) ON DUPLICATE KEY UPDATE Organization = ?");
	$prepared_delete_roleplay = $connection->prepare("DELETE from tbl_RolePlayOrgs WHERE Organization = ?");
	$prepared_insert_roleplay->bind_param("ss", $SID, $SID);
	$prepared_delete_roleplay->bind_param("s", $SID);
	
	$prepared_insert_language = $connection->prepare("INSERT INTO tbl_OrgFluencies(Organization, Language) VALUES (?, ?) ON DUPLICATE KEY UPDATE Language = ?");
	$prepared_insert_filterlang = $connection->prepare("INSERT INTO tbl_FilterFluencies(Language, Organization) VALUES (?, ?) ON DUPLICATE KEY UPDATE Language = ?");
	$prepared_insert_language->bind_param("sss", $SID, $Language, $Language);
	$prepared_insert_filterlang->bind_param("sss", $Language, $SID, $Language);
	
	$numberInserted = 0;
	$numberUpdated  = 0;
	
	for($x = 1;; $x++){//$x is current page number in query string
		//3) Query SC-API (all orgs)
		$queryString  = "http://sc-api.com/?api_source=live&system=organizations&action=all_organizations&source=rsi&start_page=$x";
		$queryString .="&end_page=$x&items_per_page=1&sort_method=&sort_direction=ascending&expedite=0&format=raw";
		$dataArray = queryAPI($queryString);
		if($dataArray == -1)break;
		//echo "Fetched metadata on " . sizeof($dataArray["data"]) . " Orgs\n";
		
		//4) Sub-query Org (more data)
		$i = 0;
		foreach ($dataArray["data"] as $org){
			//5a) Bind data from outer query
			$SID         = strtoupper( $org['sid'] );
			$Size        = intval( $org['member_count'] );
			$Main        = 0;
			
			$savedSize = getOrgSize($SID, $connection);
			//only query the org if it's new
			if(  $savedSize == 0  ){
				//note sc-api does not provide language information on live results
				$subqueryString  ='http://sc-api.com/?api_source=live&system=organizations&action=single_organization&target_id='
				$subqueryString .= $org['sid'] . '&expedite=0&format=raw'
				$orgArray = queryAPI($subqueryString);
				if($orgArray == -1){
					echo "\nWARNING -- unable to query org $SID; skipping\n\n";
					continue;
				}
				
				//5b) Bind data from subquery
				$Name    = html_entity_decode( $org['title'] );
				$IconURL = $orgArray['data']['logo'];
				if(
					//Organization
					$IconURL == "http://robertsspaceindustries.com/rsi/static/images/organization/defaults/logo/generic.jpg"
					||
					//Corporation
					$IconURL == "http://robertsspaceindustries.com/rsi/static/images/organization/defaults/logo/corp.jpg"
					||
					//PMC
					$IconURL == "http://robertsspaceindustries.com/rsi/static/images/organization/defaults/logo/pmc.jpg"
					||
					//Faith
					$IconURL == "http://robertsspaceindustries.com/rsi/static/images/organization/defaults/logo/faith.jpg"
					||
					//Syndicate
					$IconURL == "http://robertsspaceindustries.com/rsi/static/images/organization/defaults/logo/syndicate.jpg"
				)$CustomIcon = 0;
				else $CustomIcon = 1;
				
				$URL            = 'https://robertsspaceindustries.com/orgs/' . $SID;
				$Recruiting     = $orgArray['data']['recruiting'];
				$Archetype      = $orgArray['data']['archetype'];
				$Commitment     = $orgArray['data']['commitment'];
				$Roleplay       = $orgArray['data']['roleplay'];
				$PrimaryFocus   = $orgArray['data']['primary_focus'];
				$SecondaryFocus = $orgArray['data']['secondary_focus'];
				$Language       = html_entity_decode( $dataArray["data"][$i]['lang'] );//live query for single org always has null language
				//banner
				//headline
				//history
				//manifesto
				//charter
				unset($orgArray);
				//test code
				//echo "SID: " . $SID . "\n";
				//echo "Name: " . $Name . "\n";
				//echo "$Icon \n";
				//echo "Members: " . $Size . "\n";
				//echo "Commitment: " . $Commitment . "\n";
				//echo "Primary: " . $PrimaryFocus . "\n";
				//echo "\n";
				
				//6) Execute Database Queries
				$connection->query('SET foreign_key_checks = 0');//speed up inserting into hub table);
				attemptInsert($SID, $Name, $prepared_insert_org, $connection);
				$connection->query('SET foreign_key_checks = 1');
				
				attemptInsert($SID, 'update date', $prepared_insert_date, $connection);
				attemptInsert($SID, $IconURL,      $prepared_insert_icon, $connection);
				attemptInsert($SID, $Commitment,   $prepared_insert_commits, $connection);
				
				if( $Recruiting === "No" )attemptInsert($SID, $Recruiting, $prepared_insert_full, $connection);
				else                      attemptInsert($SID, $Recruiting, $prepared_delete_full, $connection);
				
				attemptInsert($SID, $PrimaryFocus,   $prepared_insert_primary,    $connection);
				attemptInsert($SID, $SecondaryFocus, $prepared_insert_secondary,  $connection);
				attemptInsert($SID, 'Performs',      $prepared_insert_performs,   $connection);
				attemptInsert($SID, $Archetype,      $prepared_insert_archetype,  $connection);
				attemptInsert($SID, $Archetype,      $prepared_insert_filterarch, $connection);
				
				
				if( $Roleplay === "Yes" )attemptInsert($SID, $Roleplay, $prepared_insert_roleplay, $connection);
				else                     attemptInsert($SID, $Roleplay, $prepared_delete_roleplay, $connection);
				
				if($Language != null){
					attemptInsert($SID, $Language, $prepared_insert_language,   $connection);
					attemptInsert($SID, $Language, $prepared_insert_filterlang, $connection);
				}
				
				//was having lock timeouts without committing
				//there's probably a better way to bulk insert than committing each time.
				++$numberInserted;
				echo "inserted SID = $SID\n";
			}
			//update existing org size
			else if ( $Size != $savedSize ){
				attemptInsert($SID, 'update org',  $prepared_update_org,  $connection);
				++$numberUpdated;
				echo "updated SID = $SID\n";
			}
			//always insert a scrape dape
			attemptInsert($SID, 'insert date', $prepared_insert_date, $connection);
			$connection->commit();
			++$i;
		}
		if($x % 32 == 1)echo "Loop $x with " . $x * 32 . " Orgs looped; total inserted == $numberInserted; total updated == $numberUpdated\n";
	}
	
	echo "Finished...\n";
	echo "Inserted $numberInserted Orgs\n";
	echo "Updated  $numberUpdated Orgs\n";
	
	//7) Close Connection
	$connection->autocommit(TRUE);
	
	$prepared_insert_org->close();
	$prepared_update_org->close();
	$prepared_insert_date->close();
	$prepared_insert_icon->close();
	$prepared_insert_commits->close();
	$prepared_insert_full->close();
	$prepared_delete_full->close();
	$prepared_insert_primary->close();
	$prepared_insert_secondary->close();
	$prepared_insert_performs->close();
	$prepared_insert_archetype->close();
	$prepared_insert_filterarch->close();
	$prepared_insert_roleplay->close();
	$prepared_delete_roleplay->close();
	$prepared_insert_language->close();
	$prepared_insert_filterlang->close();
	
	echo "Done inserts! Rebuilding table clustering...\n";
	
	//8) Recluster Tables
	$connection->query('ALTER TABLE tbl_Organizations ENGINE=INNODB');
	$connection->query('ALTER TABLE tbl_OrgMemberHistory ENGINE=INNODB');
	$connection->query('ALTER TABLE tbl_IconURLs ENGINE=INNODB');
	$connection->query('ALTER TABLE tbl_Commits ENGINE=INNODB');
	$connection->query('ALTER TABLE tbl_RolePlayOrgs ENGINE=INNODB');
	$connection->query('ALTER TABLE tbl_OrgArchetypes ENGINE=INNODB');
	$connection->query('ALTER TABLE tbl_FilterArchetypes ENGINE=INNODB');
	$connection->query('ALTER TABLE tbl_FullOrgs ENGINE=INNODB');
	$connection->query('ALTER TABLE tbl_ExclusiveOrgs ENGINE=INNODB');
	$connection->query('ALTER TABLE tbl_PrimaryFocus ENGINE=INNODB');
	$connection->query('ALTER TABLE tbl_SecondaryFocus ENGINE=INNODB');
	$connection->query('ALTER TABLE tbl_FilterArchetypes ENGINE=INNODB');
	$connection->query('ALTER TABLE tbl_OrgFluencies ENGINE=INNODB');
	$connection->query('ALTER TABLE tbl_FilterFluencies ENGINE=INNODB');
	
	//9) Close Connection
	$connection->close();
	echo "All insertions complete (total: $numberInserted)\n";
?>
