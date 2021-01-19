#!/bin/php
<?php

//MM php curl example 01/2021


//I got this from the internet a million years ago
function get_data($url){
 $ch = curl_init();
 $timeout = 5;
 curl_setopt($ch, CURLOPT_URL, $url);
 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
 curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
 curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; rv:19.0) Gecko/20100101 Firefox/19.0");
 $data = curl_exec($ch);
 curl_close($ch);
 return $data;
}

//grab gage list from csv, then turn from string to array
$gageFile =  fopen("gages.csv","r") or die ("couldn't open gage file");  
$gages = fgets($gageFile);
$gages = explode(",",$gages);

$dataFile = fopen("gageStages.txt","w") or die("Unable to open file");

foreach($gages as $gage){
 print($gage."\n");
 $url = "https://water.weather.gov/ahps2/hydrograph_to_xml.php?gage=".$gage."&output=xml";
 $content = get_data($url);

 //regular expression to grab value
 preg_match_all('/<action units=\"ft\">(.*?)<\/action>/m',$content,$action, PREG_SET_ORDER,0);
 if (!empty($action))
  $action = $action[0][1];
 else
  $action ="";
 preg_match_all('/<moderate units=\"ft\">(.*?)<\/moderate>/m',$content,$moderate, PREG_SET_ORDER,0);
 if (!empty($moderate))
  $moderate = $moderate[0][1];
 else
  $moderate="";
 preg_match_all('/<major units=\"ft\">(.*?)<\/major>/m',$content,$major, PREG_SET_ORDER,0);
 if (!empty($major))
  $major = $major[0][1];
 else
  $major = "";
 preg_match_all('/<record units=\"ft\">(.*?)<\/record>/m',$content,$record, PREG_SET_ORDER,0);
 if (!empty($record))
  $record = $record[0][1];
 else
  $record = "";
 fwrite($dataFile,$gage.",".$action.",".$moderate.",".$major.",".$record."\n");
}
fclose($dataFile);

?>

