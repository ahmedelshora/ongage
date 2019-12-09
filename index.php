<?php

ini_set('display_errors', 1);

require './Ongage.php';

$userName = 'kurt.martin@pmg360.com';
$password ='cP46kBUNdSmwB98';
$accountCode = 'pmg360';
$accountId = '10938';

$object = new Ongage($userName,$password,$accountCode);

echo "<pre>";
// print_r($object->sendRequest('lists/78606'));
// print_r($object->getSentEmails(7065537));
// print_r($object->getContacts(1059953335));
echo "</pre>";
// die;
print_r($object->getList());

die;
echo "<pre>";
print_r($object->getReport());
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