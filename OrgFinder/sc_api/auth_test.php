<?php

function GetPage($url, $custom_headers = array())
{
	$agent = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:29.0) Gecko/20100101 Firefox/29.0";
	$cookie_file_path = __DIR__."/cookies.txt";

	// begin script
	$ch = curl_init();

	$headers = $custom_headers;
	$headers[] = "Accept: */*";
	$headers[] = "Connection: Keep-Alive";

	// basic curl options for all requests
	curl_setopt($ch, CURLOPT_HTTPHEADER,  $headers);
	curl_setopt($ch, CURLOPT_HEADER,  0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);    
	curl_setopt($ch, CURLOPT_USERAGENT, $agent);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file_path);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file_path);
	
	curl_setopt($ch, CURLOPT_URL, $url);

	return curl_exec($ch);
}

function LoginRSI()
{
	// options
	$HANDLE           = 'cpsiegen';
	$PASSWORD         = '280e7f179b429a91f2fc5ce9dbb4c068';
	$LOGINURL         = "https://robertsspaceindustries.com/connect";
	$agent            = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:29.0) Gecko/20100101 Firefox/29.0";
	$cookie_file_path = __DIR__."/cookies.txt";
	$cookies = array();


	// begin script
	$ch = curl_init();

	// extra headers
	$headers[] = "Accept: */*";
	$headers[] = "Connection: Keep-Alive";
	$headers[] = "Referer: https://robertsspaceindustries.com/";

	// basic curl options for all requests
	curl_setopt($ch, CURLOPT_HTTPHEADER,  $headers);
	curl_setopt($ch, CURLOPT_HEADER,  0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);    
	curl_setopt($ch, CURLOPT_USERAGENT, $agent);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file_path);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file_path);
	
	// set first URL
	curl_setopt($ch, CURLOPT_URL, $LOGINURL);

	// execute session to get cookies and required form inputs
	$content = curl_exec($ch);
	
	$string = file_get_contents($cookie_file_path);
	
	// get cookies
	$cookies_matches = array();
	preg_match_all('|\.robertsspaceindustries\.com(\s+\S+){4}\s+(?<name>\S+)\s+(?<value>\S+)\s*|', $string, $cookies_matches , PREG_SET_ORDER);
	
	$headers = array();
	$headers[] = "Accept: */*";
	$headers[] = "Connection: Keep-Alive";
	$headers[] = "X-Requested-With: XMLHttpRequest";
	$headers[] = "Referer: ".$LOGINURL;
	foreach($cookies_matches as $cookie)
	{
		if($cookie['name'] == 'Rsi-Token')
		{
			$headers[] = "X-Rsi-Token: ".$cookie['value'];
		}
		
		$cookies[$cookie['name']] = $cookie['value'];
	}
	
	$fields['username'] = $HANDLE;
	$fields['password'] = $PASSWORD;
	$fields['remember'] = 1;

	// set postfields using what we extracted from the form
	$POSTFIELDS = http_build_query($fields);

	// change URL to login URL
	$LOGINURL   = "https://robertsspaceindustries.com/api/account/signin";
	curl_setopt($ch, CURLOPT_URL, $LOGINURL);

	// set post options
	curl_setopt($ch, CURLOPT_HTTPHEADER,  $headers);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $POSTFIELDS);
	curl_setopt($ch, CURLOPT_HEADER,  0);

	// perform login
	$result = curl_exec($ch);
	
	return $result;
}

$session_data = json_decode(LoginRSI(), true);

$messages_page = GetPage('https://forums.robertsspaceindustries.com/profile.json/comments/siegen?page=1');

$data = json_decode($messages_page);