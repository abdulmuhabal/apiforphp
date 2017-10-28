<?php 
require_once('SQLHelper.php');

class Search {

	private $sql_obj = null;

	public $api_key = "AIzaSyD_cq0k8zTyEYlZYOhld9FSP9dYm_G_71A";

	public function __construct(){
		$this->sql_obj = SQLHelper::get_instance();
	}

	public function test(){
		echo "string";
	}

	public function googlesearch($data){

		$parameters = json_decode($data,true);
		print_r($parameters);
        $latlng=$this->getGoogleLatLng($parameters['location']);
		$latitude = $latlng['lat'];
		$longtitude = $latlng['lng'];
		$place_url = "https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=".$latitude.",".$longtitude."&radius=50000&keyword=".$parameters['keyword']."&key=".$this->api_key."";

		$return = array();
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL,$place_url);
		$content = curl_exec($ch);
		$row =json_decode($content,true);
		foreach ($row['results'] as $value) {
			$return[]=$this->getGooglePlaceDetails($value['place_id']);
		}
		echo json_encode($return);	
	}

	public function getGooglePlaceDetails($place_id){

		$placedetails_url = "https://maps.googleapis.com/maps/api/place/details/json?placeid=".$place_id."&key=".$this->api_key."";

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

	public function getGoogleLatLng($address){

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

}