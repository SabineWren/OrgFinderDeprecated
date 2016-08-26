<?php
	/*
	@license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
	
	Copyright (C) 2016 SabineWren
	
	GNU AFFERO GENERAL PUBLIC LICENSE Version 3, 19 November 2007
	https://www.gnu.org/licenses/agpl-3.0.html
	
	@license-end
	*/
	mb_internal_encoding("UTF-8");
	
	function attemptInsert(&$SID, $Value, &$statement, &$connection){
		if( !$statement->execute() ){
			echo "Error Inserting for SID: $SID debug: $Value\n";
			echo $connection->error . "\n";
		}
	}
	
	function queryAPI(&$queryString){
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
				echo "Query returned null; failCounter == $failCounter\n";
				continue;//try again; we might be done
			}
			break;
		}
		
		if($failCounter >= 4)return -1;
		return $dataArray;
	}
	
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
	
	$prepared_insert_description = $connection->prepare("INSERT INTO tbl_OrgDescription(SID, Headline, Manifesto) VALUES(?, ?, ?) ON DUPLICATE KEY UPDATE Headline = ?, Manifesto = ?");
	$prepared_insert_description->bind_param("sssss", $SID, $Headline, $Manifesto, $Headline, $Manifesto);
	
	//CACHING
	$subqueryString  ='http://sc-api.com/?api_source=cache&system=organizations&action=single_organization&target_id=';
	$subqueryString .= "$SID&expedite=0&format=raw";
	$orgArray = queryAPI($subqueryString);
	unset($subqueryString);
	
	//Obviously this is not safe for user input,
	//but the html data comes from RSI via sc-api,
	//so it 'should' be safe if RSI purified it.
	//Long term we should purify it ourselves and allow whitelisted URLs.
	$AllowedTags = '<br><p><h2><h3><h4><h5>';
	$LotsOfNewlines = "/\n\n\n+/";
	
	$Headline = $orgArray['data']['headline'];//max size limited by current VARCHAR size
	$Headline = strip_tags($Headline, $AllowedTags);
	$Headline = preg_replace($LotsOfNewlines, "\n\n", $Headline);
	$Headline = substr($Headline , 0, 512);
	
	$Manifesto = $orgArray['data']['manifesto'];//max size limited by current VARCHAR size
	$Manifesto = strip_tags($Manifesto, $AllowedTags);
	$Manifesto = preg_replace($LotsOfNewlines, "\n\n", $Manifesto);
	$Manifesto = substr($Manifesto , 0, 4096);
	
	unset($orgArray);
	echo $connection->error;
	
	if( !$prepared_insert_description->execute() )echo "error inserting description\n";
	
	$prepared_insert_description->close();
	$connection->close();
	echo "All insertions complete\n";
?>
