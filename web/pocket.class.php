<?php

class Pocket {
	private $consumerKey = '';
	private $accessToken = '';
	private $simulate = true;

	function untagPost($id,$tag) {
		$endpoint = 'https://getpocket.com/v3/send';
		$vars = array();
		$vars['consumer_key'] = $this->consumer_key;
		$vars['access_token'] = $this->access_token;
		$vars['actions'] = array();
		$action1 = array();
		$action1['action'] = 'tags_remove';
		$action1['item_id'] = $id;
		$action1['tags'] = $tag;
		$action2 = array();
		$action2['action'] = 'tags_add';
		$action2['item_id'] = $id;
		$action2['tags'] = $tag.'-posted';
		$vars['actions'][] = $action1;
		$vars['actions'][] = $action2;
		if ($this->simulate) {
			return $vars;
		}
		return $this->postSomething($endpoint,$vars);
	}

	public function getAPost($tag='sg-slack-general') {
		$endpoint = 'https://getpocket.com/v3/get';
		$vars = array();
		$vars['consumer_key'] = $this->consumerKey;
		$vars['access_token'] = $this->accessToken;
		$vars['tag'] = $tag;
		$vars['count'] = '1';
		$vars['sort'] = 'newest';
		$vars['detailType'] = 'simple';
		$response = $this->postSomething($endpoint,$vars);
		return $response;
	}

	function __construct($consumerKey,$accessToken,$action='',$simulate=true) {
		$this->consumerKey = $consumerKey;
		$this->accessToken = $accessToken;
		$this->simulate = $simulate;
		if ($action=='authorized') {
			echo ("back from Pocket ... ");
			$code=$_GET['code'];
			$endpoint = 'https://getpocket.com/v3/oauth/authorize';
			$vars = array();
			$vars['consumer_key'] = $this->consumerKey;
			$vars['code'] = $code;
			$response = $this->postSomething($endpoint,$vars,true);
			echo "put this access token in the env vars";
			r($response);
		} elseif($action=='pocket-auth') {
			// Connect to Pocket and get a token
			$vars = array();
			$vars['consumer_key'] = $this->consumerKey;
			$vars['redirect_uri'] = 'http://'.$_SERVER['HTTP_HOST'].'/pocket-authorized.php';
			$endpoint = 'https://getpocket.com/v3/oauth/request';
			$response = $this->postSomething($endpoint,$vars);
			//r($response);
			$code = $response->code;
			$redirectUri = htmlentities($vars['redirect_uri'].'?code='.$code);
			$newUrl = 'https://getpocket.com/auth/authorize?request_token='.$code.'&redirect_uri='.$redirectUri;
			header('Location: '.$newUrl);
		}
		// } elseif ($action=='get') {
		// 	$endpoint = 'https://getpocket.com/v3/get';
		// 	$vars = array();
		// 	$vars['tag'] = 'sg-slack';
		// 	$vars['count'] = '5';
		// 	$vars['sort'] = 'newest';
		// 	$vars['detailType'] = 'simple';
		// 	$response = $this->post_something($endpoint,$vars);
		// 	$this->r($response);
		// }
	}

	private function postSomething($url,$vars) {
		$headers = array();
		$headers['Content-Type'] = 'application/json; charset=UTF8';
		$headers['X-Accept'] = 'application/json';
		$realHeaders = array();
		foreach($headers as $k=>$v){
			$realHeaders[] = $k.": ".$v;
		}

		$postData = json_encode($vars);

		// foreach($vars as $key=>$value) { $post_data .= $key.'='.$value.'&'; }
		// $post_data = rtrim($post_data, '&');

	  $options = array(
			CURLOPT_RETURNTRANSFER => true,     // return web page
			// CURLOPT_HEADER         => true,
			CURLOPT_FOLLOWLOCATION => true,     // follow redirects
			CURLOPT_ENCODING       => "UTF8",       //
			CURLOPT_USERAGENT      => "Slack Pack", // who am i
			CURLOPT_AUTOREFERER    => true,     // set referer on redirect
			CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
			CURLOPT_TIMEOUT        => 120,      // timeout on response
			CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
			CURLOPT_POST 		   	   => 1,
			CURLOPT_POSTFIELDS     => $postData,
			CURLOPT_HTTPHEADER	   => $realHeaders,
			CURLINFO_HEADER_OUT	   => true,
	  );

	  $curlHandle = curl_init($url);
	  curl_setopt_array($curlHandle, $options);
		$response = curl_exec($curlHandle);

		if (curl_error($curlHandle)) {
		  echo 'error:' . curl_error($curlHandle);
		}

		// if ($debug) {
		// 	$this->r($response,"response");
		// 	$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		// 	// $headersSent = curl_getinfo($ch, CURLINFO_HEADER_OUT );
		// 	// $headersSent = str_replace("\n", "<br>", $headersSent);
		// 	echo "result=".$result."<br>";
		// 	echo "http=".$http_code."<br>";
		// 	// echo "headersSent=".$headersSent."<br>";
		// }
		curl_close($curlHandle);
		$response = json_decode($response);
		return $response;
	}

} // Pocket

?>
