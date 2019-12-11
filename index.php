<?php

ini_set('display_errors', 1);
ini_set('memory_limit', '-1');

require './Ongage.php';

$userName = 'kurt.martin@pmg360.com';

$password ='cP46kBUNdSmwB98';

$accountCode = 'pmg360';

$accountId = '10938';



if (isset($_GET['contact_report']) && $_GET['contact_report'] == 1) {
	
	echo "Export started at : ".date('h:i:s').'<br>';
	
	$segmentsIds=[];

	$campansIds = [];

	$object = new Ongage($userName,$password,$accountCode);

	$segmentsResponse = $object->getSegments();


	$campainsResponse = $object->getCampaign();

	// echo "<pre>";
	// print_r($campainsResponse);
	// echo "</pre>";
	// die;

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

	// die();
	
	$data = [];

	if (isset($_GET['active']) && $_GET['active'] == 1) {
		$data[] = 'active';
	}

	if (isset($_GET['unjoin-member']) && $_GET['unjoin-member'] == 1) {
		$data[] = 'unjoin-member';
	}

	if (isset($_GET['clicked']) && $_GET['clicked'] == 1) {
		$data[] = 'clicked';
	}

	if (isset($_GET['opened']) && $_GET['opened'] == 1) {
		$data[] = 'opened';
	}

	if (isset($_GET['inactive']) && $_GET['inactive'] == 1) {
		$data[] = 'inactive';
	}

	if (isset($_GET['bounced']) && $_GET['bounced'] == 1) {
		$data[] = 'bounced';
	}

	if (isset($_GET['complaint']) && $_GET['complaint'] == 1) {
		$data[] = 'complaint';
	}


	if (empty($data)) {
		
		$data = ['bounced'];

	}
	
	// print_r($data);
	// die;
	$retrieveFile = $object->exportContactReport($campansIds , $segmentsIds,$data);
	
	if (isset($retrieveFile) && !empty($retrieveFile->id)) {
	
		$object->exportContactReportRetrieve($retrieveFile->id,$data[0]);
	
		echo " Export finished at : ".date('h:i:s').'<br>';

	}
	

}


