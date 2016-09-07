<?php
//input a query string as the first argument, and it will be parsed into an equivalent of _$SERVER

namespace thalos_api;

require_once(__DIR__.'/../application_layer/Controller.php');

$API = new Controller();

header('Content-type: application/json');
$TRACE = FALSE;

$parsed_url = parse_url($argv[1]);
if( isset($query_portion['query']) ){
	$query_portion = $parsed_url['query'];
	parse_str($query_portion, $query_array);
}
else  parse_str($argv[1], $query_array);

echo json_encode($API->Query($query_array), JSON_PRETTY_PRINT);

?>
