<?php
namespace arania;

use arania\Crawler;
use arania\Exporter;
use DOMDocument;
use DOMXPath;
use Exception;

class Scraper{
	
	private $document="";	
	private $nextControl="";
	private $nextControlAttrib="";
	private $totalPages=1;
	private $currentPage=1;
	private $mCrawler;
	
	function __construct($crawler){

		$this->document= new DOMDocument();
		$this->document->preserveWhiteSpace=false;
		libxml_use_internal_errors(true);
		$this->mCrawler= $crawler;		
	}
	
	/**
	 * Load the DOMDocument with the specified url
	 * @param string $url Url to load
	 */
	function loadDomDocument($url=""){
		if($url !== "")
			$this->mCrawler->setUrl($url);
		
		$result= $this->mCrawler->run();
		$this->document->loadHTML('<?xml version="1.0" encoding="utf-8"?>'.$result);
	}
	
	/**
	 * Enable navigation through pages using "Next Page" option in a website 
	 * @param unknown $nextPageTag Tag attribute to match in the format ([class|id]=><attribute name>)
	 * @param number $pagesToExtract Number of pages to extract or 0 to extract to the las page
	 */
	function paginationControl($nextPageTag, $pagesToExtract=1){
		$pagination= explode(":", $nextPageTag);
		$this->nextControlAttrib= $pagination[0];
		$this->nextControl= $pagination[1];
		$this->totalPages= $pagesToExtract;
	}
	
	/**
	 * fieldsToExtract format:
	 * 		fields= [
	 * 			"field name"=>"class[> child_class]",
	 * 		]
	 * @param array $fieldsToExtract Array containing the fields to extract
	 * @param string $format csv|json|xml Format to extract, CSV by default
	 */
	function extractData($fieldsToExtract, $format="csv"){
		
		if($this->nextControl ===""){
			$this->totalPages=1;
		}
		
		if($this->totalPages < 0){
			throw new Exception("totalPages must be greater than 0");
			return; 
		}
		
		$dataPages= array();
		
		do{
			
			$data= array();
			$this->loadDomDocument();
				
			$nextControlFinder= new DOMXPath($this->document);
			$nextControlNode=false;
			switch ($this->nextControlAttrib){
				case "id":
					
					break;
				case "class":
					$nextControlNode=$nextControlFinder->query('//*[contains(concat(" ",normalize-space(@class), " "), " ' . $this->nextControl . ' ")]/a/@href');
					break;
			}
			
			
			
			foreach($fieldsToExtract as $field=>$class){
				
				$fieldFinder= new DOMXPath($this->document);
				$classAttribute= explode(":", $class);
				$queryString="";
				
				if (count($classAttribute) > 1){
					switch ($classAttribute[1]){
						case "link":
							$queryString.= $this->buildNestedClassQuery($classAttribute[0]).'/a/@href';
							break;
						
					}
					
				}
				else{
					$queryString= $this->buildNestedClassQuery($class);
				}
					
				
				$foundData= $fieldFinder->query($queryString);
				//echo $field.":=".$queryString."<br>";
				//echo $foundData->length;
				$data[$field]= $foundData;
			}
			//echo html_entity_decode($data["campo"]->item(0)->nodeValue);
			
			$this->currentPage++;
			if($nextControlNode){
				//$this->loadDomDocument($nextControlNode->item(0)->nodeValue);
				echo "nextControlNode ".$nextControlNode->item(0)->nodeValue."<br>";
			}			
			$dataPages[]= $data;
			
		}while(( $this->totalPages ==0 ||$this->currentPage <= $this->totalPages) 
				&& $nextControlNode !== false);
		
		echo "pages extracted: ".($this->currentPage - 1);
		
		$formattedData="";
		
		switch($format){
			case 'csv':
				echo "data length: ".count($dataPages);
				$formattedData= Exporter::exportCSV($dataPages);
				break;
			case 'json':
					
				break;
		}
		
		return $formattedData;
		
	}

	/**
	 * Build a query for the DOMXPath object
	 * @param string $class Field class to extract
	 * @return string Query for the DOMXPath object
	 */
	function buildNestedClassQuery($class){
		$classTree= explode(">", $class);
		$queryString="";
		foreach ($classTree as $nodeClass){
			$queryString.= '//*[contains(concat(" ",normalize-space(@class), " "), " ' . trim($nodeClass) . ' ")]';
		}
		
		return $queryString;		
	}
		
}