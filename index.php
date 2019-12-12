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


	$data = [];
	
	$allCampains = 1; 

	if (isset($_GET['all_campain']) && $_GET['all_campain'] == 0) {

		$allCampains = 0 ;

	}

	

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
	

	foreach ($segmentsResponse as $item) {
		if (!in_array($item->id,$segmentsIds)) {
			$segmentsIds[] = $item->id;
			
			if ($allCampains == 0) {

				exportFunction($object,$data,[],[$item->id],$item->name);

			}

		}

	}


	foreach ($campainsResponse as $item) {
		
		if ($allCampains == 0) {

			exportFunction($object,$data,[],[$item->id],$item->name);

		}

		if (!in_array($item->id,$campansIds)) {
			$campansIds[] = $item->id;
		}
	}

	if ($allCampains == 1) {

		exportFunction($object,$data,$campansIds , $segmentsIds,'all_');

	}

}

echo " Export finished at : ".date('h:i:s').'<br>';



// this function run every campain or segment or all

function exportFunction($object,$data,$campansIds , $segmentsIds,$fileNameStart = ''){

	$retrieveFile = $object->exportContactReport($campansIds , $segmentsIds,$data,$fileNameStart);
	
	if (isset($retrieveFile) && !empty($retrieveFile->id)) {
	
		$object->exportContactReportRetrieve($retrieveFile->id,$data[0],$fileNameStart);

	}
	
}
