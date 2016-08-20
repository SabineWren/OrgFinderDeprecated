<?php

	mb_internal_encoding("UTF-8");

	if( sizeof($argv) < 3){
		echo "Correct usage: php " . $argv[0] . " <db username> <db password> <optional: full (counts members and their types)>\n";
		exit();
	}
	
	if( sizeof($argv) >= 4 && $argv[3] == 'full')$getFullMemberInfo = true;
	else $getFullMemberInfo = false;
	
	$connection = new mysqli("192.168.0.105",$argv[1],$argv[2], "cognitiondb");
	if( mysqli_connect_errno() ){
		die( "Connection failed: " . mysqli_connect_error() );
	}
	if( !$connection->set_charset("utf8") )echo "Error changing connection character set\n";

	echo "Done clustering... updating growth (this will take a few minutes)...\n";
	
	function getGrowthRate(&$SizeArray){
		$indexLast = count($SizeArray) - 1;
		if($indexLast == 0)return 0;//can't calculate growth from one scrape
		
		$newestTuple = $SizeArray[0];
		$oldestTuple = $SizeArray[$indexLast];
		
		$timeDifference = $oldestTuple['DaysAgo'] - $newestTuple['DaysAgo'];
		$sizeDifference = $newestTuple['Main'] - $oldestTuple['Main'];
		
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
	
	$prepared_init_growth = $connection->prepare("SELECT Main, abs( DATE(ScrapeDate) - DATE(CURDATE()) ) as DaysAgo FROM tbl_OrgMemberHistory WHERE Organization = ? ORDER BY ScrapeDate DESC LIMIT 10");
	$prepared_init_growth->bind_param("s", $SID);
	
	$prepared_insert_growth = $connection->prepare("UPDATE tbl_Organizations SET GrowthRate = ? WHERE SID = ?");
	$prepared_insert_growth->bind_param("ds", $Growth, $SID);
	
	$results = $connection->query("SELECT SID FROM tbl_Organizations");
	while( $result = $results->fetch_assoc() ){
		$SID = $result['SID'];
		$prepared_init_growth->execute();
		
		$meta = $prepared_init_growth->result_metadata();
	
		while ($field = $meta->fetch_field()) {
			$parameters[] = &$rowKeyValue[$field->name];
		}
		
		call_user_func_array(array($prepared_init_growth, 'bind_result'), $parameters);
	
		//fetch results into $parameters, which references the values of $rowKeyValue
		while ($prepared_init_growth->fetch()) {
			//copy the resulting row one attribute at a time
			//we use a loop because the contents are references
			foreach($rowKeyValue as $key => $val) {
				$x[$key] = $val;
			}
			$SizeArray[] = $x;//save the row
		}
		try{
			$Growth = getGrowthRate($SizeArray);
		}
		catch(Exception $e){
			$prepared_init_growth->close();
			$prepared_insert_growth->close();
			$connection->close();
			exit("debug exit\n");
		}
		unset($SizeArray);
		unset($parameters);
		$prepared_insert_growth->execute();
	}
	
	$prepared_init_growth->close();
	$prepared_insert_growth->close();
	
	$connection->close();
	echo "All insertions complete \n";
?>
