<?php
/*
	@license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
	
	Copyright (C) 2016 SabineWren
	
	GNU AFFERO GENERAL PUBLIC LICENSE Version 3, 19 November 2007
	https://www.gnu.org/licenses/agpl-3.0.html
	
	@license-end
*/
function queryAPI_closure(){
	$path = __dir__ . '/../sc_api';
	if(!$path)die("unable to locate sc_api\n\n");
	$path = $path . '/index.php';
	
	return function(&$queryString) use($path){
		for($failCounter = 0; $failCounter < 4; ++$failCounter){
			$lines = shell_exec("php5 $path '$queryString'");
			if(!$lines){
				sleep(1);
				continue;//try again
			}
		
			$dataArray = json_decode($lines, true);//json to php associated array
			if($dataArray == false){
				echo "failed to decode\n";
				var_dump($path);
				var_dump($lines);
				var_dump($dataArray);
				return -1;
			}
			unset($lines);
			break;
		}
		if($failCounter >= 4)return -1;
		if($dataArray['data'] === null){
			return 0;
		}
		return $dataArray;
	};
}
?>
