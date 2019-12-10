<?php
/**
 * 
 */
class Ongage 
{
	const BASEURL='https://api.ongage.net/api/';
	protected $userName='';
	protected $password='';
	protected $accountCode='';
	protected $listids;

	function __construct($userName,$password,$accountCode)
	{
		$this->userName = $userName;
		$this->password = $password;
		$this->accountCode =$accountCode;
		
	}

	public function sendRequest($url,$methodType = 'get'/*post or get*/,$params = []){

		$url 	 =	self::BASEURL.$url;
		
	    $headers = [
			'X_USERNAME:'.$this->userName,
			'X_PASSWORD:'.$this->password,
			'X_ACCOUNT_CODE:'.$this->accountCode,
	        'Content-Type: application/json',
	    ];

	
    	$ch = curl_init();
    	curl_setopt($ch, CURLOPT_URL, $url);
    	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		if (isset($methodType) && $methodType == 'post') {
    		curl_setopt($ch, CURLOPT_POST, false);
    	}
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    	if (isset($methodType) && $methodType == 'post') {
    		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
    	}

    	$result = curl_exec($ch);
    	$resoponse = $result;
		curl_close($ch);

		return json_decode($resoponse);
	    
	}

	// GET segments 
	public function getSegments(){

		$response = $this->sendRequest('segments');		

		if (isset($response->metadata->error) && !empty($response->metadata->error) && $response->metadata->error == true) {
			
			die ('An error occured');
		
		}

		return $response->payload;

	}

	// GET  campains
	public function getCampaign($id = ''){

		$response = $this->sendRequest('mailings');
		
		if (isset($response->metadata->error) && !empty($response->metadata->error) && $response->metadata->error == true) {
			
			die ('An error occured');
		
		}

		return $response->payload;

	}


	// export report for contacts with status [] using campain id as mailed_id or by segment_id

	public function exportContactReport($mailingId = [],$segmentId = [],$status = [ 'active', 'unjoin-member', 'clicked', 'opened', 'inactive', 'bounced', 'complaint']){

		if (empty($mailingId) && empty($segmentId)) {
			
			die("please enter array of mailing id or segment id ");

		}

		$data =[
			 "name" => $status[0]."_".time().'_'.date('Y-m-d'),
			 "date_format" => "mm/dd/yyyy",
			 "file_format" => "csv",
			 "mailing_id" => $mailingId,
			 "segment_id" => $segmentId,
			 "status" =>  $status,
			 "fields_selected" => 
			 [
				  "email",
				  // "ip",
				  // "status",
				  "sub_date"
			],
		];

		$response = $this->sendRequest('export','post',$data);

		if (isset($response->metadata->error) && !empty($response->metadata->error) && $response->metadata->error == true) {
			
			die ('An error occured');
		
		}

		return $response->payload;


	}


	// export report for contacts with status [] using campain id as mailed_id or by segment_id

	public function exportContactReportRetrieve($reportId,$fileName = 'report_'){

		$headers = [
			'X_USERNAME:'.$this->userName,
			'X_PASSWORD:'.$this->password,
			'X_ACCOUNT_CODE:'.$this->accountCode,
	        'Content-Type: application/json',
	    ];

		$uri = "segments/".$reportId."/export_retrieve";
		
		$url = self::BASEURL.$uri;

		$checkIfReportFinished = $this->sendRequest($uri);

		if (isset($checkIfReportFinished) && !empty($checkIfReportFinished->metadata->error) && $checkIfReportFinished->metadata->error == true) {

			$this->exportContactReportRetrieve($reportId,$fileName='bounced');

			return false;
		}

		

		
		$ch = curl_init($url); 

		$dir = './'; 

		$file_name = $fileName.'_'.time().'_'.date('Y-m-d'); 
		$save_file_loc = $dir . $file_name;
		$fp = fopen($save_file_loc, 'wb'); 
		// It set an option for a cURL transfer 
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_FILE, $fp); 
		curl_setopt($ch, CURLOPT_HEADER, 0); 
		// Perform a cURL session 
		$result = curl_exec($ch);
		// Closes a cURL session and frees all resources 
		curl_close($ch); 
		// Close file 
		fclose($fp); 

		return $file_name;

	}

	// GET LIST IDs from segments 
	// public function getList(){

	// 	$lists =$this->sendRequest('segments');
		
	// 	if (isset($lists->metadata->error) && !empty($lists->metadata->error) && $lists->metadata->error == true) {
	// 		die ('An error occured');
	// 	}

	// 	$listids=[];

	// 	foreach ($lists->payload as $list) {
	// 		if (!in_array($list->list_id,$listids)) {
	// 			$listids[] = $list->list_id;
	// 		}
	// 	}
		
	// 	$this->listids = $listids;

	// 	return $listids;

	// }

	// get list ids from list url

	// public function getListIds(){

	// 	$lists =$this->sendRequest('lists');
		
	// 	if (isset($lists->metadata->error) && !empty($lists->metadata->error) && $lists->metadata->error == true) {
	// 		die ('An error occured');
	// 	}

	// 	$listids=[];

	// 	foreach ($lists->payload as $list) {
	// 		if (!in_array($list->id,$listids)) {
	// 			$listids[] = $list->id;
	// 		}
	// 	}
		
	// 	$this->listids = $listids;

	// 	return $listids;

	// }
	
	



	// // GET Sent Emails 
	// public function getSentEmails($id=''){

	// 	$response =$this->sendRequest('emails/'.$id);
		
	// 	if (isset($response->metadata->error) && !empty($response->metadata->error) && $response->metadata->error == true) {
	// 		die ('An error occured');
	// 	}


	// 	return $response->payload;

	// }
	

	// ///GET REPORT
	
	// public function getReport($listIds = []){

	// 	// check if he send list ids set this->listIds with that ids 
	// 	if (isset($listIds) && !empty($listIds)) {
	// 		$this->listids = $listIds;
	// 	}
		
	// 	// forcing to set list ids 			
	// 	if (empty($this->listids)) {
	// 		die('please set list ids');
	// 	}

	// 	$selectDataOfList = [


	// 			   "listids" => $this->listids,
	// 			    "select" => [
 //      					"record_date",
	// 					"active",
	// 					"not_active",
	// 					"complaints",
	// 					"unsubscribes",
	// 					"bounces",
	// 					"opened",
	// 					"clicked",
	// 					"no_activity"
	// 			    ],
	// 			    // "filter" => [
	// 			    //     [ "stats_date", ">=", "2019-01-01" ]
	// 			    // ],
				    
	// 			    "from" => "list",
	// 			 //    "group" => [
				    	
	// 				//     "mailing_type",
	// 				//     "event_id",
	// 				//     "mailing_id",
	// 				//     "mailing_instance_id",
	// 				//     "mailing_name",
	// 				//     "event_name",
	// 				//     "email_message_id",
	// 				//     "email_message_name"
					    
	// 				// ],
	// 			 //    "order" => []

	// 	];

	// 	$selectDataOfMailng = [

	// 			   "listids" => $this->listids,
	// 			    "select" => [
	// 								"segment_id",
	// 								"segment_name",
	// 			      				"stats_date",
	// 			      				"delivery_date",
	// 							    "mailing_type",
	// 							    "mailing_id",
	// 							    "mailing_instance_id",
	// 							    "mailing_name",
	// 							    "event_name",
	// 							    "email_message_id",
	// 							    "email_message_name",
	// 							    "sum(`sent`)",
	// 							    "sum(`success`)",
	// 							    "sum(`opens`)",
	// 							    "sum(`clicks`)",
	// 							    "sum(`hard_bounces`)",
	// 							    "sum(`soft_bounces`)",
	// 							    "sum(`unsubscribes`)",
	// 							    "sum(`complaints`)",
	// 							    "unique_opens"
	// 			    			],
	// 			    "filter" => [
	// 			        			[ "stats_date", ">=", "2019-01-01" ]
	// 			    			],
				    
	// 			    "from" => "mailing",
	// 			    "group" => [
				    	
	// 				    "mailing_type",
	// 				    "event_id",
	// 				    "mailing_id",
	// 				    "mailing_instance_id",
	// 				    "mailing_name",
	// 				    "event_name",
	// 				    "email_message_id",
	// 				    "email_message_name"
					    
	// 				],
	// 			    "order" => []
	// 			];

	// 		$results = $this->sendRequest('reports/query','post',$selectDataOfMailng);

	// 		if (isset($results->metadata->error) && !empty($results->metadata->error) && $results->metadata->error == true) {
	// 			die ('An error occured'.print_r($results,1));
	// 		}
			
	// 		// var_dump($results->payload);
	// 		return $results->payload;
	// }


	// public function exportSegmentContacts($segmentIds = []){
	// 	$data = [
	// 		"name" => "___".time(),
	// 		"date_format" => "mm/dd/yyyy",
	// 		 "file_format" => "csv",
	// 		 "segment_id" =>$segmentIds,
	// 		 /*"status" => [
	// 		  "active",
	// 		  "inactive"
	// 		 ]*/
	// 	];

	// 	$resoponse = $this->sendRequest('export','post',$data);

	// 	return  $resoponse->payload;


	// }


	// public function exportRetrieve($id){
		
	// 	$resoponse =  $this->sendRequest('segments/'.$id.'/export_retrieve');
		
	// 	if ($resoponse == null) {
	// 		foreach ($this->getList() as $list) {
	// 			$resoponse .= '<a href="https://connect.ongage.net/78606/list/serve_export/'.$id.'">'.$id.'</a><br>';
	// 		}
		
	// 	}else{

	// 		return 'proccessing';

	// 	}
		
	// 	return $resoponse;
	// }


	// public function contactActivity(){
		
	// 	$response = $this->sendRequest('contact_activity');
		
	// 	return $response;

	// }

}