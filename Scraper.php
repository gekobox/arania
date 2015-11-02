<?php
namespace arania;

use arania\Crawler;
use DOMDocument;
use DOMXPath;

class Scraper{
	
	private $document="";
	private $keyField=""; // use to count the number of rows
	
	function __construct($crawler){

		$this->document= new DOMDocument();
		$this->document->validateOnParse=true;
		$this->document->preserveWhiteSpace=false;
		libxml_use_internal_errors(true);
		$this->document->loadHTML($crawler->run());
	}
	
	/**
	 * fieldsToExtract format:
	 * 		fields= [
	 * 			"field name"=>"class",
	 * 		]
	 * @param array $fieldsToExtract
	 */
	function extractData($fieldsToExtract, $format="csv"){
		$data= array();
		foreach($fieldsToExtract as $field=>$class){
			if($this->keyField ==="")
				$this->keyField= $field;
			
			$fieldFinder= new DOMXPath($this->document);
			$classAttribute= explode(":", $class);
			$queryString="";
			
			if (count($classAttribute) > 1){
				switch ($classAttribute[1]){
					case "link":
						$queryString= '//*[contains(concat(" ",normalize-space(@class), " "), " ' . $classAttribute[0] . ' ")]/a/@href';
						break;
					
				}
				
			}
			else{
				$queryString= '//*[contains(concat(" ",normalize-space(@class), " "), " ' . $class . ' ")]';
			}
				
			
			$foundData= $fieldFinder->query($queryString);
			//echo $field.":=".$queryString."<br>";
			//echo $foundData->length;
			$data[$field]= $foundData;
		}
		//echo $data["respuesta"]->item(0)->nodeValue;
		
		$formattedData="";
		
		switch($format){
			case 'csv':
				$formattedData= $this->getCsvFormat($data);
				break;
			case 'json':
				
				break;
		}
		
		return $formattedData;
		
	}

	function getCsvFormat($data){
		$dataString="";
		$rowsCount= $data[$this->keyField]->length;
		
		foreach ($data as $field=>$class){
			$dataString.= $field.";";
		}
		$dataString.= "\n";
		
		for($i=0; $i < $rowsCount; $i++){
			foreach ($data as $field){
				if($field->item($i) !== null)
					$dataString.= trim($field->item($i)->nodeValue);
				$dataString .= ";";
			}
			//echo $dataString."<br>";
			$dataString.= "\n";
		}
		
		return $dataString;
	}	
}