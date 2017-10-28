<?php

class Routes {

	public static function executeRoute($route) {
		$modulePath = ucwords($route["module"]).".php";
		$module = strtolower($route["module"]);
		$action = strtolower($route["action"]);

		if(!file_exists($modulePath)){
			Routes::handleError();
		}

		require_once($modulePath);
		Routes::executeAction($module, $action);
	}

	protected static function executeAction($module, $action) {
		$endpoint = "/".$module."/".$action;
		if($module == "admin"){
			$action = new Admin();
		}else 
		if($module == "search") { 
			$action = new Search();
		}else
		if($module == "score"){ 
			$action = new Score();
		}else
		if($module == "statics") { 
			$action = new Statics();
		}else{
            $action = new SomethingElse();
		}
		
		switch ($endpoint) {

			/*
			** THIS CONSIST OF ROUTES FROM ENDPOINT URL
			** DO NOT ENTER COSTLY PROCESSES
			** THIS WILL ONLY CALL CORRESPONDING METHODS IN CLASS
			** OR PARSING $_POST / $_GET / $_REQUEST PARAMETERS OR ISSET VALIDATION
			** FOR CLASS INPUT PARAMETERS
			*/

			//Admin Class
			case '/admin/new_fitd':
					$action->new_fitd($_POST['data']);
				break;

			case '/admin/google_analytics':
					$action->google_analytics($_POST['data']);
				break;

			case '/admin/email_capture':
					$action->email_capture($_POST['data']);
				break;
			//End of Admin


			//Search Class
			case '/search/test':
					$action->test();
				break;

			case '/search/googlesearch':
					$action->googlesearch($_POST['data']);
				break;	
			//End of Search
				

			//Static Class
			case '/statics/register':
					$action->register($_POST['data']);
				break;
			
			case '/statics/sendmailjet':
					$action->sendmailjet($_POST['data']);
				break;

			case '/statics/login':
					$action->login($_POST['data']);
				break;

			case '/statics/changepassword':
					$action->changepassword($_POST['client_id'],$_POST['old_password'],$_POST['new_password']);
				break;

			case '/statics/forgotpassword':
					$action->forgotpassword($_POST['email']);
				break;

			case '/statics/getsignature':
					$action->getsignature($_POST['client_id']);
				break;
				
			case '/statics/changesignature':
					$action->changesignature($_POST['client_id'],$_POST['signature']);
				break;

			case '/statics/uploadlogo':
					$action->uploadlogo();
				break;

			case '/statics/getpersonalinfo':
					$action->getpersonalinfo($_POST['client_id']);
				break;

			case '/statics/updatepersonalinfo':
					$action->updatepersonalinfo($_POST['personalinfo'],$_POST['client_id']);
				break;
			//End of Static


			//Client Class
			case '/client/dashboard':
					$action->dashboard($_POST['client_id']);
				break;

			case '/client/fullreport':
					$action->fullreport($_POST['url_id'],$_POST['client_id']);
				break;	

			case '/client/viewreport':
					$action->viewreport($_POST['compscore'],$_POST['client_id']);
				break;	

			case '/client/insertreport':
					$action->insertreport($_POST['client_id'],$_POST['compscore'],$_POST['compdata']);
				break;

			case '/client/insertlead':
					$action->insertlead($_POST['client_id'],$_POST['name'],$_POST['email'],$_POST['phone'],$_POST['url_id']);
				break;

			case '/client/insertsearch':
					$action->insertsearch($_POST['client_id'],$_POST['compscore']);
				break;

			case '/client/prevsearch':
					$action->prevsearch($_POST['client_id']);
				break;

			case '/client/prevreport':
					$action->prevreport($_POST['client_id']);
				break;

			case '/client/searchlead':
					$action->searchlead($_POST['client_id'],$_POST['address'],$_POST['keyword']);
				break;

			case '/client/whois':
					$action->whois($_POST['url']);
				break;

			case '/client/mysearch':
					$action->mysearch($_POST['client_id']);
				break;

			case '/client/myreport':
					$action->myreport($_POST['client_id']);
				break;

			case '/client/mylead':
					$action->mylead($_POST['client_id']);
				break;

			// End of Client
					
			// Score CLass 

			case '/score/allscore':
					$action->allscore($_POST['url'],$_POST['place_id']);
				break;	

			case '/score/seo':
					$action->seo($_POST['url']);
				break;

			case '/score/social':
					$action->social($_POST['url']);
				break;

			case '/score/performance':
					$action->performance($_POST['url']);
				break;	

			case '/score/reputation':
					$action->reputation($_POST['place_id']);
				break;

			case '/score/shark':
					$action->shark($_POST['place_id']);
				break;

			case '/score/marketing':
					$action->marketing($_POST['url']);
				break;
					
			// End of Score

			default:
				Routes::handleError();
				break;
		}
	}

	public static function handleError() {
		http_response_code(404);
		$response = array();
		$response["error"] = "Resource Not Found";
		die(json_encode($response));
	}
}