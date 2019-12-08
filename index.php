<?php

ini_set('display_errors', 1);
$url 	  =	'https://api.ongage.net/api/emails';

    $headers = [
        
		'X_USERNAME:kurt.martin@pmg360.com',
		'X_PASSWORD:cP46kBUNdSmwB98',
		'X_ACCOUNT_CODE:pmg360',
        'Content-Type: application/json',
    ];

	
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    // curl_setopt($ch, CURLOPT_POST, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    // curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array()));
    $result = curl_exec($ch);

    echo "<pre>";
	print_r($result);
    echo "</pre>";
    // curl_close($ch);