<?php
include_once("functions.php");

$postURL = explode(PHP_EOL, $_POST["url"]);
$postTable = $_POST["table"];

foreach ($postURL as $url){
	if(strlen($url)>0){
		analyzeArticle(URLtoArticle($url), $url, $postTable);
	}
}
 
