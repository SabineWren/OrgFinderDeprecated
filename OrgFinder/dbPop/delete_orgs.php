<?php

require_once('functions.php');
$queryAPI = queryAPI_closure();

// Connect to DB
if( sizeof($argv) < 3){
	echo "Correct usage: php " . $argv[0] . " <db username> <db password>\n";
	exit();
}

$connection = new mysqli("localhost",$argv[1],$argv[2], "cognitiondb");
if( mysqli_connect_errno() ){
	die( "Connection failed: " . mysqli_connect_error() );
}
if( !$connection->set_charset("utf8") )echo "Error changing connection character set\n";

//get list of orgs with their last scrape date
$to_delete = array();
$result = $connection->query("SELECT Organization as SID, DATEDIFF( curdate(), ScrapeDate ) as scrape FROM tbl_OrgMemberHistory GROUP BY SID HAVING MAX(ScrapeDate)");
$x = 0;
while($row = $result->fetch_assoc()){
	$SID    = $row['SID'];
	$scrape = $row['scrape'];
	if($scrape > 0){
		echo "checking SID = $SID\n";
		$queryString = "api_source=live&system=organizations&action=single_organization&target_id=$SID&expedite=0&format=pretty_json";
		$dataArray = $queryAPI($queryString);
		if($dataArray === -1){
			continue;
		}
		if($dataArray ===  0){
			$to_delete[] = $SID;
			if( count($to_delete) > 4 ){
				echo "Limit of 5 reached.\n";
				break;
			}
		}
		else echo "SID == $SID exists but has old data\n";
	}
	++$x;
	if($x % 1024 === 0)echo "looped $x times \n";
}
unset($result);

// delete old orgs
function closure(&$connection){
	return function($sql) use($connection){
		$results = $connection->query($sql);
		if(!$results)echo $connection->error . "\n";
	};
}

$execute = closure($connection);

foreach($to_delete as $SID){
	$execute("DELETE FROM tbl_Cog              WHERE SID = '$SID'");
	$execute("DELETE FROM tbl_OPPF             WHERE SID = '$SID'");
	$execute("DELETE FROM tbl_STAR             WHERE SID = '$SID'");
	$execute("DELETE FROM tbl_IconURLs         where Organization = '$SID'");
	$execute("DELETE FROM tbl_Commits          where Organization = '$SID'");
	$execute("DELETE FROM tbl_RolePlayOrgs     where Organization = '$SID'");
	$execute("DELETE FROM tbl_OrgArchetypes    where Organization = '$SID'");
	$execute("DELETE FROM tbl_FilterArchetypes where Organization = '$SID'");
	$execute("DELETE FROM tbl_FullOrgs         where Organization = '$SID'");
	$execute("DELETE FROM tbl_ExclusiveOrgs    where Organization = '$SID'");
	$execute("DELETE FROM tbl_OrgFluencies     where Organization = '$SID'");
	$execute("DELETE FROM tbl_FilterFluencies  where Organization = '$SID'");
	$execute("DELETE FROM tbl_OrgDescription   where SID = '$SID'");
	$execute("DELETE FROM tbl_Performs         where Organization = '$SID'");
	$execute("DELETE FROM tbl_PrimaryFocus     where Organization = '$SID'");
	$execute("DELETE FROM tbl_SecondaryFocus   where Organization = '$SID'");
	$execute("DELETE FROM tbl_OrgMemberHistory where Organization = '$SID'");
	$execute("DELETE FROM tbl_Organizations    where SID = '$SID'");
	echo "deleted $SID\n";
}

$connection->close();
?>
