<?php
include_once("functions.php");

$postURL = $_POST["url"];
$postTable = $_POST["table"];
$postRequest = $_POST["request"];

if($postRequest == 1){
	echo URLtoArticle($postURL);
}
else if($postRequest == 2){
	echo analyzeArticle(URLtoArticle($postURL), $postURL, $postTable);
} 
