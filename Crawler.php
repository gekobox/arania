<?php
namespace arania;

class Crawler{
	
	private $mUrl="";
	private $cookieDir="/tmp";
	private $cookieFile="";
	private $acceptCookie=false;
	private $previuosInteractionData;
	private $userAgent="";
	private $header= array();
	private $curlOpt;
	
	
	function __construct($url){
		$this->mUrl= $url;		
	}
	
	function setUrl($url){
		$this->mUrl= $url;
	}
	
	function acceptCookie($accept=false, $cookiePath=""){
		$this->acceptCookie=$accept;
		$this->cookieFile=time();
		if($cookiePath !== ""){
			$this->cookieDir= $cookiePath;
		}
	}
	
	/**
	 * Send and receive data before starting to extract useful data 
	 */
	
	function previousInteraction($arrayData){
		$this->previuosInteractionData= $arrayData;
	}
	
	function setUserAgent($ua){
		$this->userAgent= $ua;
	}
	
	function setHeader($headerArray){
		$this->header= $headerArray;
	}
	
	/**
	 * Set the crawler options with the corresponding CURLOPT_XXX options
	 * @param array $curlOptions specify the key/value Array with the format: array(CURLOPT_XXX =>"value")
	 */
	function setOpt($curlOptions){
		$this->curlOpt= $curlOptions;
	}
	
	/**
	 * Run the crawler
	 * @return string String of the crawled url 
	 */
	function run(){
		//run the  crawler
		if($this->mUrl === ""){
			echo "empty url";
			return;
		}			
		$curl= curl_init();
		curl_setopt($curl, CURLOPT_URL, $this->mUrl);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		
		
		if($this->userAgent !== "")	
			curl_setopt($curl, CURLOPT_USERAGENT, $this->userAgent);
		if($this->acceptCookie)
			curl_setopt($curl, CURLOPT_COOKIEFILE, $this->cookieDir."/".$this->cookieFile);
		if(count($this->header) > 0)
			curl_setopt($curl, CURLOPT_HTTPHEADER, $this->header);
		
		foreach ($this->curlOpt as $option=>$value){
			curl_setopt($curl, $option, $value);
		}
		
		$result= curl_exec($curl);
		if($result === false)
			$result= curl_error($curl);
		
		curl_close($curl);
		
		//echo $result;		
		
		return $result;			
	}	
	
}