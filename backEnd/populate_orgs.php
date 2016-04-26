<?php
/*
 * @Description:
 * 1) Connect to DB
 * 2) Prepare statement
 * OUTER LOOP:
 *		3) Query SC-API
 *		INNER LOOP:
 *			4) Bind data to statement
 *			5) Execute insertion
 * 6) Close connection
 */

/***** 1) Connect to DB
// DO NOT LEAVE PASSWORD ON GITHUB
$connection = new mysqli('localhost', '<username>', '<password>', 'cognitiondb');

if($connection->connect_error()){
    die("Connection failed: " . $connection->connect_error);
    exit("@Schon: help");
}

echo "Success: We connected like tetris!\n\n"
*/

/***** 2) Prepare statement
$stmt = $connection->prepare("INSERT INTO tbl_Organizations (SID, Name, Icon) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $SID, $Name, $Icon);
*/

/***** 3) Query SC-API */
	for($x = 1; $x <= 2; $x++)
	{
		$y = 0;
		$lines = file_get_contents(
			'http://sc-api.com/?api_source=live&system=organizations&action=all_organizations&source=rsi&start_page='
			. $x . '&end_page=' . $x . 
			'&items_per_page=1&sort_method=&sort_direction=ascending&expedite=0&format=pretty_json'
		);    
		$dataArray = json_decode($lines, true);//convert json object to php associative array
		if($dataArray == false)exit("failed to decode\n");

		foreach ($dataArray["data"] as $org)
		{
			/* Bind data to statement */
			$SID = strtoupper( $org['sid'] );
			$memberCount = intval( $org['member_count'] );
			$name = $org['title'];
			$icon = $org['logo'];

			/* test code */
			echo "SID: " . $SID . "\n";
			echo "Members: " . $memberCount . "\n";
			echo "Name: " . $name . "\n";
			echo "$icon \n";
			echo "\n";

			/* Execute insertion */
			//$stmt->execute();
		}
		
		unset($lines);
		unset($data);
	}



/***** 6) Close connection
mysqli_close($connection);
*/
?>
