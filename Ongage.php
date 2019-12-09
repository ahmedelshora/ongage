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
		// $resoponse = json_decode($result);
		curl_close($ch);

		return json_decode($resoponse);
	    
	}

	// GET LIST IDs from segments 
	public function getList(){

		$lists =$this->sendRequest('segments');
		
		if (isset($lists->metadata->error) && !empty($lists->metadata->error) && $lists->metadata->error == true) {
			die ('An error occured');
		}

		$listids=[];

		foreach ($lists->payload as $list) {
			if (!in_array($list->list_id,$listids)) {
				$listids[] = $list->list_id;
			}
		}
		
		$this->listids = $listids;

		return $listids;

	}

	// get list ids from list url

	public function getListIds(){

		$lists =$this->sendRequest('lists');
		
		if (isset($lists->metadata->error) && !empty($lists->metadata->error) && $lists->metadata->error == true) {
			die ('An error occured');
		}

		$listids=[];

		foreach ($lists->payload as $list) {
			if (!in_array($list->id,$listids)) {
				$listids[] = $list->id;
			}
		}
		
		$this->listids = $listids;

		return $listids;

	}
	
	// GET LIST IDs from campains
	public function getCampaign($id = ''){
		
		$response =$this->sendRequest('mailings/'.$id);
		
		
		if (isset($response->metadata->error) && !empty($response->metadata->error) && $response->metadata->error == true) {
			die ('An error occured');
		}


		return $response->payload;

	}




	// GET Sent Emails 
	public function getSentEmails($id=''){

		$response =$this->sendRequest('emails/'.$id);
		
		if (isset($response->metadata->error) && !empty($response->metadata->error) && $response->metadata->error == true) {
			die ('An error occured');
		}


		return $response->payload;

	}
	

	///GET REPORT
	
	public function getReport($listIds = []){

		// check if he send list ids set this->listIds with that ids 
		if (isset($listIds) && !empty($listIds)) {
			$this->listids = $listIds;
		}
		
		// forcing to set list ids 			
		if (empty($this->listids)) {
			die('please set list ids');
		}

		$selectDataOfList = [


				   "listids" => $this->listids,
				    "select" => [
      					"record_date",
						"active",
						"not_active",
						"complaints",
						"unsubscribes",
						"bounces",
						"opened",
						"clicked",
						"no_activity"
				    ],
				    // "filter" => [
				    //     [ "stats_date", ">=", "2019-01-01" ]
				    // ],
				    
				    "from" => "list",
				 //    "group" => [
				    	
					//     "mailing_type",
					//     "event_id",
					//     "mailing_id",
					//     "mailing_instance_id",
					//     "mailing_name",
					//     "event_name",
					//     "email_message_id",
					//     "email_message_name"
					    
					// ],
				 //    "order" => []

		];

		$selectDataOfMailng = [

				   "listids" => $this->listids,
				    "select" => [
									"segment_id",
									"segment_name",
				      				"stats_date",
				      				"delivery_date",
								    "mailing_type",
								    "mailing_id",
								    "mailing_instance_id",
								    "mailing_name",
								    "event_name",
								    "email_message_id",
								    "email_message_name",
								    "sum(`sent`)",
								    "sum(`success`)",
								    "sum(`opens`)",
								    "sum(`clicks`)",
								    "sum(`hard_bounces`)",
								    "sum(`soft_bounces`)",
								    "sum(`unsubscribes`)",
								    "sum(`complaints`)"
				    			],
				    "filter" => [
				        			[ "stats_date", ">=", "2019-01-01" ]
				    			],
				    
				    "from" => "mailing",
				    "group" => [
				    	
					    "mailing_type",
					    "event_id",
					    "mailing_id",
					    "mailing_instance_id",
					    "mailing_name",
					    "event_name",
					    "email_message_id",
					    "email_message_name"
					    
					],
				    "order" => []
				];

			$results = $this->sendRequest('reports/query','post',$selectDataOfMailng);

			if (isset($results->metadata->error) && !empty($results->metadata->error) && $results->metadata->error == true) {
				die ('An error occured'.print_r($results,1));
			}
			
			// var_dump($results->payload);
			return $results->payload;
	}


	public function exportSegmentContacts($segmentIds = []){
		$data = [
			"name" => "___".time(),
			"date_format" => "mm/dd/yyyy",
			 "file_format" => "csv",
			 "segment_id" =>$segmentIds,
			 /*"status" => [
			  "active",
			  "inactive"
			 ]*/
		];

		$resoponse = $this->sendRequest('export','post',$data);

		return  $resoponse->payload;


	}


	public function exportRetrieve($id){
		
		$resoponse =  $this->sendRequest('segments/'.$id.'/export_retrieve');
		
		if ($resoponse == null) {
			foreach ($this->getList() as $list) {
				$resoponse .= '<a href="https://connect.ongage.net/78606/list/serve_export/'.$id.'">'.$id.'</a><br>';
			}
		
		}else{

			return 'proccessing';

		}
		
		return $resoponse;
	}


	public function contactActivity(){
		
		$response = $this->sendRequest('contact_activity');
		
		return $response;

	}

}