<?php 
include('../lib/Stripe.php');
require '../mailjet/autoload.php';
require_once('SQLHelper.php');
use \Mailjet\Resources;

class Statics {

	private $sql_obj = null;

	public function __construct(){
		$this->sql_obj = SQLHelper::get_instance();
	}



	public function login($data){

		$return = array();
		$client = json_decode($data,true);
        $query = "SELECT * FROM client_user WHERE client_email = '".$client['client_email']."'
                                            AND client_password = '".md5($client['client_password'])."'";

        $query_result = $this->sql_obj->SELECT($query);
        if (mysqli_num_rows($query_result) > 0){
            while($row = mysqli_fetch_assoc($query_result)){
                $return['id'] = $row['client_id'];
                $return['firstname'] = $row['client_firstname'];
                $return['lastname'] = $row['client_lastname'];
                $return['msg'] = "Successfully Login";
                $return['status'] = true;
            }
        }else{
                $return['msg'] = "No Records Found";
                $return['status'] = false;
        }
        echo json_encode($return);
	}


	public function sendmailjet($data){
		$apikey = 'bb737f6bb9f23762294eb6d0ad6fb293';
		$apisecret = '7b1decaeac44f711b2bc9cfc05adde77';
		$mj = new \Mailjet\Client($apikey, $apisecret);
		$body = [
		    'FromEmail' => "ricardo.emong@gmail.com",
		    'FromName' => "Mailjet Test",
		    'Subject' => "Your email flight plan!",
    		'Text-part' => "Dear passenger, welcome to Mailjet! May the delivery force be with you!",
		    'Recipients' => [['Email' => "sescongene@gmail.com"]]
		];
		$response = $mj->post(Resources::$Email, ['body' => $body]);
		$response->success() && var_dump($response->getData());
	}


	public function register($data){

		$client = json_decode($data,true);

	    $default_signature = "<h4>".$client['client_firstname']." ".$client['client_lastname']."</h4><br/>
	    					  <h5>".$client['client_email']."</h5><br/>
	     					  <h6>".$client['client_address']."</h6>";

		$ins_client = "
			INSERT INTO client_user SET
			client_firstname = '".$client['client_firstname']."',
			client_lastname = '".$client['client_lastname']."',
			client_address = '".$client['client_address']."',
			client_birthday	= '".$client['client_birthday']."',
			client_country = '".$client['client_country']."',
			client_email = '".$client['client_email']."',
			client_password	 = '".md5($client['client_password'])."',
			client_contact	 = '".$client['client_contact']."',
			client_signature  = '".urlencode($default_signature)."'

		";
		Stripe::setApiKey("sk_test_q4ZOPSfBdNM32BBEFIHy7YcR");

		try {
    		Stripe_Charge::create(array("amount" => 9999,
                                		"currency" => "usd",
                                		"card" => $client['stripeToken'],
								        "description" => "The payment was made by ".$client['client_email'])
                               	);
    		$return['stripe_status'] = "Ok";
  		}catch (Exception $e){
			$return['stripe_status'] = "Error:".$e;
  		}

		if($this->sql_obj->SELECT($ins_client) === TRUE){
			$return['status'] = true;
		}else{
			$return['status'] = false;
		}

		echo json_encode($return);
		
	}

	public function changepassword($client_id,$old_password,$new_password){

		$sel_search = "
			SELECT * FROM client_user WHERE
			client_id = '".$client_id."' AND
			client_password = '".md5($old_password)."'
		";	
		$res_client = $this->sql_obj->SELECT($sel_search);

		if (mysqli_num_rows($res_client) > 0) {
    		while($row = mysqli_fetch_assoc($res_client)){
    		$sel_update = "
				UPDATE client_user SET client_password='".md5($new_password)."'
				WHERE client_id= '".$client_id."'
			";
			$sel_client = $this->sql_obj->SELECT($sel_update);
			$return['status'] = "Password is succesfully changed";
    		}
    	}else{
			$return['status'] = "No Record";
		}	

		echo json_encode($return);

	}


	public function forgotpassword($email){

		$sel_search = "
			SELECT * FROM client_user WHERE
			client_email = '".$email."'
		";
		$res_client = $this->sql_obj->SELECT($sel_search);

	    if (mysqli_num_rows($res_client) > 0) {
    		while($row = mysqli_fetch_assoc($res_client)){
			$new_password = "adminadmin";
			$sel_update = "
				UPDATE client_user SET client_password='".md5($new_password)."'
				WHERE client_email= '".$row['client_email']."'
			";
			$sel_client = $this->sql_obj->SELECT($sel_update);
			$to = $row['client_email'];
			$subject = "Shark Leads Forgot Password";

			$txt = '<!DOCTYPE html>
			<html>
    		<body style="padding: 0; margin: 0; width: 700px; font-family: Arial; font-size: 16px">
    			<div style="height: 50px; background-color: #2d2d2d; padding: 45px">
   			    </div>
        		<div style="height: 3px; background-color: #0066ff;"></div>
        		<div style="height: 35-; background-color: #c5bfbf; padding: 50px 70px 80px 70px">
            	<div style="height: 350; background-color: white; padding: 20px 80px">
        	<center>
            <h1 style="">Forgot Password!</h1>
            Your password have been changed. Please change your password immediately.
            <br><br>
            This is your new password: <br><br>
            <p style="background-color: #0066ff; border: none; color: white; padding: 10px 64px; text-align: center; text-decoration: none; display: inline-block; font-size: 18px; margin: 4px 2px; cursor: pointer; font-weight: bolder; border-radius: 50px;">'.$new_password.'</p>
            <br><br>
            Thank you from Lead Shark!<br><br>            
            <br><br>
        	</center>
    		</div>
			</div>
			<div style="height: 3px; background-color: #0066ff;"></div>
			<div style="height: 40px; background-color: #2d2d2d;"></div>
			</body>
			</html>
			';

			$headers = "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
			$headers .= "From: sharleadsinfo@no-reply.com". "\r\n";;
			mail($to,$subject,$txt,$headers);
			$return['msg'] = "Email Sent";
            $return['status'] = true;
            }
		}else{
			$return['msg'] = "No Record Found";
            $return['status'] = false;
		}

		echo json_encode($return);
	}
	
    public function getsignature($client_id){

    	$sel_search = "
			SELECT * FROM client_user WHERE
			client_id = '".$client_id."'
		";
		$res_client = $this->sql_obj->SELECT($sel_search);
	    if (mysqli_num_rows($res_client) > 0){
	    	$row = mysqli_fetch_assoc($res_client);
    		$return['client_signature'] = urldecode($row['client_signature']);
			$return['status'] = "ok";
	    }else{
			$return['status'] = "No Record";
		}
		echo json_encode($return);
    }

    public function changesignature($client_id,$signature){

    	$sel_search = "
			UPDATE client_user  SET client_signature='".urlencode($signature)."'
			WHERE client_id='".$client_id."'
		";
		$res_client = $this->sql_obj->SELECT($sel_search);
	    $return['client_id'] = $client_id;
    	$return['signature'] = $signature;
    	$return['status'] = "Ok";
		echo json_encode($return);
    }

    public function uploadlogo(){

      		$return= array();
      		$client_id = $_POST['client_id'];
      		$file_name =  $_FILES['file_data']['name'];
      		$file_tmp  =  $_FILES['file_data']['tmp_name'];
      		$file_type =  $_FILES['file_data']['type'];

      	if(isset($file_name)){
        	if(empty($return)==true){
         		move_uploaded_file($file_tmp,"../uploads/".$file_name);
         	    $logo_url = "http://".$_SERVER['HTTP_HOST']."/leadgen/uploads/".$file_name;
				$ins_client = "UPDATE client_user  SET client_logo='".urlencode($logo_url)."'
								WHERE client_id='".$client_id."'
							   ";
				$res_client = $this->sql_obj->SELECT($ins_client);
				$return['1'] = $ins_client;
            	$return['status'] ='You succcesfully upload the logo';
      		}else{
         		$return['status'] ='Error on uploading files';
      		} 
      	}else{
      		$return['status'] ='image was not set';
      	}
      	echo json_encode($return);
    }

    public function getpersonalinfo($client_id){

    	$sel_search = "
			SELECT * FROM client_user WHERE
			client_id = '".$client_id."'
		";
		$res_client = $this->sql_obj->SELECT($sel_search);
	    if (mysqli_num_rows($res_client) > 0){
	    	$row = mysqli_fetch_assoc($res_client);
    		$return['client_firstname'] = $row['client_firstname'];
    		$return['client_lastname'] = $row['client_lastname'];
    		$return['client_address'] = $row['client_address'];
    		$return['client_birthday'] = $row['client_birthday'];
    		$return['client_country'] = $row['client_country'];
    		$return['client_email'] = $row['client_email'];
    		$return['client_contact'] = $row['client_contact'];
    		$return['client_signature'] = urldecode($row['client_signature']);
    		$return['client_logo'] = urldecode($row['client_logo']);
			$return['status'] = "Ok";
	    }else{
			$return['status'] = "No User Found";
		}

		echo json_encode($return);
    }

    public function updatepersonalinfo($personalinfo,$client_id){

    	$client = json_decode($personalinfo,true);

    	$sel_search = "
			UPDATE client_user  SET client_firstname ='".$client['firstname']."',
									client_lastname ='".$client['lastname']."',
									client_address='".$client['address']."',
									client_birthday='".$client['birthday']."',
									client_country='".$client['country']."',
									client_email='".$client['email']."',
									client_contact='".$client['contact']."'
									WHERE client_id='".$client_id."'
		";
		$res_client = $this->sql_obj->SELECT($sel_search);
		$return['1'] = $personalinfo;
    	$return['status'] = "Ok";
 		echo json_encode($return);
    }

}



