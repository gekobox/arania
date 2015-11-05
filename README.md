#arania
Write web crawling applications with simple sintax and export extracted data in diferent formats
 - CSV
 - JSON
 - XML

#Requirement

You must have [composer](https://getcomposer.org) installed on your system

# Installation
## New project
Create a composer project inside your /webroot dir (for web application) or anywhere else (for command line application)

> composer create-project -s dev arania/arania <project name>

## Add to existing composer project

```json
{
    "require": {
        "arania/arania": "1.*"
    }
}
``` 
# Usage
 
arania is based on two components: the Crawler wich obtains the data and the Scraper wich extracts the content
 
## Basic extraction
Import the namespace classes
 
```php
use arania\Crawler;
use arania\Scraper;
```

Create a Crawler object with the target URL
```php
 $crawler= new Crawler("the/target/url");
```
 
Create a Scraper object and pass the Crawler instance to it
 
```php
$scraper= new Scraper($crawler);
```
 
Define the fields to extract in a key/value array with the format:
```
array("custom field name"=>"tag class name")
```

Example
```php
$fields= array("Description"=>"desc");
```
Or get a class specifying its path in the class hierarchy

Example
```php
//Get a class wich is child of another class
$fields= array("Description"=>"parent_class>child_class");
``
 
Finally call extractData() method from Scraper instance and pass the fields to extract array and the return format
> extractData(array(),"csv|json|xml")
 
```php
$extractedContent= $scraper->extractData($fields) // by default it returns csv format
 ```

###Full code
```php
use arania\Crawler;
use arania\Scraper;

$crawler= new Crawler("the/target/url");
$scraper= new Scraper($crawler);
$fields= array(
   "Descripction"=>"desc",
   "Price"=>"info>price"
 );
$extractedContent= $scraper->extractData($fields) // by default it returns csv format

```
