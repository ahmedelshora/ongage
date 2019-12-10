<?php

ini_set('display_errors', 1);
ini_set('memory_limit', '-1');

require './Ongage.php';

$userName = 'kurt.martin@pmg360.com';
$password ='cP46kBUNdSmwB98';
$accountCode = 'pmg360';
$accountId = '10938';

$segmentsIds=[];
$campansIds = [];

$object = new Ongage($userName,$password,$accountCode);

$segmentsResponse = $object->getSegments();

$campainsResponse = $object->getCampaign();

foreach ($segmentsResponse as $item) {
	if (!in_array($item->id,$segmentsIds)) {
		$segmentsIds[] = $item->id;
	}
}


foreach ($campainsResponse as $item) {
	if (!in_array($item->id,$campansIds)) {
		$campansIds[] = $item->id;
	}
}


$retrieveFile = $object->exportContactReport($campansIds , $segmentsIds);

if (isset($retrieveFile) && !empty($retrieveFile->id)) {
	$object->exportContactReportRetrieve($retrieveFile->id);
	echo "export finished at : ".date('h:i:s');
}


