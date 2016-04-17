<?php
$connection = new mysqli('localhost', '<username>', '<password>', 'cognitiondb');

if($connection->connect_error()){
    die("Connection failed: " . $connection->connect_error);
    exit("@Schon: help");
}

echo "Success: We connected like tetris!"

$stmt = $connection->prepare("INSERT INTO tbl_Organizations (SID, Name, Icon) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $SID, $Name, $Icon);

/* Query SC-API */

/* assign values to statement */

$stmt->execute()

mysqli_close($connection);
?>
