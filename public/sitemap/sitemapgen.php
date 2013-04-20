<?php
/*#!/usr/bin/php*/ //moved here becasue some of scripts using session in CLI mode

error_reporting(E_ALL);

define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../../application'));
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));


set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    realpath(APPLICATION_PATH). '/controllers',
    realpath(APPLICATION_PATH). '/Obj',
    realpath(APPLICATION_PATH). '/layouts',
    realpath(APPLICATION_PATH),
    get_include_path(),
)));



/** Zend_Application */
require_once 'Zend/Application.php';

require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->setFallbackAutoloader(true);

include APPLICATION_PATH.'/constants/user-types.php';

$objINI = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);

$dbparams = $objINI->resources->db->params->toArray();

$db = new Zend_Db_Adapter_Pdo_Mysql($dbparams);
Zend_Db_Table_Abstract::setDefaultAdapter($db);
$translit = new Zend_Filter_Translit();

$myFile = "testFile.txt";
$fh = fopen($myFile, 'w') or die("can't open file");
$stringData = "Bobby Bopper\n";
fwrite($fh, $stringData);
$stringData = "Tracy Tanner\n";
fwrite($fh, $stringData);
fclose($fh);

/**/
$xml_string = '<?xml version="1.0" encoding="UTF-8"?>
<urlset
  xmlns="http://www.google.com/schemas/sitemap/0.84"
  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
  xsi:schemaLocation="http://www.google.com/schemas/sitemap/0.84
                      http://www.google.com/schemas/sitemap/0.84/sitemap.xsd">';
/**/
$xml_string .=  "<url>
  <loc>http://masterholiday.net/faq/iventor/</loc>
  <priority>0.50</priority>
	</url>
	<url>
	  <loc>http://masterholiday.net/index/all-post/</loc>
	  <priority>0.50</priority>
	</url>
	<url>
	  <loc>http://masterholiday.net/static/how-this-work-client/</loc>
	  <priority>0.50</priority>
	</url>
	<url>
	  <loc>http://masterholiday.net/static/rules/</loc>
	  <priority>0.50</priority>
	</url>
	<url>
	  <loc>http://masterholiday.net/static/agreement/</loc>
	  <priority>0.50</priority>
	</url>
	<url>
	  <loc>http://masterholiday.net/static/how-this-work-iventor/</loc>
	  <priority>0.50</priority>
	</url>
	<url>
	  <loc>http://masterholiday.net/faq/</loc>
	  <priority>0.50</priority>
	</url>";

/**/

$posts = new Obj_DBTable_Posts();
$all = $posts->getAllPost();
foreach ($all as $p) {
$loc = "http://masterholiday.net/post/".$p['Id']."/".$translit->filter($p['Title'])."/";
$xml_string .= " <url>
  <loc>$loc</loc>
  <priority>0.50</priority>
 </url>\n";
}


$cat = new Obj_DBTable_IventorServices();
$uniqueCats = $cat->getAllUniqueCategories();
foreach ($uniqueCats as $t) {
$cat = new Obj_DBTable_ServiceCategories(); 
$alias = $cat->getAlias($t['CategoryId']);
$loc = "http://masterholiday.net/catalog/".$alias."/";
$xml_string .= " <url>
  <loc>$loc</loc>
  <priority>0.90</priority>
 </url>\n";
}


$cat = new Obj_DBTable_IventorServices();
$uniqueCats = $cat->getAllUniqueCategories();
foreach ($uniqueCats as $t) {
$cat = new Obj_DBTable_ServiceCategories(); 
$alias = $cat->getAlias($t['CategoryId']);
$Obj_ivent_service = new Obj_DBTable_IventorServices();
$ivent_service = $Obj_ivent_service->getAllCitiesByIventorId($t['CategoryId']);
$Obj_city = new Obj_DBTable_Citys();
$Obj_cat = new Obj_DBTable_ServiceCategories();
	foreach ($ivent_service as $service){
					$city = $Obj_city->getCity($service['CityId']);
					$cityalias = $Obj_city->getAlias($service['CityId']);
					$cat = $Obj_cat->getAlias($t['CategoryId']);
					$loc = "http://masterholiday.net/catalog/".$alias."/".$cityalias."/";
					$xml_string .= " <url>
					  <loc>$loc</loc>
					  <priority>0.90</priority>
					 </url>\n";			
//echo $loc."<br>";					 
					}

}

$invinfo = new Obj_DBTable_InventorInfo();
$infos = $invinfo->getFullIventorsList();

foreach ($infos as $u) {
					//echo "http://masterholiday.net/iventor/".$u->Id."/".$translit->filter($u->CompanyName)."/<br>";
					$loc = "http://masterholiday.net/iventor/".$u->Id."/".$translit->filter($u->CompanyName)."/";
					$xml_string .= " <url>
					  <loc>$loc</loc>
					  <priority>0.80</priority>
					 </url>\n";					
					}   


$xml_string .= "</urlset>";
$sitemap_file = "sitemap.xml";
if(!$hndl = fopen($sitemap_file,'w')) {
		print "Can't open sitemap file - '$sitemap_file'.\nDumping result to screen...\n<br /><br /><br />\n\n\n";
		print '<textarea rows="25" cols="70" style="width:100%">'.$xml_string.'</textarea>';
	} else {
	//print '<textarea rows="25" cols="70" style="width:100%">'.$xml_string.'</textarea>';
		fwrite($hndl,$xml_string);
		fclose($hndl);
		print '<p>Sitemap was written to <a href="http://masterholiday.net/sitemap/'.$sitemap_file .'">'.$sitemap_file .'></a></p>';
		
	}



die();



