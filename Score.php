<?php 
error_reporting(0);
require_once('SQLHelper.php');
require_once ('../vendor/autoload.php');
use \SEOstats\Services\Social as Social;
use \SEOstats\Services as SEOstats;

class Score {

	private $sql_obj = null;
	public $api_key = "AIzaSyD_cq0k8zTyEYlZYOhld9FSP9dYm_G_71A";

	public function __construct(){

		$this->sql_obj = SQLHelper::get_instance();
	}


	public function allscore($url,$place_id){

		$info['seo']=$this->seo($url);
		$info['social']=$this->social($url);
		$info['performance']=$this->performance($url);
		$info['reputation']=$this->reputation($place_id);
		$info['shark']=$this->shark($place_id);
		$info['marketing']=$this->marketing($url);

		$return['seo'] =  $info['seo']['grade'];
		$return['social'] =  $info['social']['grade'];
		$return['performance'] =  $info['performance']['grade'];
		$return['reputation'] =  $info['reputation']['grade'];
		$return['shark'] =  $info['shark']['grade'];
		$return['marketing'] =  $info['marketing']['grade'];
		
		echo json_encode($return);

	}

	public function seo($url){
		
		$tags = get_meta_tags($url);
		$seostats = new \SEOstats\SEOstats;
		$return = array();

   		if ($seostats->setUrl($url)) {

        	$return['google_seo_count']=SEOstats\Google::getPageRank();
            $return['google_site_index']=SEOstats\Google::getSiteindexTotal();
            $return['google_backlinks']=SEOstats\Google::getBacklinksTotal();
            $return['google_total_search']=SEOstats\Google::getSearchResultsTotal($url);
        	$return['alexa_seo_count']=SEOstats\Alexa::getGlobalRank();
        	$return['alexa_month_rank']=SEOstats\Alexa::getMonthlyRank();
        	$return['alexa_week_rank']=SEOstats\Alexa::getWeeklyRank();
        	$return['alexa_daily_rank']=SEOstats\Alexa::getDailyRank();
        	$return['alexa_back_link']=SEOstats\Alexa::getBacklinkCount();
        	$return['alexa_load_time']=SEOstats\Alexa::getPageLoadTime();
        	
        	$return['alexa_trafficgraph_one']=SEOstats\Alexa::getTrafficGraph(1);
        	$return['alexa_trafficgraph_two']=SEOstats\Alexa::getTrafficGraph(2);
        	$return['alexa_trafficgraph_third']=SEOstats\Alexa::getTrafficGraph(3);
        	$return['alexa_trafficgraph_four']=SEOstats\Alexa::getTrafficGraph(4);
        	$return['alexa_trafficgraph_five']=SEOstats\Alexa::getTrafficGraph(5);
        	$return['alexa_trafficgraph_six']=SEOstats\Alexa::getTrafficGraph(6);

        	$listMobileInfo= $this->getMobileFriendlyResult($url);
        	$return['mobile_friendliness'] = $listMobileInfo['mobile_friendliness'];
         	$return['mobile_viewport'] = $listMobileInfo['mobile_viewport'];
        	$return['font_legibility'] = $listMobileInfo['font_legibility'];
        	$return['content_to_viewport'] = $listMobileInfo['content_to_viewport'];
        	$return['sizeprox_of_links'] = $listMobileInfo['sizeprox_of_links'];

        	$gaChecker= $this->getGoogleAnalyticsChecker($url);
        	$return['ga_checker'] = $gaChecker['ga_checker'];

        	$listDomainRank= SEOstats\SemRush::getDomainRank();
        	$return['est_expenses_on_site'] = $listDomainRank['Ac'] ? $listDomainRank['Ac']: 0 ;  
            $return['num_keywords_on_search'] = $listDomainRank['Ad'] ? $listDomainRank['Ad']: 0; 
            $return['est_visitors'] = $listDomainRank['At'] ? $listDomainRank['At']: 0;  
            $return['domain_name'] = $listDomainRank['Dn'] ? $listDomainRank['Dn']: 0;  
            $return['est_potetial_client'] = $listDomainRank['Oa'] ? $listDomainRank['Oa']: 0;  
            $return['est_income_same_client'] = $listDomainRank['Oc'] ? $listDomainRank['Oc']: 0; 
            $return['est_competitors_organic'] = $listDomainRank['Oo'] ? $listDomainRank['Oo']: 0;
            $return['est_visitors_2'] = $listDomainRank['Ot'] ? $listDomainRank['Ot']: 0;  
            $return['sem_rush_rating'] = $listDomainRank['Rk'] ? $listDomainRank['Rk']: 0;
        	$return['semrush_competitors'] = SEOstats\SemRush::getCompetitors();
            $return['semrush_graph_one'] = SEOstats\SemRush::getDomainGraph();
            $return['semrush_graph_two'] = SEOstats\SemRush::getDomainGraph();
        	$return['open_site_explorer'] = SEOstats\OpenSiteExplorer::getPageMetrics();
            $return['meta_keyword']=isset($tags['keywords'])? $tags['keywords']: "No Meta Keyword";
            $return['meta_description']=isset($tags['description'])?$tags['description']:"No Meta Description";
            $return['bing_backlinks']=0;
            $return['dmoz']= Social::getDmozListing();
            $return['sistrix'] = SEOstats\Sistrix::getVisibilityIndex();
        	$return['grade']= "A";
        	$return['status']= "Ok";

  		}else{
  			$return['grade'] = "-";
  			$return['status']= "Error";	
  		}
		
		return $return;
	}

	public function social($url){

    	$seostats = new \SEOstats\SEOstats;
    	$return = array();

    	if ($seostats->setUrl($url)) {

    		$return['linkedin'] = Social::getLinkedInShares();
    		$return['pinterest'] = Social::getPinterestShares();
    		$return['facebook'] = Social::getFacebookShares();
    		$return['twitter'] = Social::getTwitterShares();
    		$return['googleplus'] = Social::getGooglePlusShares();

    		$return['delicious'] = Social::getDeliciousShares();
    		$return['xing'] = Social::getXingShares();
    		$return['digg'] = Social::getDiggShares();
    		$return['xkontakt'] = Social::getVKontakteShares();
    		$return['stumble'] = Social::getStumbleUponShares();

        	$return['totaleachcount'] = $return['linkedin']+
        								$return['pinterest']+
        								$return['facebook']+
        								$return['twitter']+
        								$return['googleplus'];

        	$return['grade'] = $this->getSocialGrade($return['totaleachcount']);
        	$return['status']= "Ok";
   		}else{
   			$return['grade'] = "-";
   			$return['status']= "Error";
   		}

		return $return;

	}

	public function performance($url){

		$ch = curl_init($url); 
   		curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
		curl_exec($ch); 
		if(!curl_errno($ch)){ 
   			$return = curl_getinfo($ch);
 	 		$return['grade']=$this->getPerformanceGrade($return['total_time']);
 	 		$return['status'] = "Ok";
  		}else{
  			$return['grade'] = "-";
  		    $return['status'] = "Error";
  	 	}
		return $return;
	}


	public function reputation($place_id){

		$placedetails_url = "https://maps.googleapis.com/maps/api/place/details/json?placeid=".$place_id."&key=".$this->api_key."";

		$return= array();
		$eachreviewratings = 0;
		$numberofreviews = 0;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL,$placedetails_url);
		$content = curl_exec($ch);
		$row =json_decode($content,true);

		if (isset($row['result']['reviews'])){

			foreach ($row['result']['reviews'] as $value) {
				$eachreviewratings=$value['rating']+$eachreviewratings;
				$numberofreviews++;
				$return['rating'][] = $value['rating'];
				$return['comment'][] = $value['text'];
			}
			 
			$return['grade'] = $this->getReputationGrade($eachreviewratings/$numberofreviews);
			$return['status']="Ok";

		}else{

  			$return['grade'] = "-";
			$return['status']="No Reviews";

		}

		return $return;
	}


	public function shark($place_id){

		$seostats = new \SEOstats\SEOstats;
        $placedetails_url = "https://maps.googleapis.com/maps/api/place/details/json?placeid=".$place_id."&key=".$this->api_key."";

        $return= array();
        $eachreviewratings = 0;
        $numberofreviews = 0;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL,$placedetails_url);
        $content = curl_exec($ch);
        $row =json_decode($content,true);

        if ($seostats->setUrl($row['result']['website'])) {
                $list= SEOstats\SemRush::getDomainRank();
                $est_ads_expenses= $list['Ac'] ? $list['Ac'] : 0;
                if($est_ads_expenses == 0){
                    $return['marketing_status']="No";
                    $return['marketing_review']="No Marketing Budget";
                }else{
                    $return['marketing_status']="Yes";
                    $return['marketing_review']="Yes Marketing Budget";
                }
        }       

        if (isset($row['result']['reviews'])){

            foreach ($row['result']['reviews'] as $value) {
                
                if($value['rating'] >= 1 && $value['rating'] <= 3){
                    $eachreviewratings=$value['rating']+$eachreviewratings;
                    $numberofreviews++;
                    $return['rating'][] = $value['rating'];
                    $return['comment'][] = $value['text'];
                    $return['status']="Ok";             
                }

            }
            if($numberofreviews == 0){
                $return['status']="No Negative Reviews";
            }

            if($numberofreviews == 0 || $return['marketing_status'] == "No"){
                $return['grade']="No";
            }else{
                $return['grade']="Yes";
            }

        }else{
            $return['grade'] = "No";         
            $return['status']="No Reviews";
        }
        
		return $return;

	}

	public function marketing($url){
 		$seostats = new \SEOstats\SEOstats;
        $return = array();
        if ($seostats->setUrl($url)) {
                $list= SEOstats\SemRush::getDomainRank();
                $return['est_ads_expenses'] = $list['Ac'] ? $list['Ac'] : 0;
                $return['num_of_keyword']   = $list['Ad'] ? $list['Ad'] : 0;
                $return['est_num_visitors'] = $list['At'] ? $list['At'] : 0;
                $return['est_cost_purchase_through_ads'] = $list['Oc'] ? $list['Oc'] : 0;

                if($return['est_ads_expenses'] != 0 && $return['num_of_keyword'] != 0 &&  $return['est_num_visitors'] != 0 && $return['est_cost_purchase_through_ads'] != 0 ){
                    $return['grade'] = "Yes";
                    $return['status'] = "Ok";
                }else{
                    $return['grade'] = "No";
                    $return['status'] = "Ok";     
                }
        }else{    
                $return['grade'] = "No";
                $return['status'] = "Error";
        }
		return $return;
	}



	//functions to grade of each output to A B C D F
	public function getSocialGrade($grade){

		$return;

		if ($grade >= 3000){
		   $return = "A";
		}else
		if ($grade >= 1000){
			$return = "B";
		}else
		if ($grade >= 100){
			$return = "C";
		}else
		if ($grade >= 50){
			$return = "D";
		}else
		if ($grade >= 10){
			$return = "F";
		}else
		if ($grade >= 0){
			$return = "F";
		}

		return $return;
	}

	public function getPerformanceGrade($grade){

		$return;

		if ($grade >= 0 && $grade <= 1){
			$return = "A";
		}else
		if ($grade >= 1 && $grade <= 2){
			$return = "B";
		}else
		if ($grade >= 2 && $grade <= 3){
			$return = "C";
		}else
		if ($grade >= 3 && $grade <= 4){
			$return = "D";
		}else
		if ($grade >= 4){
			$return = "F";
		}

		return $return;
	}

	public function getReputationGrade($grade){

		$return;

		if ($grade >= 5 || $grade >= 4){
			$return = "A";
		}else
		if ($grade >= 3){
			$return =  "B";
		}else
		if ($grade >= 2){
			$return =  "C";
		}else
		if ($grade >= 1){
			$return =  "D";
		}else
		if ($grade >= 0){
			$return =  "F";
		}

		return $return;
	}

	public function getSharkGrade($grade){

		$return;

		if ($grade >= 5 || $grade >= 4){
			$return = "F";
		}else
		if ($grade >= 3){
			$return =  "D";
		}else
		if ($grade >= 2){
			$return =  "C";
		}else
		if ($grade >= 1){
			$return =  "B";
		}else
		if ($grade >= 0){
			$return =  "A";
		}

		return $return;
	}

	public function getMobileFriendlyResult($url){
		$whois_url = "https://www.googleapis.com/pagespeedonline/v3beta1/mobileReady?url=".$url;
   		$ch = curl_init();
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    	curl_setopt($ch, CURLOPT_URL,$whois_url);
    	$content = curl_exec($ch);
    	$row =json_decode($content,true);

    	$return['mobile_friendliness'] = "Awesome! Your site is ".$row['ruleGroups']['USABILITY']['score']."% mobile-friendly";

    	$return['mobile_viewport'] = $row['formattedResults']['ruleResults']['ConfigureViewport']['ruleImpact']."% Mobile viewport not set";

    	$return['font_legibility'] = $row['formattedResults']['ruleResults']['UseLegibleFontSizes']['ruleImpact']."% Text too small to read issue";
    
    	$return['content_to_viewport'] = $row['formattedResults']['ruleResults']['SizeContentToViewport']['ruleImpact']."% Content wider than screen issue";

    	$return['sizeprox_of_links'] = $row['formattedResults']['ruleResults']['SizeTapTargetsAppropriately']['ruleImpact']."% Links too close together issue";

    	return $return;
	}


	public function getGoogleAnalyticsChecker($url){
		$return = array();
		$options = array(
	        CURLOPT_RETURNTRANSFER => true,     // return web page
	        CURLOPT_HEADER         => false,    // don't return headers
	        CURLOPT_FOLLOWLOCATION => true,     // follow redirects
	        CURLOPT_ENCODING       => "",       // handle all encodings
	        CURLOPT_USERAGENT      => "rachmar", // who am i
	        CURLOPT_AUTOREFERER    => true,     // set referer on redirect
	        CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
	        CURLOPT_TIMEOUT        => 120,      // timeout on response
	        CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
	        CURLOPT_SSL_VERIFYPEER => false     // Disabled SSL Cert checks
    	);
		$ch = curl_init($url);
		curl_setopt_array($ch, $options);
		$content = curl_exec($ch); 
		$ua_regex = "/UA-[0-9]{5,}-[0-9]{1,}/";
		$s   =  preg_match_all($ua_regex, $content, $ua_id);
		if ($s == 0){
			$return['ga_checker'] = "NOT_FOUND";
		}else{
			$return['ga_checker'] = "FOUND";
		}
		return $return;
	}
}

