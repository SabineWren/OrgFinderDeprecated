<?php
	/*
	@license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
	
	Copyright (C) 2016 SabineWren
	
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
	* 9) rebuild growth
	* 10) Close connection
	*/
	mb_internal_encoding("UTF-8");
	
	//Obviously this is not safe for user input,
	//but the html data comes from RSI via sc-api,
	//so it 'should' be safe if RSI purified it.
	//Long term we should purify it ourselves and allow whitelisted URLs.
	$AllowedTags = '<a><br><p><h2><h3><h4><h5><span>';
	$LotsOfNewlines = "/\n\n\n+/";
	$ChopLongDashes = "/(-|—|_){10,}/";
	
	function getOrgSize(&$SID, &$connection){
//THIS IS A POSSIBLE SECURITY VULNERABILITY (SQL injection)
//but the input is from the sc-api, not from a regular user
		$rows = $connection->query("SELECT Size, Main, GrowthRate FROM tbl_Organizations WHERE SID = '$SID'");
		$row = $rows->fetch_assoc();
		//IN THE FUTURE, USE DATABASE CONSTRAINTS INSTEAD OF COMPARING THINGS!!!!!!
		if($row === null){
			echo "NOT FOUND Org SID = $SID\n";
			$result = ['Size' => 0];
			return $result;
		}
		if($row['Main'] === null){
			echo "Main is null\n";
			$result = ['Size' => 0];
			return $result;
		}
		
		$GrowthRate = $row['GrowthRate'];
		
		$rows = $connection->query("SELECT Size, Main, Affiliate, Hidden FROM tbl_OrgMemberHistory WHERE Organization = '$SID' ORDER BY ScrapeDate DESC LIMIT 1");
		
		if($rows == null){
			echo "Error: existing org not in history; treating it as new\n";
			$result = ['Size' => 0];
			return $result;
		}
		
		$row = $rows->fetch_assoc();
		$result = [
			'Size'      => $row['Size'],
			'Main'      => $row['Main'],
			'Affiliate' => $row['Affiliate'],
			'Hidden'    => $row['Hidden'],
			'GrowthRate'=> $GrowthRate
		];
		return $result;
	}
	
	function attemptInsert(&$SID, $Value, &$statement, &$connection){
		if( !$statement->execute() ){
			echo "Error Inserting for SID: $SID debug: $Value\n";
			echo $connection->error . "\n";
		}
	}
	
	require_once('functions.php');
	$queryAPI = queryAPI_closure();
	
	//1) Connect to DB
	if( sizeof($argv) < 3){
		echo "Correct usage: php " . $argv[0] . " <db username> <db password> <optional: full (counts members and their types)>\n";
		exit();
	}
	
	if( sizeof($argv) >= 4 && $argv[3] == 'full')$getFullMemberInfo = true;
	else $getFullMemberInfo = false;
	
	$connection = new mysqli("localhost",$argv[1],$argv[2], "cognitiondb");
	if( mysqli_connect_errno() ){
		die( "Connection failed: " . mysqli_connect_error() );
	}
	if( !$connection->set_charset("utf8") )echo "Error changing connection character set\n";
	
	$connection->autocommit(FALSE);
	
	//2) Prepare statements
	$prepared_insert_org  = $connection->prepare("INSERT INTO tbl_Organizations (SID, Name, Size, Main, CustomIcon, GrowthRate) VALUES (?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE Name = ?, Size = ?, Main = ?, CustomIcon = ?, GrowthRate = ?");
	$prepared_insert_org ->bind_param("ssddddsdddd", $SID, $Name, $Size, $Main, $CustomIcon, $GrowthRate, $Name, $Size, $Main, $CustomIcon, $GrowthRate);
	
	$prepared_insert_date  = $connection->prepare("INSERT INTO tbl_OrgMemberHistory (Organization, ScrapeDate, Size, Main, Affiliate, Hidden) VALUES (?, CURDATE(), ?, ?, ?, ?) ON DUPLICATE KEY UPDATE ScrapeDate = CURDATE(), Size = ?, Main = ?, Affiliate = ?, Hidden = ?");
	$prepared_insert_date ->bind_param("sdddddddd", $SID, $Size, $Main, $Affiliate, $Hidden, $Size, $Main, $Affiliate, $Hidden);
	
	$prepared_insert_icon = $connection->prepare("INSERT INTO tbl_IconURLs(Organization, Icon) VALUES (?, ?) ON DUPLICATE KEY UPDATE Icon = ?");
	$prepared_insert_icon->bind_param("sss", $SID, $IconURL, $IconURL);
	
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
	
	$prepared_insert_description = $connection->prepare("INSERT INTO tbl_OrgDescription(SID, Headline, Manifesto) VALUES(?, ?, ?) ON DUPLICATE KEY UPDATE Headline = ?, Manifesto = ?");
	$prepared_insert_description->bind_param("sssss", $SID, $Headline, $Manifesto, $Headline, $Manifesto);
	
	$numberInserted = 0;
	
	for($x = 1;; $x = $x + 4){//$x is current page number in query string
		//3) Query SC-API (all orgs)
		//the +3 means query four pages at a time
		$queryString  = "api_source=live&system=organizations&action=all_organizations&source=rsi&start_page=$x";
		$queryString .="&end_page=" . ($x+3) . "&items_per_page=1&sort_method=&sort_direction=ascending&expedite=0&format=raw";
		$dataArray = $queryAPI($queryString);
		unset($queryString);
		if($dataArray === -1 || $dataArray === 0)break;
		
		//4) Sub-query Org (more data)
		foreach ($dataArray["data"] as $org){
			//5a) Bind data from outer query
			$SID         = strtoupper( $org['sid'] );
			$Size        = intval( $org['member_count'] );
			
			$savedSize = getOrgSize($SID, $connection);
			
			//we can run this script with 'full' option to update all org sizes (slow)
			//if org is new/changed, we need to completely repopulate it
			if($getFullMemberInfo || $savedSize["Size"] != $Size){
				//get member info
				$membersArray = array();
				for($pageStart = 1;; $pageStart += 10){
					$memberQueryString  = "api_source=live&system=organizations&action=organization_members&target_id=$SID&start_page=";
					$memberQueryString .= "$pageStart&end_page=" . ($pageStart + 9) . "&expedite=0&format=pretty_json";
					$memberDataArray = $queryAPI($memberQueryString);
					if($memberDataArray === -1){
						echo "FAILED to query members for SID == $SID; skipping org\n";
						continue 2;
					}
					unset($memberQueryString);
					if($memberDataArray === 0)break;//done reading data
					
					foreach($memberDataArray["data"] as $member){
						$membersArray[] = $member;
					}
					$currentMemberCount = count($membersArray);
					echo "$currentMemberCount members\n";
				}
			
				$total = $Main = $Affiliate = $Hidden = 0;
				foreach($membersArray as $member){
					++$total;
					if($member['type'] === 'main')++$Main;
					else if($member['type'] === 'affiliate')++$Affiliate;
					else if($member['visibility'] === 'hidden' || $member['visibility'] == 'redacted')++$Hidden;
					else echo "WARNING: org $SID has member of unknown type\n";
				}
				if($total != $Size){
					echo "WARNING: org $SID has size $Size on main query, but size $total from adding members\n";
				}
				
				if($savedSize["Size"] == 0)$GrowthRate = 0.0;
				else $GrowthRate = $savedSize["GrowthRate"];
				
				unset($membersArray);
				//note sc-api does not always provide language information on live results
				$subqueryString  ='api_source=live&system=organizations&action=single_organization&target_id=';
				$subqueryString .= $org['sid'] . '&expedite=0&format=raw';
				$orgArray = $queryAPI($subqueryString);
				unset($subqueryString);
				if($orgArray === -1 || $orgArray === 0){
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
				
				$Recruiting     = $orgArray['data']['recruiting'];
				$Archetype      = $orgArray['data']['archetype'];
				$Commitment     = $orgArray['data']['commitment'];
				$Roleplay       = $orgArray['data']['roleplay'];
				$PrimaryFocus   = $orgArray['data']['primary_focus'];
				$SecondaryFocus = $orgArray['data']['secondary_focus'];
				$Language       = html_entity_decode( $org['lang'] );//live query for single orgs might have null language
				
				//banner
				//history
				//charter
				
				$Headline = $orgArray['data']['headline'];//max size limited by current VARCHAR size
				$Headline = strip_tags($Headline, $AllowedTags);
				$Headline = preg_replace($LotsOfNewlines, "\n\n", $Headline);
				$Headline = preg_replace($ChopLongDashes, "————————————————————————————————————————\n", $Headline);
				$Headline = substr($Headline , 0, 512);
	
				$Manifesto = $orgArray['data']['manifesto'];//max size limited by current VARCHAR size
				$Manifesto = strip_tags($Manifesto, $AllowedTags);
				$Manifesto = preg_replace($LotsOfNewlines, "\n\n", $Manifesto);
				$Manifesto = preg_replace($ChopLongDashes, "————————————————————————————————————————\n", $Manifesto);
				$Manifesto = substr($Manifesto , 0, 4096);
				
				unset($orgArray);
				
				echo $connection->error;
				
				//6) Execute Database Queries
				$connection->query('SET foreign_key_checks = 0');//speed up inserting into hub table);
				attemptInsert($SID, $Name, $prepared_insert_org, $connection);
				if($Main === null)echo "ERROR: Inserting NULL value\n";
				$connection->query('SET foreign_key_checks = 1');
				
				if($CustomIcon)attemptInsert($SID, $IconURL, $prepared_insert_icon, $connection);
				attemptInsert($SID, $Commitment, $prepared_insert_commits, $connection);
				
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
				
				if( !$prepared_insert_description->execute() )echo "error inserting description\n";
				
				++$numberInserted;
				echo "inserted SID = $SID\n";
				usleep(500000);
			}
			//if we didn't update any size information
			else{
				$Main      = $savedSize["Main"];
				$Affiliate = $savedSize["Affiliate"];
				$Hidden    = $savedSize["Hidden"];
			}
			//always insert a scrape date with member info
			attemptInsert($SID, 'insert date', $prepared_insert_date, $connection);
			$connection->commit();
		}
		echo $x * 32 . " Orgs looped; total inserted == $numberInserted\n";
	}
	unset($x);
	
	echo "Finished...\n";
	echo "Inserted $numberInserted Orgs\n";
	
	//7) Close Connection
	$connection->autocommit(TRUE);
	
	$prepared_insert_org->close();
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
	$prepared_insert_description->close();
	
	echo "Done main inserts! Rebuilding table clustering...\n";
	
	//8) Recluster Tables
	$connection->query('ALTER TABLE tbl_Organizations ENGINE=INNODB');
	$connection->query('ALTER TABLE tbl_Performs ENGINE=INNODB');
	$connection->query('ALTER TABLE tbl_PrimaryFocus ENGINE=INNODB');
	$connection->query('ALTER TABLE tbl_SecondaryFocus ENGINE=INNODB');
	$connection->query('ALTER TABLE tbl_OrgMemberHistory ENGINE=INNODB');
	$connection->query('ALTER TABLE tbl_IconURLs ENGINE=INNODB');
	$connection->query('ALTER TABLE tbl_Commits ENGINE=INNODB');
	$connection->query('ALTER TABLE tbl_RolePlayOrgs ENGINE=INNODB');
	$connection->query('ALTER TABLE tbl_OrgArchetypes ENGINE=INNODB');
	$connection->query('ALTER TABLE tbl_FilterArchetypes ENGINE=INNODB');
	$connection->query('ALTER TABLE tbl_FullOrgs ENGINE=INNODB');
	$connection->query('ALTER TABLE tbl_ExclusiveOrgs ENGINE=INNODB');
	$connection->query('ALTER TABLE tbl_OrgFluencies ENGINE=INNODB');
	$connection->query('ALTER TABLE tbl_FilterFluencies ENGINE=INNODB');
	$connection->query('ALTER TABLE tbl_OrgDescription ENGINE=INNODB');
	
	/* 9)
	 * Look at the last 8 days (including today):
	 * i.e. 7 days of growing past the start point
	 * so start (7) minus current (0) = 7 days of growth
	 *
	 * if only one day (today) then return 0.0
	 * else normalize to one week of growth
	 *
	 * if some scrapes were skipped, we look further back in time to still get at least 7 days of growth (no interpolation)
	 */
	echo "Done clustering... updating growth (this will take a few minutes)...\n";
	
	function getGrowthRate(&$SizeArray){
		$indexLast = count($SizeArray) - 1;
		if($indexLast == 0)return 0;//can't calculate growth from one scrape
		
		$newestTuple = $SizeArray[0];
		$oldestTuple = $SizeArray[$indexLast];
		
		$timeDifference = $oldestTuple['DaysAgo'] - $newestTuple['DaysAgo'];
		$sizeDifference = $newestTuple['Size'] - $oldestTuple['Size'];
		
		// the 7 normalizes to weekly average
		try{
			if(!$timeDifference)throw new Exception("Divide By Zero");
			return ($sizeDifference * 7 / $timeDifference);
		}
		catch(Exception $e){
			var_dump($SizeArray);
			var_dump($newestTuple);
			var_dump($oldestTuple);
			throw $e;
		}
	}
	
	$prepared_init_growth = $connection->prepare("SELECT Size, DATEDIFF( CURDATE(), ScrapeDate ) as DaysAgo FROM tbl_OrgMemberHistory WHERE Organization = ? ORDER BY ScrapeDate DESC LIMIT 8");
	$prepared_init_growth->bind_param("s", $SID);
	
	$prepared_insert_growth = $connection->prepare("UPDATE tbl_Organizations SET GrowthRate = ? WHERE SID = ?");
	$prepared_insert_growth->bind_param("ds", $Growth, $SID);
	
	//get a complete list of saved orgs to recalculate
	$results = $connection->query("SELECT SID FROM tbl_Organizations");
	$AllOrgsToUpdate = array();
	while( $result = $results->fetch_assoc() ){
		$AllOrgsToUpdate[] = $result['SID'];
	}
	unset($results);
	
	echo "Done building list of SIDs\n";
	$x = 0;
	
	//get data needed to recalculate growth
	foreach($AllOrgsToUpdate as $SID){
		$prepared_init_growth->execute();
		
		$meta = $prepared_init_growth->result_metadata();
	
		while ($field = $meta->fetch_field()) {
			$parameters[] = &$rowKeyValue[$field->name];
		}
		
		call_user_func_array(array($prepared_init_growth, 'bind_result'), $parameters);
	
		//fetch results into $parameters, which references the values of $rowKeyValue
		$row = array();
		while ($prepared_init_growth->fetch()) {
			//copy the resulting row one attribute at a time
			//we use a loop because the contents are references
			foreach($rowKeyValue as $key => $val) {
				$row[$key] = $val;
			}
			$SizeArray[] = $row;
		}
		unset($row);
		
		//recalculate growth
		try{
			$Growth = getGrowthRate($SizeArray);
		}
		catch(Exception $e){
			$prepared_init_growth->close();
			$prepared_insert_growth->close();
			$connection->close();
			exit("debug exit (no resource leak)\n");
		}
		unset($SizeArray);
		unset($parameters);
		$prepared_insert_growth->execute();
		++$x;
		if($x%1024 === 0)echo "recalculated $x organizations\n";
	}
	
	$prepared_init_growth->close();
	$prepared_insert_growth->close();
	echo "Clustering growth...\n";
	$connection->query('ALTER TABLE tbl_GrowthRate ENGINE=INNODB');
	echo "Done updating growth!\n";
	
	//10) Close Connection
	$connection->close();
	echo "All insertions complete (total: $numberInserted)\n";
?>

