<?php
//THIS SCRIPT IS FOR TESTING WAYS OF FILTERING ORG INFO
	mb_internal_encoding("UTF-8");
	
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
	
	//Obviously this is not safe for user input,
	//but the html data comes from RSI via sc-api,
	//so it 'should' be safe if RSI purified it.
	//Long term we should purify it ourselves and allow whitelisted URLs.
	$AllowedTags    = '<a><br><p><h2><h3><h4><h5><span>';
	$LotsOfNewlines = "/\n\n\n+/";
	$ChopLongDashes = "/(-|—){10,}/";
	
	//note sc-api does not always provide language information on live results
	$subqueryString  ='http://sc-api.com/?api_source=cache&system=organizations&action=single_organization&target_id=';
	$subqueryString .= 'ADI' . '&expedite=0&format=raw';
	$orgArray = queryAPI($subqueryString);
	unset($subqueryString);
	if($orgArray == -1){
		echo "\nWARNING -- unable to query org $SID; skipping\n\n";
		continue;
	}
	
	//banner
	//history
	//charter
	
	$Headline = $orgArray['data']['headline'];//max size limited by current VARCHAR size
	$Headline = strip_tags($Headline, $AllowedTags);
	$Headline = preg_replace($LotsOfNewlines, "\n\n", $Headline);
	$Headline = preg_replace($ChopLongDashes, "\n", $Headline);
	$Headline = substr($Headline , 0, 512);
	echo "$Headline\n\n";
	
	$Manifesto = $orgArray['data']['manifesto'];//max size limited by current VARCHAR size
	$Manifesto = strip_tags($Manifesto, $AllowedTags);
	$Manifesto = preg_replace($LotsOfNewlines, "\n\n", $Manifesto);
	$Manifesto = preg_replace($ChopLongDashes, "————————————————————————————————————————\n", $Manifesto);
	$Manifesto = substr($Manifesto, 0, 4096);
	echo "$Manifesto\n\n";
	
	unset($orgArray);

?>
