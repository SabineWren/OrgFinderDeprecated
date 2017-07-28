<?php
mb_internal_encoding("UTF-8");

function getSID($index){
	$currentSID = 'SID' . $index;
	if( isset($_POST[$currentSID]) && $_POST[$currentSID] !== "" ){
		$SID = $_POST[$currentSID];
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
$prepared_insert_cog ->bind_param("ss", $SID, $SID);
$prepared_delete_cog = $connection->prepare("DELETE FROM tbl_Cog WHERE SID = ?");
$prepared_delete_cog ->bind_param("s", $SID);

$path = __dir__ . '/../dbPop';
$path = $path . '/manual_update.php';

for($i = 0; $i < 8; ++$i){
	$SID = getSID($i);
	if($SID === 0)continue;
	
	if($i < 5){
		$update = shell_exec("php5 $path insert_cog $password $SID");
		//echo "php5 $path insert_cog $password $SID<br>";
		if(!$update)echo "Failed to update SID == $SID prior to inserting<br>";
		if(!$prepared_insert_cog->execute())echo "Failed to insert Org == $SID into cog<br>";
		else echo "inserted " . $SID . " into cog<br>";
	}
	else{
		if(!$prepared_delete_cog->execute())echo "Failed to remove Org == $SID from cog<br>";
		else echo "removed " . $SID . " from cog<br>";
	}
}

$prepared_insert_cog->close();
$prepared_delete_cog->close();
$connection->close();

?>
