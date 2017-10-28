<?php 
require_once('SQLHelper.php');
include('Score.php');
require_once ('../vendor/autoload.php');
use \SEOstats\Services\Social as Social;
use \SEOstats\Services as SEOstats;

class Client {

	private $sql_obj = null;
	private $score_obj = null;

	public $api_key = "AIzaSyD_cq0k8zTyEYlZYOhld9FSP9dYm_G_71A";

	public function __construct(){
		$this->sql_obj = SQLHelper::get_instance();
		$this->score_obj = new Score();
	}

	public function whois($url){

		$whois_url = "https://www.enclout.com/api/v1/whois/show.json?auth_token=RevqF9TvHj9Fj81cUSjL&url=http%3A%2F%2Fmycodingtricks.com%2Fphp%2F2-ways-to-count-facebook-likes-shares-and-comments-using-php%2F";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL,$whois_url);
		$content = curl_exec($ch);
		$row =json_decode($content,true);
		echo json_encode($row);

	}

	public function dashboard($client_id){

		$return = array();
	  	$return['search_metric'] = $this->getSearchMetric($client_id);
	    $return['report_metric'] = $this->getReportMetric($client_id);
	    $return['lead_metric'] = $this->getLeadMetric($client_id);
	    $return['business_metric'] = $this->getBusinessMetric($client_id);
	  	echo json_encode($return);
	}

	public function fullreport($url_id,$client_id){

		$url_id = urlencode($url_id);
		$sel_search = "
			SELECT * FROM client_report WHERE
			report_url_id = '".$url_id."' ORDER BY report_id DESC
		";
		$res_client = $this->sql_obj->SELECT($sel_search);
	    if (mysqli_num_rows($res_client) > 0) {
    		while($row = mysqli_fetch_assoc($res_client)){
	    		$return['establishment'] = urldecode($row['report_compscore']);
	    		$return['data'] = urldecode($row['report_data']);
	    	    $return['dialog'] = urldecode($row['report_dialog']);
	    		$return['url'] = $row['report_url'];
	    		$return['created_date'] = $row['report_date'];
	    	    $userinfo = $this->getUserInfo($client_id);
	    	    $return['signature'] = $userinfo['signature'];
	    	    $return['logo'] = $userinfo['logo'];
	    	    $return['created_by'] = $userinfo['name'];
	    	    $return['phone'] = $userinfo['phone'];
	    	    $return['email'] = $userinfo['email'];
	    		$return['status'] = "Ok";
            }
		}else{
				$return['status'] = "No Record";
		}
		echo json_encode($return);	
	}


	public function viewreport($compscore,$client_id){

		$return = array();
		$report_data= json_decode($compscore,true);
	
			$seo_result = $this->score_obj->seo($report_data['website']);
    		$social_result = $this->score_obj->social($report_data['website']);
    		$performance_result = $this->score_obj->performance($report_data['website']);
    		$reputation_result = $this->score_obj->reputation($report_data['place_id']);
    		$shark_result = $this->score_obj->shark($report_data['place_id']);
    		$marketing_result = $this->score_obj->marketing($report_data['website']);

    		$return['business'] = $report_data;
   	 		$return['seo'] = $seo_result ;
    		$return['social'] = $social_result ;
    		$return['performance'] = $performance_result ;
    		$return['reputation'] = $reputation_result ;
    		$return['shark'] = $shark_result ;
    		$return['marketing'] = $marketing_result;
    		$userinfo = $this->getUserInfo($client_id);
	    	$return['signature'] = $userinfo['signature'];
	        $return['logo'] = $userinfo['logo'];
	    	$return['created_by'] = $userinfo['name'];
	    	$return['phone'] = $userinfo['phone'];
	    	$return['email'] = $userinfo['email'];
    		$return['status'] = "Ok";

    	echo json_encode($return);	
	}


	public function mysearch($client_id){

		$sel_search = "
			SELECT * FROM client_search WHERE
			search_client_id = '".$client_id."' ORDER BY search_id DESC
		";
		$res_client = $this->sql_obj->SELECT($sel_search);
	    if (mysqli_num_rows($res_client) > 0) {
    		while($row = mysqli_fetch_assoc($res_client)){
    			$return['keyword'] = $row['search_keyword'];
				$return['address'] = $row['search_address'];
				$return['created_date'] = $row['search_timestamp'];
	    		$return['establishment'] = urldecode($row['search_establishment']);
	    		$return['status'] = "Ok";
	    		$list[]=$return;
            }
		}else{
				$return['status'] = "No Record";
			    $list[]=$return;

		}

		echo json_encode($list);	
	}

	public function myreport($client_id){

		$sel_search = "
			SELECT * FROM client_report WHERE
			report_client_id = '".$client_id."' ORDER BY report_id DESC
		";
		$res_client = $this->sql_obj->SELECT($sel_search);
	    if (mysqli_num_rows($res_client) > 0) {
    		while($row = mysqli_fetch_assoc($res_client)){
	    		$return['establishment'] = urldecode($row['report_compscore']);
	    		$return['data'] = urldecode($row['report_data']);
	    		$return['url'] = $row['report_url'];
	    		$return['created_date'] = $row['report_date'];
	    		$return['status'] = "Ok";
	    		$list[]=$return;
            }
		}else{
				$return['status'] = "No Record";
			    $list[]=$return;

		}
		echo json_encode($list);	
	}

	public function mylead($client_id){

		$sel_search = "
			SELECT * FROM client_lead WHERE
			lead_client_id = '".$client_id."' ORDER BY lead_id DESC
		";
		$res_client = $this->sql_obj->SELECT($sel_search);
	    if (mysqli_num_rows($res_client) > 0) {
    		while($row = mysqli_fetch_assoc($res_client)){
	    		$return['name'] = $row['lead_name'];
	    		$return['email'] = $row['lead_email'];
	    		$return['url'] = $row['lead_url'];
                $return['phone'] = $row['lead_phone'];
	    	    $return['date'] = $row['lead_timestamp'];
	    		$list[]=$return;
            }
		}else{
				$return['status'] = "No Record";
			    $list[]=$return;

		}
		echo json_encode($list);
	}

	public function insertreport($client_id,$compscore,$compdata){

		$listcompdata = json_decode($compdata,true);
		$listcompscore = json_decode($compscore,true);

		$data_of_establishment = $this->getestablishmentdata($listcompscore['website'],$listcompscore['place_id']);
		$business_name = preg_replace("![^a-z0-9]+!i", "-", $listcompscore['name']);
		$report_id = $business_name.uniqid();
		$report_url = "http://".$_SERVER['HTTP_HOST']."/leadgen/#/fullreport/".$report_id;

		if ($client_id != 0){
			$ins_search = "
				INSERT INTO client_report SET
		 		report_client_id = '".$client_id."',
		 		report_compscore = '".urlencode($compscore)."',
		 		report_data= '".urlencode(json_encode($data_of_establishment))."',
		 		report_url= '".$report_url."',
		 		report_dialog= '".urlencode($compdata)."',
		 		report_url_id = '".$report_id."'
		 	";
		 		$res_client = $this->sql_obj->SELECT($ins_search);
		 		$return['fullreport'] = $report_url;
		 		$return['status'] = "Ok";
			}else{
		 				
			}
		$return['1231231231'] = $report_id;
		$return['123'] = json_encode($data_of_establishment);
		echo json_encode($return);

	}

	public function insertlead($client_id,$name,$email,$phone,$url_id){

	     $report_url = "http://".$_SERVER['HTTP_HOST']."/leadgen/#/fullreport/".$url_id;

		if ($client_id != 0){
			$ins_search = "
				INSERT INTO client_lead SET
		 		lead_name = '".$name."',
		 		lead_email = '".$email."',
		 		lead_phone = '".$phone."',
		 		lead_url = '".$report_url."',
		 		lead_client_id = '".$client_id."'
		 	";
		 		$res_client = $this->sql_obj->SELECT($ins_search);
		 		$return['status'] = "Ok";
			}else{
		 		$return['status'] = "Error";	
			}

		echo json_encode($return);
	}

	public function insertsearch($client_id,$compscore){

		if ($client_id != 0){
			$ins_search = "
				INSERT INTO client_compscore SET
		 		compscore_client_id = '".$client_id."',
		 		compscore_compscore = '".urlencode($compscore)."'
		 	";
		 		$res_client = $this->sql_obj->SELECT($ins_search);
		 		$return['status'] = "Ok";
			}else{
		 		$return['status'] = "Error";	
			}

		echo json_encode($return);	
	}

	public function prevsearch($client_id){

		$sel_search = "SELECT * FROM client_compscore WHERE
			compscore_client_id = '".$client_id."'ORDER BY compscore_id DESC LIMIT 1";

		$res_client = $this->sql_obj->SELECT($sel_search);

	    if (mysqli_num_rows($res_client) > 0) {
    		while($row = mysqli_fetch_assoc($res_client)){
	    		$return['establishment'] = urldecode($row['compscore_compscore']);
 	    		$return['status'] = "Ok";
            }
		}else{
				$return['status'] = "No Record";
		}

		echo json_encode($return);
	}

	public function prevreport($client_id){

		$sel_search = "SELECT * FROM client_report WHERE
			report_client_id = '".$client_id."'ORDER BY report_id DESC LIMIT 1";

		$res_client = $this->sql_obj->SELECT($sel_search);
	    if (mysqli_num_rows($res_client) > 0) {
    		while($row = mysqli_fetch_assoc($res_client)){
	    		$return['establishment'] = urldecode($row['report_compscore']);
	    		$return['data'] = urldecode($row['report_data']);
	    		$return['url'] = urldecode($row['report_url']);
	    		$return['created_date'] = $row['report_date'];
	    		$return['status'] = "Ok";
            }
		}else{
				$return['status'] = "No Record";
		}

		echo json_encode($return);
	}

	public function searchlead($client_id,$address,$keyword){

        $latlng=$this->getLatLng($address);
		$latitude = $latlng['lat'];
		$longtitude = $latlng['lng'];
		$place_url = "https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=".$latitude.",".$longtitude."&radius=50000&keyword=".$keyword."&key=".$this->api_key."";


		$return = array();
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL,$place_url);
		$content = curl_exec($ch);
		$row =json_decode($content,true);

		foreach ($row['results'] as $value) {
			$return[]=$this->getDetails($value['place_id']);
		}

		if (!$return) {
   			$return['status'] = "No Records";	
		} else {
			$establishment = json_encode($return);
		
		    if ($client_id != 0){
			   $ins_search = "
				INSERT INTO client_search SET
		 		search_client_id = '".$client_id."',
		 		search_keyword = '".$keyword."',
		 		search_address = '".$address."',
		 		search_establishment = '".urlencode($establishment)."'
		 	";
		 	$res_client = $this->sql_obj->SELECT($ins_search);
		 	$return['status'] = "Ok";
			}
        }
		echo json_encode($return);	
	}

	public function getDetails($place_id){

		$placedetails_url = "https://maps.googleapis.com/maps/api/place/details/json?placeid=".$place_id."&key=".$this->api_key."";

		//temp place_url
		//$placedetails_url = "http://limecodes.com/leadgen/sample/placedetails.php?place_id=".$place_id;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL,$placedetails_url);
		$content = curl_exec($ch);
		$row =json_decode($content,true);

   		$return['phone_number']= isset($row['result']['international_phone_number']) ? $row['result']['international_phone_number'] : 'NotFound';
    	$return['name']= isset($row['result']['name']) ? $row['result']['name'] : 'NotFound';
    	$return['address']= isset($row['result']['vicinity']) ? $row['result']['vicinity'] : 'NotFound';
    	$return['website']= isset($row['result']['website']) ? $row['result']['website'] : 'NotFound';
    	$return['place_id'] = $place_id;
    	return $return;
	}

	public function getLatLng($address){

		$latlng_url = "https://maps.googleapis.com/maps/api/geocode/json?address=".$address."&key=".$this->api_key."";

		$return = array();
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL,$latlng_url);
		$content = curl_exec($ch);
		$row =json_decode($content,true);

		$return['lat']=$row['results'][0]['geometry']['location']['lat'];
		$return['lng']=$row['results'][0]['geometry']['location']['lng'];
		
		return $return;		
	}

	public function getestablishmentdata($url,$place_id){

		$return = array();

		$seo_result = $this->score_obj->seo($url);
		$social_result = $this->score_obj->social($url);
		$performance_result = $this->score_obj->performance($url);
		$reputation_result = $this->score_obj->reputation($place_id);
		$shark_result = $this->score_obj->shark($place_id);
		$marketing_result = $this->score_obj->marketing($url);


		$return['seo'] = $seo_result ;
		$return['social'] = $social_result ;
		$return['performance'] = $performance_result ;
		$return['reputation'] = $reputation_result ;
		$return['shark'] = $shark_result ;
		$return['marketing'] = $marketing_result;

		return $return;
	}

	public function getReportMetric($client_id){
		$return=0;
		$sel_search = "
			SELECT * FROM client_report WHERE
			report_client_id = '".$client_id."'
		";
		$res_client = $this->sql_obj->SELECT($sel_search);
	    if (mysqli_num_rows($res_client) > 0) {
    		while($row = mysqli_fetch_assoc($res_client)){
	    		$return++;	
            }
		}else{
			    $return=0;
		}

		return $return;
	}

	public function getLeadMetric($client_id){
	    $return=0;
		$sel_search = "
			SELECT * FROM client_lead WHERE
			lead_client_id = '".$client_id."'
		";
		$res_client = $this->sql_obj->SELECT($sel_search);
	    if (mysqli_num_rows($res_client) > 0) {
    		while($row = mysqli_fetch_assoc($res_client)){
	    		$return++;	
            }
		}else{
				$return=0;
		}
		return $return;
	}

	public function getSearchMetric($client_id){
	    $return=0;
		$sel_search = "
			SELECT * FROM client_search WHERE
			search_client_id = '".$client_id."' 
		";
		$res_client = $this->sql_obj->SELECT($sel_search);
	    if (mysqli_num_rows($res_client) > 0) {
    		while($row = mysqli_fetch_assoc($res_client)){
    			$return++;	
            }
		}else{
				$return = 0;
		}

		return $return;
	}

	public function getBusinessMetric($client_id){

	    $return=0;
		$sel_search = "SELECT * FROM client_compscore WHERE
			compscore_client_id = '".$client_id."'";

		$res_client = $this->sql_obj->SELECT($sel_search);

	    if (mysqli_num_rows($res_client) > 0) {
    		while($row = mysqli_fetch_assoc($res_client)){
	    		$return++;	
            }
		}else{
				$return = 0;
		}

		return $return*20;
	}

	public function getUserInfo($client_id){

    	$sel_search = "
			SELECT * FROM client_user WHERE
			client_id = '".$client_id."'
		";
		$res_client = $this->sql_obj->SELECT($sel_search);
	    if (mysqli_num_rows($res_client) > 0){
	    	$row = mysqli_fetch_assoc($res_client);
    		$return['signature'] = urldecode($row['client_signature']);
    		$return['logo'] = urldecode($row['client_logo']);
    		$return['name'] = $row['client_firstname']." ".$row['client_lastname'];
    		$return['phone'] = $row['client_contact'];
    		$return['email'] = $row['client_email'];
	    }else{
			$return = "Error";
		}

		return $return;
    }
}


