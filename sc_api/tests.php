<?php

echo "testing single org\n";
$output = shell_exec('php5 ./index.php "http://example.com/?api_source=live&system=organizations&action=single_organization&target_id=TEST&expedite=0&format=raw"');
if(!$output)die('ERROR for single org');
$queryResult = json_decode($output, true);
$dataArray = $queryResult['data'];
if($dataArray === null)die("ERROR Query Result null\n");
echo $output . "\n";

echo "testing all orgs\n";
$output = shell_exec('php5 ./index.php "http://example.com/?api_source=live&system=organizations&action=all_organizations&source=rsi&start_page=1&end_page=1&items_per_page=1&sort_method=&sort_direction=ascending&expedite=0&format=raw"');
if(!$output)die('ERROR for all orgs');
$queryResult = json_decode($output, true);
$dataArray = $queryResult['data'];
if($dataArray === null)die("ERROR Query Result null\n");
echo $output . "\n";

echo "testing org members\n";
$output = shell_exec('php5 ./index.php "http://example.com/?api_source=live&system=organizations&action=organization_members&target_id=TEST&start_page=1&end_page=1&expedite=0&format=pretty_json"');
if(!$output)die('ERROR for org members');
$queryResult = json_decode($output, true);
$dataArray = $queryResult['data'];
if($dataArray === null)die("ERROR Query Result null\n");
echo $output . "\n";

echo "tests completed\n";
?>

