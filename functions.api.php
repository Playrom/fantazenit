<?php

class ApiAccess{


    /**
     * Base URL of the API
     *
     * @var $apiPath String
     */
    private $apiPath;

    /*
     * Token
     */

    private $token;

    /**
     * Constructor
     *
     * @param $apiPath
     */
    public function  __construct($apiPath){
        $this->apiPath=$apiPath;
        $this->token=null;
    }

    /**
     *
     * Access to an API
     *
     * @param $endpoint String
     * @param $type String
     * @param $headerParams String[]
     * @param $data String
     * @return String
     */
    public function accessApi($endpoint,$type,$data=null){
	    	    
	    /*$filename = 'json/'.str_replace("/", "--", $endpoint).'.json';
	    
	    $fp = fopen($filename, 'r');
	    
	    if(is_file($filename) && $type == "GET"){
		    $json = fread($fp, filesize($filename));
			fclose($fp);
	    }else{*/
		
	    
	        $ch=curl_init($this->apiPath.$endpoint);
	
	        $headerParams=array();
	        $dataPost=null;
	
	        if($data!=null){
	            if(isset($data['headerParams'])){
	                $headerParams=$data['headerParams'];
	            }
	
	            if(isset($data['postParams'])){
	                $dataPost=$data['postParams'];
	
	                $json=json_encode($dataPost,true);
	                                
	                curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
	
	                $headerParams[]='Content-Type: application/json';
	                $headerParams[]='Content-Length: ' . strlen($json);
	            }
	        }
	        
	        if(strpos($endpoint, "balsick") !== false){
		        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,4); 
				curl_setopt($ch, CURLOPT_TIMEOUT, 4); //timeout in seconds
	        }
	
	        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
	
	        if($this->token!=null){
	            $headerParams[] = 'Token:'.$this->token;
	        }
	
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
	        curl_setopt($ch, CURLOPT_HTTPHEADER, $headerParams);
	
	        $json=curl_exec($ch);
	        
	        /*$fp = fopen('json/'.str_replace("/", "--", $endpoint).'.json', 'w');
			fwrite($fp, $json);
			fclose($fp);
			
		}*/

        return json_decode($json,true);
    }

    /**
     * Login with Username and Password
     *
     * @param $username String
     * @param $password String
     * @return String
     */

    public function requestToken($username,$password){

        $data=array(
            'username'=>$username,
            'password'=>$password
        );

        $result=$this->accessApi("/login","POST",array('postParams'=>$data));

        if($result['error']==true){
            error_log("Password Non Corretta");
        }else {

            if (isset($result['apiKey'])) {
                $token = $result['apiKey'];
                $this->token=$token;
            }

        }

        return $result;
    }

    public function setToken($token){
        $this->token=$token;
    }

}

?>