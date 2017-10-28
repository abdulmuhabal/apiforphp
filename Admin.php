<?php 
require_once('SQLHelper.php');
// require_once ('../vendor/autoload.php');
// use \SEOstats\Services\Social as Social;
// use \SEOstats\Services as SEOstats;

class Admin {

	private $sql_obj = null;
    public  $api_key = "AIzaSyD_cq0k8zTyEYlZYOhld9FSP9dYm_G_71A";

	public function __construct(){
		$this->sql_obj = SQLHelper::get_instance();
	}

	public function test(){
        echo "string";
    }

    public function new_fitd($data){

        $parameters = json_decode($data,true);
        print_r($parameters);
     
    }
    public function google_analytics($data){

        $parameters = json_decode($data,true);

        //ex: {"websites": ["https://trello.com", "http://avfglobalsolutions.com","http://www.sushidito.com"] }

        foreach ($parameters['websites'] as $site){
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
            $ch = curl_init($site);
            curl_setopt_array($ch, $options);
            $content = curl_exec($ch); 
            $ua_regex = "/UA-[0-9]{5,}-[0-9]{1,}/";
            $s   =  preg_match_all($ua_regex, $content, $ua_id);
            if ($s == 0){
                $return[] = $site;
            }
        }
        echo json_encode($return);

    }

    public function email_capture($data){

       $parameters = json_decode($data,true);

        //ex: {"websites": ["https://trello.com", "http://avfglobalsolutions.com","http://www.sushidito.com"] }

        foreach ($parameters['websites'] as $site){
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
            $ch = curl_init($site);
            curl_setopt_array($ch, $options);
            $content = curl_exec($ch); 
            $email_regex = "/[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})/";
            $s   =  preg_match_all($email_regex, $content, $matches);
            if ($s == 0){
                $return[] = $site;
            }
        }
        echo json_encode($return);
    }

    public function performance(){
        echo "string";
    }

    public function reputation(){
        echo "string";
    }

    public function website(){
        echo "string";
    }

    public function full_report(){
        echo "string";
    }
}

