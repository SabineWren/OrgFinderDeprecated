<?php
mb_internal_encoding("UTF-8");

function getSID($index){
	$currentSID = 'SID' . $index;
	if( isset($_POST[$currentSID]) ){
		$SID = $_POST[$currentSID];
		if($SID === $currentSID)return 0;
		return $SID;
	}
	return 0;
}

$password = $_POST['password'];

$connection = new mysqli("localhost", "insert_cog", $password, "cognitiondb");
if( mysqli_connect_errno() ){
	die( "Connection failed: " . mysqli_connect_error() );
}
if( !$connection->set_charset("utf8") )echo "Error changing connection character set\n";

$prepared_insert_cog = $connection->prepare("INSERT INTO tbl_Cog(SID) VALUES(?) ON DUPLICATE KEY UPDATE SID = ?");
echo $connection->error . "<br>";
$prepared_insert_cog ->bind_param("ss", $SID, $SID);
echo $connection->error . "<br>";

for($i = 0; $i < 7; ++$i){
	$SID = getSID($i);
	if($SID === 0)continue;
	
	if(!$prepared_insert_cog->execute())echo "Failed to insert Org == $SID<br>";
	else echo "inserted " . $SID . "<br>";
}

$prepared_insert_cog->close();
$connection->close();

?>
