<?php

function send_sms($user_id,$pwd,$mobile_no, $msg)
{
	$request =""; //initialise the request variable
	$param[method]= "sendMessage";
	$param[send_to] = "91".$mobile_no;
	$param[msg] = $msg;
	$param[msg_type] = "TEXT"; //Can be "FLASH”/"UNICODE_TEXT"/”BINARY”
	$param[loginid] = $user_id; //user id
	$param[auth_scheme] = "PLAIN";
	$param[password] = $pwd; //pwd
	$param[v] = "1.1";
	$param[format] = "text";
	//Have to URL encode the values
	foreach($param as $key=>$val) {
	$request.= $key."=".urlencode($val);
		//we have to urlencode the values
		$request.= "&";
		//append the ampersand (&) sign after each
		//parameter/value pair
	}

	$request = substr($request, 0, strlen($request)-1);
	//remove final (&) sign from the request
	$url ="http://amrestechnologies.msg4all.com/GatewayAPI/rest?".$request;
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$curl_scraped_page = curl_exec($ch);
	curl_close($ch);
	list($status,$statuscode,$msg) = explode("|",$curl_scraped_page);
	return trim($status);
}
?>
