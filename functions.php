<?php
require '/vendor/autoload.php';

//watson set up
use WatsonSDK\Common\WatsonCredential;
use WatsonSDK\Common\SimpleTokenProvider;
use WatsonSDK\Services\ToneAnalyzer;
use WatsonSDK\Services\ToneAnalyzer\ToneModel;

$usernameWatson = 'username';
$passwordWatson = 'password';

function URLtoArticle($url, $tld = '.com'){
	//get article html and pull out story
	$article = '';
	$source = URLtoSource($url, $tld = '.com');

	//special cases for articles
	if($source == 'nytimes'){
		$html = get_fcontent($url);
		$doc = new DOMDocument();
		@$doc->loadHTML($html);
		$finder = new DomXPath($doc);
		
		//for opinion section
		$classname="css-imjp5j e2kc3sl0";
		$nodes = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");
		for($x=0; $x < $nodes->length; $x++){
			$article .= $nodes->item($x)->nodeValue;
		}
		
		//for news
		$classname2="story-body-text story-content";
		$nodes2 = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname2 ')]");
		for($x=0; $x < $nodes2->length; $x++){
			$article .= $nodes2->item($x)->nodeValue;
		}

	}


	if($source == 'foxnews'){
		$html = get_fcontent($url);
		$doc = new DOMDocument();
		@$doc->loadHTML($html);
		$p=$doc->getElementsByTagName('p');
		$article = '';

		for($x=0; $x < $p->length-3; $x++){
			$article .= $p->item($x)->nodeValue;
		}
	}

	if($source == 'washingtonpost'){
		$html = get_fcontent($url);
		$doc = new DOMDocument();
		@$doc->loadHTML($html);
		$p=$doc->getElementsByTagName('p');
		$article = '';
		for($x=0; $x < $p->length; $x++){
			$article .= $p->item($x)->nodeValue;
		}

		$startString = '';
		$endString = 'We are a participant in the Amazon Services LLC Associates Program';
		//for non opinion
		//"We’re interested in your feedback"

		$pos = strpos($article, $startString) + strlen($startString);
		if($pos>0){
			$article = substr($article, $pos);
		}
		$pos2 = strripos($article, $endString);
		if($pos2>0){
			$article = substr($article, 0, $pos2);
		}
	}

	if($source == 'nbcnews'){
		$html = get_fcontent($url);
		$doc = new DOMDocument();
		@$doc->loadHTML($html);
		$p=$doc->getElementsByTagName('p');
		$article = '';
		for($x=0; $x < $p->length; $x++){
			$article .= $p->item($x)->nodeValue;
		}

		$endString = 'We appreciate your help making nbcnews.com a better place';
		//for non opinion
		//"We’re interested in your feedback"

		$pos2 = strripos($article, $endString);
		if($pos2>0){
			$article = substr($article, 0, $pos2);
		}
	}

	if(strlen($article)<1){
		$html = get_fcontent($url);
		$doc = new DOMDocument();
		@$doc->loadHTML($html);
		$p=$doc->getElementsByTagName('p');
		$article = '';
		for($x=0; $x < $p->length; $x++){
			$article .= $p->item($x)->nodeValue;
		}
	}

	return $article;
}

function URLtoSource($url, $tld='.com'){
	$posDotCom = strpos($url, $tld);
	$posBackSlash = strpos($url, '//');
	$posWWW = strpos($url, 'www.');

	if(($posWWW>0)&&($posWWW<10)){
		$source = substr($url,$posWWW+4,$posDotCom-$posWWW-4);
	}
	else{
		$source = substr($url,$posBackSlash+2,$posDotCom-$posBackSlash-2);
	}

	return $source;
}

function analyzeArticle($article, $url, $SQLtable){
	
	$source = URLtoSource($url, $tld='.com');

	global $usernameWatson, $passwordWatson;
	$analyzer = new ToneAnalyzer( WatsonCredential::initWithCredentials($usernameWatson, $passwordWatson) );

	//set up model for Watson
	$model = new ToneModel();
	$model->setText($article);
	$result = $analyzer->getTone($model);
	//for testing
	//$result = '{"document_tone":{"tone_categories":[{"tones":[{"score":0.0,"tone_id":"anger","tone_name":"Anger"},{"score":0.0,"tone_id":"disgust","tone_name":"Disgust"},{"score":0.0,"tone_id":"fear","tone_name":"Fear"},{"score":0.0,"tone_id":"joy","tone_name":"Joy"},{"score":0.0,"tone_id":"sadness","tone_name":"Sadness"}],"category_id":"emotion_tone","category_name":"Emotion Tone"},{"tones":[{"score":0.0,"tone_id":"analytical","tone_name":"Analytical"},{"score":0.0,"tone_id":"confident","tone_name":"Confident"},{"score":0.0,"tone_id":"tentative","tone_name":"Tentative"}],"category_id":"language_tone","category_name":"Language Tone"},{"tones":[{"score":0.301046,"tone_id":"openness_big5","tone_name":"Openness"},{"score":0.331715,"tone_id":"conscientiousness_big5","tone_name":"Conscientiousness"},{"score":0.6058,"tone_id":"extraversion_big5","tone_name":"Extraversion"},{"score":0.636361,"tone_id":"agreeableness_big5","tone_name":"Agreeableness"},{"score":0.495296,"tone_id":"emotional_range_big5","tone_name":"Emotional Range"}],"category_id":"social_tone","category_name":"Social Tone"}]}}';

	$data = json_decode($result, true);

	$dataArray;
	for($x=0; $x< count ($data['document_tone']['tone_categories']); $x++){
		for($y=0; $y< count ($data['document_tone']['tone_categories'][$x]['tones']); $y++){
			$dataArray[$data['document_tone']['tone_categories'][$x]['tones'][$y]['tone_id']]=$data['document_tone']['tone_categories'][$x]['tones'][$y]['score'];
			echo $data['document_tone']['tone_categories'][$x]['tones'][$y]['tone_id'].": ". $data['document_tone']['tone_categories'][$x]['tones'][$y]['score'];
			echo '<br>';
		}	
	}
	$dataArray['news source'] = $source;
	$dataArray['url'] = $url;
	$dataArray['story'] = $article;
	mysqli_insert($SQLtable, $dataArray);
}


function mysqli_insert($table, $assoc) {
	//sql setup
	$hostname = "localhost";
	$username = "username";
	$password = "password";
	$db = "projectNews";
	$mysqli = new mysqli($hostname,$username,$password,$db);
    
	foreach ($assoc as $column => $value) {
        $cols[] = $column;
        $vals[] = mysqli_real_escape_string($mysqli, $value);
    }
	
    $colnames = "`".implode("`, `", $cols)."`";
    $colvals = "'".implode("', '", $vals)."'";

	$mysql = mysqli_query($mysqli, "INSERT INTO $table ($colnames) VALUES ($colvals)");// or die('Database Connection Error ('.mysqli_errno($mysqli).') '.mysqli_error($mysqli). " on query: INSERT INTO $table ($colnames) VALUES ($colvals)");

	mysqli_close($mysqli);
    if ($mysql)
        return TRUE;
    else return FALSE;
}


function get_fcontent( $url,  $javascript_loop = 0, $timeout = 5 ) {
    $url = str_replace( "&amp;", "&", urldecode(trim($url)) );

    $cookie = tempnam ("/tmp", "CURLCOOKIE");
    $ch = curl_init();
    curl_setopt( $ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1" );
    curl_setopt( $ch, CURLOPT_URL, $url );
    curl_setopt( $ch, CURLOPT_COOKIEJAR, $cookie );
    curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
    curl_setopt( $ch, CURLOPT_ENCODING, "" );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
    curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );    # required for https urls
    curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, $timeout );
    curl_setopt( $ch, CURLOPT_TIMEOUT, $timeout );
    curl_setopt( $ch, CURLOPT_MAXREDIRS, 10 );
    $content = curl_exec( $ch );
    $response = curl_getinfo( $ch );
    curl_close ( $ch );

    if ($response['http_code'] == 301 || $response['http_code'] == 302) {
        ini_set("user_agent", "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1");

        if ( $headers = get_headers($response['url']) ) {
            foreach( $headers as $value ) {
                if ( substr( strtolower($value), 0, 9 ) == "location:" )
                    return get_url( trim( substr( $value, 9, strlen($value) ) ) );
            }
        }
    }

    if (    ( preg_match("/>[[:space:]]+window\.location\.replace\('(.*)'\)/i", $content, $value) || preg_match("/>[[:space:]]+window\.location\=\"(.*)\"/i", $content, $value) ) && $javascript_loop < 5) {
        return get_url( $value[1], $javascript_loop+1 );
    } else {
        return $content;
    }
}
