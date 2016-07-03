<?php

class Pocket {
	private $endpoint = 'https://getpocket.com/v3/oauth/request';
	private $request_uri = 'pocketapp1234:authorizationFinished';
	private $consumer_key = '55686-b8c05db7247a1eb28a88120b';
	private $token = '';
	private $headers;

	function __construct() {
		// Connect to Pocket and get a token
		$vars = array();
		$vars['consumer_key'] = $this->consumer_key;
		$vars['request_uri'] = $this->request_uri;
		$endpoint = $this->endpoint;
		$token = $this->post_something($endpoint,$vars);
		echo $token;
	}

	private function post_something($url,$vars) {
		$headers = array();
		$headers['Content-Type'] = 'application/json; charset=UTF8';
		// $headers['X-Accept'] = 'application/json';
		$headers['X-Accept'] = 'application/x-www-form-urlencoded';
		$_headers = array();
		foreach($headers as $k=>$v){
			$_headers[] = $k.": ".$v;
		}
		$this->r($vars);

		$post_data = json_encode($vars);

		// foreach($vars as $key=>$value) { $post_data .= $key.'='.$value.'&'; }
		// $post_data = rtrim($post_data, '&');

		$this->r($post_data);

	    $options = array(
	        CURLOPT_RETURNTRANSFER => true,     // return web page
	        CURLOPT_HEADER         => false,    // don't return headers
	        CURLOPT_FOLLOWLOCATION => true,     // follow redirects
	        CURLOPT_ENCODING       => "UTF8",       // 
	        CURLOPT_USERAGENT      => "Slack Pack", // who am i
	        CURLOPT_AUTOREFERER    => true,     // set referer on redirect
	        CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
	        CURLOPT_TIMEOUT        => 120,      // timeout on response
	        CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
	        CURLOPT_POST 		   => 1,
	        CURLOPT_POSTFIELDS     => $post_data,
			CURLOPT_HTTPHEADER	   => $_headers,
			CURLINFO_HEADER_OUT	   => true,
	    );

	    $ch = curl_init( $url );
	    curl_setopt_array( $ch, $options );
		$result = curl_exec($ch);

		if(curl_error($ch)) {
		    echo 'error:' . curl_error($ch);
		}

		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		$headersSent = curl_getinfo($ch, CURLINFO_HEADER_OUT );
		$headersSent = str_replace("\n", "<br>", $headersSent);

		echo "result=".$result."<br>";
		echo "http=".$http_code."<br>";
		echo "headersSent=".$headersSent."<br>";

		curl_close($ch);

	}

	private function r($a) {
		echo "<pre>".print_r($a,TRUE)."</pre>";
	}

function doRESTCALL($url, $method, $data) {
	ob_start();
	$ch = curl_init();
	$headers = (function_exists('getallheaders'))?getallheaders(): array();
	$_headers = array();
	foreach($headers as $k=>$v){
		$_headers[strtolower($k)] = $v;
	}

	// set URL and other appropriate options
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt ($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $_headers);
	curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
	curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0 );
	$post_data = 'method=' . $method . '&input_type=json&response_type=json';
	$json = getJSONObj();
	$jsonEncodedData = $json->encode($data, false);
	$post_data = $post_data . "&rest_data=" . $jsonEncodedData;
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	$result = curl_exec($ch);
	curl_close($ch);

	if (true) {
		$result = explode("\r\n\r\n", $result, 2);
		print_r($result[1]);
		$response_data = $json->decode($result[1]);
	} else {
		
	}
	ob_end_flush();
	//print_r($response_data);
	return $response_data;
}


	private function get_web_page($url) {
	    $options = array(
	        CURLOPT_RETURNTRANSFER => true,     // return web page
	        CURLOPT_HEADER         => false,    // don't return headers
	        CURLOPT_FOLLOWLOCATION => true,     // follow redirects
	        CURLOPT_ENCODING       => "",       // handle all encodings
	        CURLOPT_USERAGENT      => "spider", // who am i
	        CURLOPT_AUTOREFERER    => true,     // set referer on redirect
	        CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
	        CURLOPT_TIMEOUT        => 120,      // timeout on response
	        CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
	    );
	    $ch      = curl_init( $url );
	    curl_setopt_array( $ch, $options );
	    $content = curl_exec( $ch );
	    $err     = curl_errno( $ch );
	    $errmsg  = curl_error( $ch );
	    $header  = curl_getinfo( $ch );
	    curl_close( $ch );
	    $header['errno']   = $err;
	    $header['errmsg']  = $errmsg;
	    $header['content'] = $content;
	    return $header;
	}

} // Pocket

?>