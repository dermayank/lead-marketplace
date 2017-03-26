<?php
    global $wpdb;

    $wploadPath = explode('/wp-content/', dirname(__FILE__));
    include_once(str_replace('wp-content/' , '', $wploadPath[0] . '/wp-load.php'));
    function shorten_url($url)
    {
        $out = get_option("google_url_short_key");
        $longUrl = $url;
    	$apiKey = $out['key'];

    	$postData = array('longUrl' => $longUrl, 'key' => $apiKey);
    	$jsonData = json_encode($postData);
    	$curlObj = curl_init();
    	curl_setopt($curlObj, CURLOPT_URL, 'https://www.googleapis.com/urlshortener/v1/url?key='.$apiKey);
    	curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, 1);
    	curl_setopt($curlObj, CURLOPT_SSL_VERIFYPEER, 0);
    	curl_setopt($curlObj, CURLOPT_HEADER, 0);
    	curl_setopt($curlObj, CURLOPT_HTTPHEADER, array('Content-type:application/json'));
    	curl_setopt($curlObj, CURLOPT_POST, 1);
    	curl_setopt($curlObj, CURLOPT_POSTFIELDS, $jsonData);
    	$response = curl_exec($curlObj);
    	$json = json_decode($response);

    	curl_close($curlObj);
    	$new_key= $json->id;
    	return $new_key;
    }
 ?>
