<?php

ini_set('display_errors', 1);
ini_set('memory_limit', '-1');
require './Ongage.php';

$userName = 'kurt.martin@pmg360.com';
$password ='cP46kBUNdSmwB98';
$accountCode = 'pmg360';
$accountId = '10938';

$object = new Ongage($userName,$password,$accountCode);


echo "<pre>";
// print_r($object->contactActivity());
$response = $object->contactActivity();

if (isset($response) && $response->metadata->error != true) {

	foreach ($response->payload as $value) {
		// $fileResponse = $object->sendRequest();
		// var_dump($fileResponse);



		$url 	 =	'https://api.ongage.net/api/contact_activity/'.$value->id.'/export';

	    $headers = [
			'X_USERNAME:'.$userName,
			'X_PASSWORD:'.$password,
			'X_ACCOUNT_CODE:'.$accountCode,
	        'Content-Type: application/json',
	    ];

	
    	$ch = curl_init();
    	curl_setopt($ch, CURLOPT_URL, $url);
    	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    	$result = curl_exec($ch);
    	$resoponse = $result;
		
		curl_close($ch);

		$list =array_chunk(explode(',',$resoponse),18);

		$file = fopen("contacts.csv","w");

		foreach ($list as $line) {

		  fputcsv($file, $line);
		  
		}

		fclose($file);



	}

}
echo "</pre>";


/*
if (isset($_GET['export']) && !empty($_GET['export'])) {
	
	$response = $object->exportRetrieve($_GET['export']);
	
	echo $response;

}else{

	$response = $object->exportSegmentContacts([1011808742]);
	
	$id = $response->id;
	
	echo '<a href="?export='.$id.'">'.$id.'</a>';

}
/*

Getting all aggregate activity gmail contacts

https://api.ongage.net/api/aggregate_activity

{
 "title": "Active gmail contacts",
 "selected_fields": [ "sent", "opens", "clicks", "unsubscribes", "complaints" ],
 "group_fields": [ "email" ],
 "filters": {
   "criteria": [
      {
        "type": "email",
        "field_name": "email",
        "operator": "_LIKE",
        "operand": [
          "gmail.com"
        ],
        "case_sensitive": 0,
        "condition": "and"
      }
   ],
   "user_type":"active"
 }
}