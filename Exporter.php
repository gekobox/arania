<?php
namespace arania;

class Exporter{
	static function exportCSV($dataPages){
		$dataString="";
		foreach ($dataPages as $data){
			$rowsCount= $data[key($data)]->length;
				
			foreach ($data as $field=>$class){
				$dataString.= $field.";";
			}
			$dataString.= "\n";
				
			for($i=0; $i < $rowsCount; $i++){
				foreach ($data as $field){
					if($field->item($i) !== null){
						$dataString.= html_entity_decode(trim($field->item($i)->nodeValue));
						//echo html_entity_decode($field->item($i)->nodeValue)."<br>";
					}
					$dataString .= ";";
				}
				//echo $dataString."<br>";
				$dataString.= "\n";
			}
		}
		return "\xEF\xBB\xBF".$dataString;	//utf-8 with BOM
	}
}