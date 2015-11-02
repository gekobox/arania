<?php
require __DIR__ . '/vendor/autoload.php';

use arania\Crawler;
use arania\Scraper;

//$crawler= new Crawler("http://stackoverflow.com/questions/6366351/getting-dom-elements-by-classname");
//$crawler= new Crawler("http://localhost/dummy.html");
$crawler= new Crawler("http://www.amazon.com/gp/bestsellers/electronics/281052/ref=s9_ri_gw_clnk/185-2243211-2423035?pf_rd_m=ATVPDKIKX0DER&pf_rd_s=desktop-2&pf_rd_r=1G15XTXKMS4TJMYRD5JV&pf_rd_t=36701&pf_rd_p=2091268582&pf_rd_i=desktop");
$scraper= new Scraper($crawler);
$fields= array("campo"=>"zg_title",
				"nose"=>"zg_price",
				"enlace"=>"zg_title:link"
		
);
//echo $scraper->extractData($fields);
$fileContent= $scraper->extractData($fields);

//echo $fileContent;
$fileHandler= fopen("fileContent.csv","w");
if(!$fileHandler)
if(fwrite($fileHandler, $fileContent) === false)	
fclose($fileHandler);