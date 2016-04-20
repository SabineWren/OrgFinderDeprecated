<?php
echo "attempting connection\n";
$connection = new mysqli('64.71.77.121', 'Sabine', '<pw goes here>', 'cognitiondb', 3306);
echo "constructor executed\n";

if($connection->connect_error()){
    die("Connection failed: " . $connection->connect_error);
    exit("@Schon: help");
}

echo "Success: We connected like tetris!\n";

$stmt = $connection->prepare("INSERT INTO tbl_Organizations (SID, Name, Icon) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $SID, $Name, $Icon);

$stmt = $connection->prepare("SELECT * FROM View_OrganizationsEverything WHERE SID LIKE ? OR Name LIKE ?");
$stmt->bind_param("ss", $SID, $Name);

/* wait for queries */

/* assign values to statement */
$SID = "%%";
$Name= "%%";
$stmt->execute();

/* code copied from stack exchange
  * http://stackoverflow.com/questions/750648/select-from-in-mysqli */
$meta = $stmt->result_metadata();

while ($field = $meta->fetch_field()) {
  $parameters[] = &$row[$field->name];
}

call_user_func_array(array($stmt, 'bind_result'), $parameters);

while ($stmt->fetch()) {
  foreach($row as $key => $val) {
    $x[$key] = $val;
  }
  $results[] = $x;
}
/* end copied code */

foreach($results as result){
	echo(result);
}

echo "Done - Closing Connection.\n";

mysqli_close($connection);
?>




