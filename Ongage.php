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

	public function sendRequest($url,$methodType = 'get',$params = []){

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

	// GET LIST IDs
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

		$data = [
				   "listids" => $this->listids,
				    "select" => [
				    "mailing_type",
				    "event_id",
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
				    "group" => [     "mailing_type",
				    "event_id",
				    "mailing_id",
				    "mailing_instance_id",
				    "mailing_name",
				    "event_name",
				    "email_message_id",
				    "email_message_name" ],
				    "order" => []
				];

			$results = $this->sendRequest('reports/query','post',$data);

			if (isset($results->metadata->error) && !empty($results->metadata->error) && $results->metadata->error == true) {
				die ('An error occured');
			}

			return $results->payload;
	}
}